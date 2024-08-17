<?php
namespace App\Providers;

use Illuminate\Broadcasting\Broadcasters\RedisBroadcaster;
use \Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Support\Arr;
use Predis\Connection\ConnectionException;
use RedisException;
use SodiumException;

/**
 * EncryptedRedisBroadcaster extends the default RedisBroadcaster to support encrypted channels.
 *
 * This broadcaster generates a shared secret based on the user ID and channel name.
 * The payload is encrypted using the shared secret and then transmitted over Redis.
 *
 * Requires the Sodium PHP extension enabled.
 *
 * Usage:
 * - Update the `REDIS_ENCRYPTION_MASTER_KEY_BASE64` in the .env file.
 * - Enable the Sodium extension in your php.ini file (`extension=sodium`).
 */
class EncryptedRedisBroadcaster extends RedisBroadcaster {

    protected $masterKey;

    /**
     * Create a new broadcaster instance.
     *
     * @param  \Illuminate\Contracts\Redis\Factory  $redis
     * @param  string|null  $connection
     * @param  string  $prefix
     * @param  string|null  $masterKey
     * @return void
     */
    public function __construct(Redis $redis, $connection = null, $prefix = '', $masterKey = null)
    {
        parent::__construct($redis, $connection, $prefix);
        $this->masterKey = $masterKey;
    }

    /**
     * Determine if the channel is an encrypted channel.
     *
     * @param string $channelName
     * @return bool
     */
    protected function isEncryptedChannel($channelName)
    {
        return strpos($channelName, 'private-encrypted') === 0;
    }

    /**
     * Generate a shared secret for the given channel.
     *
     * @param string $channel
     * @return string
     */
    protected function generateSharedSecret(string $channel, $userId)
    {
        return base64_encode(hash_hmac('sha256', "$userId:$channel:" . time(), $this->masterKey, true));
    }

    /**
     * Store the shared secret for the given channel.
     *
     * @param string $channel
     * @param string $sharedSecret
     */
    protected function storeSharedSecret($channel, $sharedSecret)
    {
        $this->redis->connection($this->connection)->set("shared_secret:{$channel}", $sharedSecret);
    }

    /**
     * Retrieve the shared secret for the given channel.
     *
     * @param string $channel
     * @return string
     */
    protected function retrieveSharedSecret($channel)
    {
        $sharedSecret = $this->redis->connection($this->connection)->get("shared_secret:{$channel}");
        return $sharedSecret ? base64_decode($sharedSecret) : null;
    }

    /**
     * Encrypt the payload.
     *
     * @param array $payloadData
     *
     * @return array
     *
     * @throws SodiumException
     */
    protected function encryptPayload(array $payloadData, string $sharedSecret)
    {
        try {
            $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
            $cipherText = sodium_crypto_secretbox(
                json_encode($payloadData),
                $nonce,
                $sharedSecret
            );

            return [
                'ciphertext' => base64_encode($cipherText),
                'nonce' => base64_encode($nonce),
            ];
        } catch (SodiumException $e) {
            throw new BroadcastException('Encryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $result
     * @return string
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        $user = $this->retrieveUser($request, $channelName);

        $broadcastIdentifier = method_exists($user, 'getAuthIdentifierForBroadcasting')
                        ? $user->getAuthIdentifierForBroadcasting()
                        : $user->getAuthIdentifier();


        $validateResult = [
            'channel_data' => [
                'user_id' => $broadcastIdentifier,
                'user_info' => $result,
            ],
        ];

        if ($this->isEncryptedChannel($request->channel_name)) {
            $sharedSecret = $this->generateSharedSecret($broadcastIdentifier, $request->channel_name);

            $this->storeSharedSecret($request->channel_name, $sharedSecret);

            $validateResult['shared_secret'] = $sharedSecret;
        }

        return json_encode($validateResult);
    }

    /**
     * Broadcast the given event.
     *
     * @param  array  $channels
     * @param  string  $event
     * @param  array  $payload
     * @return void
     *
     * @throws \Illuminate\Broadcasting\BroadcastException
     */
    public function broadcast(array $channels, $event, array $data = [])
    {
        if (empty($channels)) return;

        $connection = $this->redis->connection($this->connection);

        $payload = [
            'event' => $event,
            'data' => $data,
            'socket' => Arr::pull($data, 'socket'),
        ];

        try {
            $normalChannels = [];
            foreach ($channels as $channel)
                if ($this->isEncryptedChannel($channel)) {
                    $channelPayload = $payload;
                    $sharedSecret = $this->retrieveSharedSecret($channel);
                    $channelPayload['data'] = $this->encryptPayload($channelPayload['data'], $sharedSecret);
                    $connection->eval(
                        $this->broadcastMultipleChannelsScript(),
                        0, json_encode($channelPayload), ...$this->formatChannels([$channel])
                    );
                } else
                    $normalChannels[] = $channel;

            $connection->eval(
                $this->broadcastMultipleChannelsScript(),
                0, json_encode($payload), ...$this->formatChannels($normalChannels)
            );
        } catch (ConnectionException|RedisException $e) {
            throw new BroadcastException(
                sprintf('Redis error: %s.', $e->getMessage())
            );
        }
    }
}

# Laravel Echo Server (Socket.IO) ^0.0.1

## Versions

| Package                     | Version   | URL Source                                                             |
| --------------------------- | --------- | ---------------------------------------------------------------------- |
| laravel                     | 10.x      | [laravel](https://laravel.com/docs/10.x)                               |
| node.js                     | 20.5.1    | [node.js](https://nodejs.org/en/download)                              |
| fixed-laravel-echo-server   | 0.0.2     | [npm](https://www.npmjs.com/package/@abdopr/fixed-laravel-echo-server) |
| redis-server                | 7.2.1     | [redis](https://redis.io/docs/getting-started)                         |
| laravel_echo_null (flutter) | 0.0.5+4   | [pub.dev](https://pub.dev/packages/laravel_echo_null)                  |
| storage_database (flutter)  | 0.0.9+4   | [pub.dev](https://pub.dev/packages/storage_database)                   |

## Configure Steps:

* install redis server [redis](https://redis.io/docs/getting-started).
* run redis server (read redis documentation for more details).
* Install Fixed Laravel Echo Server ```npm i -g @abdopr/fixed-laravel-echo-server```.
* init laravel echo server ```fixed-laravel-echo-server init``` (Note: verify ports)
* start laravel echo server ```fixed-laravel-echo-server start```
* create your user model&migration.
* configure sanctum auth middleware ([sanctum](https://laravel.com/docs/10.x/sanctum)) or any other middleware.
* configure broadcasting (read [broadcasting](https://laravel.com/docs/10.x/broadcasting) for more information).
* edit broadcasting auth middleware and set it to your middleware you use like ```Broadcast::routes(['middleware' => 'auth:sanctum']);```.
* create new user for test.
* login user and get token for test.
* try to connect the laravel echo client, this some examples:
    * [flutter using laravel_echo_null](https://github.com/AbdoPrDZ/laravel_echo_null/tree/main/example).
    * [flutter using storage_database](https://github.com/AbdoPrDZ/storage_database/tree/main/example).
    * [js using laravel_echo](https://github.com/AbdoPrDZ/laravel_echo_socket_server/tree/main/resources/js/bootstrap.js).
* create your events and test it.

## Note: You can message me if there any problem.

# Contact Us

GitHub Profile: <https://github.com/AbdoPrDZ>

WhatsApp + Telegram (+213778185797)

Facebook Account: <https://www.facebook.com/profile.php?id=100008024286034>

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Events\Message\MessageCreatedEvent;
use App\Events\Message\MessageUpdatedEvent;
use App\Events\Message\MessageDeletedEvent;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
    ];

    protected $dispatchesEvents = [
        'created' => MessageCreatedEvent::class,
        'updated' => MessageUpdatedEvent::class,
        'deleted' => MessageDeletedEvent::class,
    ];
}

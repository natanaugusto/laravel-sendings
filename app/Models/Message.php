<?php

namespace App\Models;

use App\Traits\Models\Fileable;
use App\Traits\Models\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, Fileable, BelongsToUser;

    protected $fillable = ['user_id', 'subject', 'body'];

    public static function getStorageDisk(): string
    {
        return 'message';
    }


    public static function getQueueConnection(): string
    {
        return 'message';
    }
}

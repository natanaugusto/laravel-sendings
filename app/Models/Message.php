<?php

namespace App\Models;

use App\Models\Contracts\FileableInterface;
use App\Models\Contracts\QueuelableInterface;
use App\Models\Traits\Fileable;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model implements FileableInterface, QueuelableInterface
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

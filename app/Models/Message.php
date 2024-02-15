<?php

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends FileableBaseModel
{
    use HasFactory, BelongsToUser;

    protected $fillable = ['user_id', 'subject', 'body'];
}

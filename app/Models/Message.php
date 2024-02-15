<?php

namespace App\Models;

use App\Models\Traits\HasFile;
use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, BelongsToUser, HasFile;

    protected $fillable = ['user_id', 'subject', 'body'];
}

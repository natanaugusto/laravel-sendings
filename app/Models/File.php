<?php

namespace App\Models;

use App\Models\Traits\BelongsToUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class File extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = ['user_id', 'path', 'size'];

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}

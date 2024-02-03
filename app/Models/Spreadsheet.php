<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Spreadsheet extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'path', 'rows'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
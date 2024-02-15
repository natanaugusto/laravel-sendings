<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

abstract class FileableBaseModel extends Model
{
    const STORAGE_DISK = 'local';
    const QUEUE_CONNECTION = 'default';

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}

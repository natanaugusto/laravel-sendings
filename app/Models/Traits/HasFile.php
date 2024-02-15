<?php

namespace App\Models\Traits;

use App\Models\File;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasFile
{
    public  function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }
}

<?php

namespace App\Models\Contracts;

use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface FileableInterface
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     * @return \App\Models\File;
     */
    public function file(): MorphOne;

    public function files(): MorphMany;

    public function storeFile(UploadedFile $file): string;

    public function storeFiles(array $files): array;

    public static function generateFilename(UploadedFile $file): string;

    public static function getStorageDisk(): string;
}

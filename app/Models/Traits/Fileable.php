<?php

namespace App\Models\Traits;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Fileable
{
    protected static $GENERATED_FILENAME;

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function storeFile(UploadedFile $file): string
    {
        $fileName = self::generateFilename($file);
        $file->storeAs(
            '',
            $fileName,
            static::getStorageDisk()
        );
        $this->file()->create([
            'user_id' => $this->user_id,
            'path' => Storage::disk(static::getStorageDisk())->path($fileName),
            'size' => Storage::disk(static::getStorageDisk())->size($fileName),
        ]);
        return $fileName;
    }

    public function storeFiles(array $files): array
    {
        $stored = [];
        foreach ($files as $file) {
            array_push($stored, $this->storeFile($file));
        }
        return $stored;
    }

    public static function generateFilename(UploadedFile $file): string
    {
        if (empty(self::$GENERATED_FILENAME)) {
            self::$GENERATED_FILENAME = now()->format('YmdHi') . "_{$file->getClientOriginalName()}";
        }
        return self::$GENERATED_FILENAME;
    }

    abstract public static function getStorageDisk(): string;
}

<?php

namespace App\Models;

// use Illuminate\Http\File as UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

abstract class FileableBaseModel extends Model
{
    protected static $GENERATED_FILENAME;

    public function file(): MorphOne
    {
        return $this->morphOne(File::class, 'fileable');
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

    public static function generateFilename(UploadedFile $file): string
    {
        if (empty(self::$GENERATED_FILENAME)) {
            self::$GENERATED_FILENAME = now()->format('YmdHi') . "_{$file->getClientOriginalName()}";
        }
        return self::$GENERATED_FILENAME;
    }

    abstract public static function getStorageDisk(): string;
    abstract public static function getQueueConnection(): string;
}

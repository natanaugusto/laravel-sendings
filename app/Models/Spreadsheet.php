<?php

namespace App\Models;

use App\Enums\IncreaseType;
use App\Models\Traits\Fileable;
use App\Models\Traits\BelongsToUser;
use App\Models\Contracts\FileableInterface;
use App\Models\Contracts\QueuelableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Spreadsheet extends Model implements FileableInterface, QueuelableInterface
{
    use HasFactory, BelongsToUser, Fileable;

    protected $fillable = ['user_id', 'name'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function increase(IncreaseType $type = IncreaseType::IMPORTED, int $count = 1): void
    {
        if ($this->isDirty()) {
            $this->save();
        }
        $this->refresh();
        $this->attributes[$type->value] = $this->attributes[$type->value] + $count;
        $this->save();
    }

    public static function getStorageDisk(): string
    {
        return 'spreadsheet';
    }

    public static function getQueueConnection(): string
    {
        return 'spreadsheet';
    }

    public function afterStore(string $filename): void
    {
        $path = Storage::disk(static::getStorageDisk())->path($filename);
        $this->rows = file_exists($path) ?
            IOFactory::load($path)->getActiveSheet()->getHighestRow() : 0;
        $this->save();
    }

    protected static function boot(): void
    {
        parent::boot();
        static::created(fn ($model) => $model->afterStore($model->name));
    }
}

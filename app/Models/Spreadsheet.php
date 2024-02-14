<?php

namespace App\Models;

use App\Enums\IncreaseType;
use App\Models\Traits\BelongsToUser;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Spreadsheet extends Model
{
    use HasFactory, BelongsToUser;

    protected $fillable = ['user_id', 'path'];

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

    protected static function boot(): void
    {
        parent::boot();
        static::created(fn ($model) => self::fillRowsFromSpreadsheet($model));
    }

    protected static function fillRowsFromSpreadsheet(self $model): void
    {
        $path = Storage::path($model->path);
        $model->rows = file_exists($path) ?
            IOFactory::load($path)->getActiveSheet()->getHighestRow() : 0;
        $model->save();
    }
}

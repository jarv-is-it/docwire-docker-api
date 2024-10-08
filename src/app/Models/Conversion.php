<?php

namespace App\Models;

use App\Enums\ConversionOutput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Conversion extends Model
{
    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'filename' => '',
        'extension' => '',
        'path' => '',
        'identifier' => null,
        'webhook' => null,
        'output' => ConversionOutput::PlainText,
        'language' => 'eng',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'extension',
        'path',
        'identifier',
        'webhook',
        'output',
        'language',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'output' => ConversionOutput::class,
    ];

    public function getContentAttribute()
    {
        return Storage::get($this->path);
    }

    public static function booted()
    {
        static::deleting(function (Conversion $conversion) {
            Storage::delete($conversion->path);
        });
    }
}

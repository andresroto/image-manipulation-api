<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageManipulation extends Model
{
    use HasFactory;

    const TYPE_RESIZE = 'resize';

    const UPDATED_AT = null;

    protected $fillable = [
        'name',
        'path',
        'type',
        'data',
        'output_path',
        'user_id',
        'album_id'
    ];

    /**
     * @return BelongsTo
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}

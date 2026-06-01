<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeImage extends Model
{
    protected $fillable = [
        'office_id',
        'image',
    ];

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }
}

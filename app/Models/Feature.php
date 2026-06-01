<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Feature extends Model
{
    protected $fillable = [
        'name',
        'icon',
    ];

    public function offices(): BelongsToMany
    {
        return $this->belongsToMany(Office::class, 'office_features');
    }
}

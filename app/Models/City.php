<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'slug', 'image'];

    protected $appends = ['officeCount'];

    public function getOfficeCountAttribute()
    {
        return $this->offices_count ?? $this->offices()->count();
    }

    public function offices(): HasMany
    {
        return $this->hasMany(Office::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Office extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'city_id',
        'provider_id',
        'name',
        'slug',
        'thumbnail',
        'about',
        'address',
        'price',
        'duration_type',
        'is_open',
        'is_full_booked',
        'rating',
        'sales_contacts',
    ];

    protected function casts(): array
    {
        return [
            'sales_contacts' => 'array',
            'is_open' => 'boolean',
            'is_full_booked' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(OfficeImage::class);
    }

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'office_features');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class);
    }
}

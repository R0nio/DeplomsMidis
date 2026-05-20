<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'short_description',
        'full_description',
        'category',
        'status',
        'total_investment',
        'collected_total_investment',
        'number_date_realise',
        'ownership',
        'activity',
        'type_build',
        'count_new_job',
        'latitude',
        'longitude',
        'address',
        'is_moderated',
    ];

    protected $casts = [
        'category' => 'array',
        'is_moderated' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    // Связи
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Project_investment::class);
    }

    public function forecasts(): HasMany
    {
        return $this->hasMany(Project_forecast::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Project_photos::class)->orderBy('order');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorite', 'project_id', 'user_id')
            ->withTimestamps();
    }

    public function isFavoritedBy(?int $userId): bool
    {
        if (!$userId) return false;
        
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }
}

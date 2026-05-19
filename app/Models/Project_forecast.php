<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project_forecast extends Model
{
    use HasFactory;

    protected $table = 'project_forecasts';

    protected $fillable = [
        'project_id',
        'year',
        'expected_revenue',
        'expected_expenses',
    ];

    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'expected_expenses' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}

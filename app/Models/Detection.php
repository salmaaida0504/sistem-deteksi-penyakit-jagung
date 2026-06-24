<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    protected $fillable = [
        'id_opt',
        'image_path',
        'predicted_class',
        'confidence',
        'all_predictions',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'all_predictions' => 'array',
        ];
    }

    public function opt(): BelongsTo
    {
        return $this->belongsTo(Opt::class, 'id_opt', 'id_opt');
    }
}

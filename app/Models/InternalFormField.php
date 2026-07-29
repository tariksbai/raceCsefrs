<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalFormField extends Model
{
    use HasFactory;

    protected $table = 'internal_form_fields';

    protected $fillable = [
        'form_id',
        'type',
        'label',
        'placeholder',
        'required',
        'options',
        'order',
        'validation_rules',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'boolean',
            'options' => 'array',
            'validation_rules' => 'array',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(InternalForm::class, 'form_id');
    }

    public function values(): HasMany
    {
        return $this->hasMany(InternalFormResponseValue::class, 'field_id');
    }
}

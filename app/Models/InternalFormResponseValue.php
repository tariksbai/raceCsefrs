<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternalFormResponseValue extends Model
{
    use HasFactory;

    protected $table = 'internal_form_response_values';

    protected $fillable = [
        'response_id',
        'field_id',
        'value',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(InternalFormResponse::class, 'response_id');
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(InternalFormField::class, 'field_id');
    }
}

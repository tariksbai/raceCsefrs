<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InternalFormResponse extends Model
{
    use HasFactory;

    protected $table = 'internal_form_responses';

    protected $fillable = [
        'form_id',
        'user_id',
        'ip_address',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(InternalForm::class, 'form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(InternalFormResponseValue::class, 'response_id');
    }
}

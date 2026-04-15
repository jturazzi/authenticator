<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TotpAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'secret',
        'digits',
        'period',
        'algorithm',
        'locked',
        'description',
    ];

    protected $hidden = [
        'secret',
    ];

    protected function casts(): array
    {
        return [
            'locked' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getOtpauthUri(): string
    {
        $label = rawurlencode($this->name);

        $params = http_build_query([
            'secret' => $this->secret,
            'issuer' => $this->name,
            'algorithm' => strtoupper($this->algorithm),
            'digits' => $this->digits,
            'period' => $this->period,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }
}

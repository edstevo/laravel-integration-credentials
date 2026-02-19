<?php

namespace EdStevo\LaravelIntegrationCredentials\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $expires_at
 */
class IntegrationCredential extends Model
{
    protected $fillable = [
        'provider',        // e.g. shopify, stripe, github
        'key',             // e.g. access_token, api_key, shop_id
        'value',           // e.g. token, secret, account id
        'expires_at',      // nullable
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Sensitive fields should never be exposed.
     */
    protected $hidden = [
        'value',
    ];

    /**
     * Polymorphic parent.
     */
    public function integrable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Convenience helper.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }
}

<?php

namespace EdStevo\LaravelIntegrationCredentials\Models\Concerns;

use Carbon\Carbon;
use EdStevo\LaravelIntegrationCredentials\Models\IntegrationCredential;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @property IntegrationCredential[] $integrations
 */
trait MorphManyIntegrationCredentials
{
    public function integrationCredentials(): MorphMany
    {
        return $this->morphMany(IntegrationCredential::class, 'integrable');
    }

    /**
     * Fetch a credential record.
     */
    public function getIntegrationCredential(string $provider, string $key): ?IntegrationCredential
    {
        return $this->integrationCredentials()
            ->where('provider', $provider)
            ->where('key', $key)
            ->first();
    }

    public function getIntegrationCredentialValue(string $provider, string $key, bool $allowExpired = false): ?string
    {
        $credential = $this->getIntegrationCredential($provider, $key);

        if (! $credential) {
            return null;
        }

        if (! $allowExpired && $credential->isExpired()) {
            return null;
        }

        return $credential->value;
    }

    /**
     * Create or update a credential.
     */
    public function setIntegrationCredential(
        string $provider,
        string $key,
        string $value,
        ?Carbon $expiresAt = null
    ): IntegrationCredential {
        return $this->integrationCredentials()->updateOrCreate(
            [
                'provider' => $provider,
                'key' => $key,
            ],
            [
                'value' => $value,
                'expires_at' => $expiresAt,
            ]
        );
    }

    /**
     * Remove a specific credential.
     */
    public function forgetIntegrationCredential(string $provider, string $key): bool
    {
        return (bool) $this->integrationCredentials()
            ->where('provider', $provider)
            ->where('key', $key)
            ->delete();
    }

    /**
     * Remove all credentials for a provider.
     */
    public function forgetIntegrationProvider(string $provider): bool
    {
        return (bool) $this->integrationCredentials()
            ->where('provider', $provider)
            ->delete();
    }

    /**
     * Scope: only models that have a given integration credential.
     */
    public function scopeWhereHasIntegrationCredential(Builder $query, string $provider, string $key, bool $mustBeValid = true): Builder
    {
        return $query->whereHas('integrationCredentials', function (Builder $q) use ($provider, $key, $mustBeValid) {
            $q->where('provider', $provider)
                ->where('key', $key);

            if ($mustBeValid) {
                $q->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                });
            }
        });
    }
}

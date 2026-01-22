<?php

use Workbench\App\Models\TestIntegrationOwner;

it('can attach integration credentials via morphMany', function () {
    $owner = TestIntegrationOwner::create([
        'name' => 'Test App',
    ]);

    $credential = $owner->integrationCredentials()->create([
        'provider' => 'shopify',
        'key' => 'access_token',
        'value' => 'shpca_test_123',
    ]);

    expect($credential)->toBeInstanceOf(\EdStevo\LaravelIntegrationCredentials\Models\IntegrationCredential::class)
        ->and($credential->integrable)->toBeInstanceOf(TestIntegrationOwner::class)
        ->and($credential->provider)->toBe('shopify')
        ->and($credential->key)->toBe('access_token')
        ->and($credential->value)->toBe('shpca_test_123');
});

it('can set and retrieve an integration credential', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential(
        provider: 'shopify',
        key: 'access_token',
        value: 'shpca_test_456'
    );

    $credential = $owner->getIntegrationCredential('shopify', 'access_token');

    expect($credential)
        ->not->toBeNull()
        ->and($credential->value)->toBe('shpca_test_456');
});

it('can retrieve only the credential value', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential(
        'shopify',
        'shop_id',
        '123456'
    );

    $value = $owner->getIntegrationCredentialValue('shopify', 'shop_id');

    expect($value)->toBe('123456');
});

it('does not return expired credentials by default', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential(
        provider: 'shopify',
        key: 'access_token',
        value: 'expired_token',
        expiresAt: now()->subDay()
    );

    expect(
        $owner->getIntegrationCredentialValue('shopify', 'access_token')
    )->toBeNull();
});

it('can return expired credentials when explicitly allowed', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential(
        provider: 'shopify',
        key: 'access_token',
        value: 'expired_token',
        expiresAt: now()->subDay()
    );

    $value = $owner->getIntegrationCredentialValue(
        provider: 'shopify',
        key: 'access_token',
        allowExpired: true
    );

    expect($value)->toBe('expired_token');
});

it('can forget a single integration credential', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential('shopify', 'access_token', 'abc');

    expect($owner->forgetIntegrationCredential('shopify', 'access_token'))
        ->toBeTrue();

    expect(
        $owner->getIntegrationCredential('shopify', 'access_token')
    )->toBeNull();
});

it('can forget all credentials for a provider', function () {
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $owner->setIntegrationCredential('shopify', 'access_token', 'abc');
    $owner->setIntegrationCredential('shopify', 'shop_id', '123');
    $owner->setIntegrationCredential('stripe', 'secret_key', 'sk_test');

    expect($owner->forgetIntegrationProvider('shopify'))->toBeTrue();

    expect($owner->getIntegrationCredential('shopify', 'access_token'))->toBeNull()
        ->and($owner->getIntegrationCredential('shopify', 'shop_id'))->toBeNull()
        ->and($owner->getIntegrationCredential('stripe', 'secret_key'))->not->toBeNull();
});

it('can scope query via eloquent - expecting test model', function () {
    $testId = \Illuminate\Support\Str::random();

    $owner = TestIntegrationOwner::create(['name' => 'Test App']);
    $owner->setIntegrationCredential('shopify', 'id', $testId);

    $res = TestIntegrationOwner::whereHasIntegrationCredentialValue('shopify', 'id', $testId)->first();

    expect($res)->toBeInstanceOf(TestIntegrationOwner::class)
        ->and($res->id)->toBe($owner->id);
});

it('can scope query via eloquent - expecting null', function () {
    $testId = \Illuminate\Support\Str::random();
    $owner = TestIntegrationOwner::create(['name' => 'Test App']);

    $res = TestIntegrationOwner::whereHasIntegrationCredentialValue('shopify', 'id', $testId)->first();

    expect($res)->toBeNull();
});

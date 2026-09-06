<?php

use App\Models\User;
use App\Services\DirectorPinService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->director = User::factory()->create(['role' => 'director']);
});

it('stores approval_pin as a hash, never as plain text', function () {
    app(DirectorPinService::class)->setPin($this->director, '123456');

    $stored = $this->director->fresh()->approval_pin;

    expect($stored)->not->toBe('123456');
    expect(Hash::check('123456', $stored))->toBeTrue();
});

it('hides approval_pin from serialized output', function () {
    app(DirectorPinService::class)->setPin($this->director, '123456');

    expect($this->director->fresh()->toArray())->not->toHaveKey('approval_pin');
});

it('rejects setting a pin when director already has one', function () {
    app(DirectorPinService::class)->setPin($this->director, '111111');

    try {
        app(DirectorPinService::class)->setPin($this->director, '222222');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('pin');
    }

    expect(Hash::check('111111', $this->director->fresh()->approval_pin))->toBeTrue();
});

it('verifies a correct pin', function () {
    app(DirectorPinService::class)->setPin($this->director, '123456');

    app(DirectorPinService::class)->verifyPin($this->director, '123456');

    expect(true)->toBeTrue();
});

it('rejects verification with a wrong pin', function () {
    app(DirectorPinService::class)->setPin($this->director, '123456');

    try {
        app(DirectorPinService::class)->verifyPin($this->director, '999999');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('pin');
    }
});

it('rejects verification when director has no pin yet', function () {
    try {
        app(DirectorPinService::class)->verifyPin($this->director, '123456');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('pin');
    }
});

it('changes pin only with a correct current pin', function () {
    app(DirectorPinService::class)->setPin($this->director, '111111');

    try {
        app(DirectorPinService::class)->changePin($this->director, '999999', '222222');
        $this->fail('Expected ValidationException to be thrown.');
    } catch (ValidationException $e) {
        expect($e->errors())->toHaveKey('current_pin');
    }

    expect(Hash::check('111111', $this->director->fresh()->approval_pin))->toBeTrue();

    app(DirectorPinService::class)->changePin($this->director, '111111', '222222');

    expect(Hash::check('222222', $this->director->fresh()->approval_pin))->toBeTrue();
});
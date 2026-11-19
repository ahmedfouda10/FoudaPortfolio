<?php

declare(strict_types=1);

/*
 * Filament attaches the "MFA required" middleware when the panel routes are
 * registered, so the flag has to be set before the application boots.
 */
beforeAll(function (): void {
    $_ENV['ADMIN_REQUIRE_MFA'] = $_SERVER['ADMIN_REQUIRE_MFA'] = 'true';
    putenv('ADMIN_REQUIRE_MFA=true');
});

afterAll(function (): void {
    $_ENV['ADMIN_REQUIRE_MFA'] = $_SERVER['ADMIN_REQUIRE_MFA'] = 'false';
    putenv('ADMIN_REQUIRE_MFA=false');
});

it('sends the admin to set up app-based MFA before using the dashboard', function (): void {
    expect(config('portfolio.admin.require_mfa'))->toBeTrue();

    actingAsAdmin();

    $response = $this->get('/admin');

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toContain('multi-factor-authentication');
});

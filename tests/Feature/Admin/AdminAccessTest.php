<?php

declare(strict_types=1);

use App\Models\User;

it('redirects guests to the login page', function (): void {
    $this->get('/admin')->assertRedirect('/admin/login');
});

it('forbids users other than the configured admin', function (): void {
    config(['portfolio.admin.email' => ADMIN_EMAIL]);

    $this->actingAs(User::factory()->create(['email' => 'someone-else@example.com']))
        ->get('/admin')
        ->assertForbidden();
});

it('forbids everyone when no admin email is configured', function (): void {
    config(['portfolio.admin.email' => null]);

    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();
});

it('lets the configured admin in, whatever the email casing', function (): void {
    config(['portfolio.admin.email' => 'Owner@Example.com']);

    $this->actingAs(User::factory()->create(['email' => 'owner@example.com']))
        ->get('/admin')
        ->assertOk();
});

it('keeps the dashboard out of search engines and shared caches', function (): void {
    actingAsAdmin();

    $this->get('/admin')
        ->assertOk()
        ->assertHeader('X-Robots-Tag', 'noindex, nofollow')
        ->assertSee('<meta name="robots" content="noindex, nofollow">', escape: false);

    expect($this->get('/admin')->headers->get('Cache-Control'))->toContain('no-store')->toContain('private');

    $this->get('/admin/login')->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});

it('does not load avatars from third-party services', function (): void {
    actingAsAdmin();

    $this->get('/admin')
        ->assertOk()
        ->assertDontSee('ui-avatars.com')
        ->assertSee('data:image/svg+xml;base64,', escape: false);
});

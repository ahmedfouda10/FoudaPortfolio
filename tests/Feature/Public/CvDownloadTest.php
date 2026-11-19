<?php

declare(strict_types=1);

use App\Models\CvDownload;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('local'));

it('downloads the CV and records the download without storing the IP', function (): void {
    Storage::disk('local')->put('cv/resume.pdf', '%PDF-1.7 fake');
    SiteSetting::factory()->create(['full_name' => 'Ahmed Fouda', 'cv_file' => 'cv/resume.pdf']);

    $response = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.7'])
        ->withHeaders(['User-Agent' => 'RecruiterBrowser/1.0', 'Referer' => 'https://www.linkedin.com/'])
        ->get(route('cv.download'));

    $response->assertOk()->assertDownload('ahmed-fouda-cv.pdf');

    $download = CvDownload::query()->sole();

    expect($download->ip_hash)->toHaveLength(64)
        ->not->toContain('203.0.113.7')
        ->toBe(hash_hmac('sha256', '203.0.113.7', (string) config('app.key')))
        ->and($download->user_agent)->toBe('RecruiterBrowser/1.0')
        ->and($download->referer)->toBe('https://www.linkedin.com/');
});

it('returns 404 and records nothing when no CV has been uploaded', function (): void {
    SiteSetting::factory()->create(['cv_file' => null]);

    $this->get(route('cv.download'))->assertNotFound();

    expect(CvDownload::query()->count())->toBe(0);
});

it('returns 404 when the CV file is missing from disk', function (): void {
    SiteSetting::factory()->create(['cv_file' => 'cv/missing.pdf']);

    $this->get(route('cv.download'))->assertNotFound();
});

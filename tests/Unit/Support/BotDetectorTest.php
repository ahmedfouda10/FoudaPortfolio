<?php

declare(strict_types=1);

use App\Support\BotDetector;

it('recognises crawlers, previews and tools', function (?string $userAgent): void {
    expect((new BotDetector)->isBot($userAgent))->toBeTrue();
})->with([
    null,
    '',
    'Googlebot/2.1',
    'Mozilla/5.0 (compatible; bingbot/2.0)',
    'facebookexternalhit/1.1',
    'WhatsApp/2.23',
    'curl/8.5.0',
    'python-requests/2.31',
    'Mozilla/5.0 HeadlessChrome/140.0',
]);

it('lets regular browsers through', function (): void {
    expect((new BotDetector)->isBot('Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Safari/605.1.15'))->toBeFalse();
});

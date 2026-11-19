<?php

declare(strict_types=1);

namespace App\Support;

/**
 * A deliberately simple user-agent check, good enough to keep crawlers,
 * link previews and audit tools out of the project view counter.
 */
final class BotDetector
{
    private const string PATTERN = '/bot|crawl|spider|slurp|scrap|preview|fetch|monitor|lighthouse|headless|curl|wget|python|httpclient|okhttp|facebookexternalhit|embedly|whatsapp|telegram|discord|slack/i';

    public function isBot(?string $userAgent): bool
    {
        return $userAgent === null || trim($userAgent) === '' || preg_match(self::PATTERN, $userAgent) === 1;
    }
}

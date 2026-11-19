<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Project;
use App\Support\BotDetector;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

/**
 * Counts a project view at most once per visitor (session) per day, ignoring bots.
 *
 * The counter is incremented with a single UPDATE (no model events), so it
 * never invalidates the cached public pages.
 */
final readonly class RecordProjectViewAction
{
    private const string SESSION_KEY = 'viewed_projects';

    public function __construct(private BotDetector $botDetector) {}

    /**
     * @return bool Whether the view was counted.
     */
    public function __invoke(Project $project, Request $request): bool
    {
        if ($this->botDetector->isBot($request->userAgent()) || ! $request->hasSession()) {
            return false;
        }

        $session = $request->session();
        $today = CarbonImmutable::today()->toDateString();

        /** @var array{date?: string, ids?: list<int>} $viewed */
        $viewed = $session->get(self::SESSION_KEY, []);
        $ids = ($viewed['date'] ?? null) === $today ? ($viewed['ids'] ?? []) : [];

        if (in_array($project->id, $ids, true)) {
            return false;
        }

        Project::query()->whereKey($project->id)->increment('views_count');

        $session->put(self::SESSION_KEY, ['date' => $today, 'ids' => [...$ids, $project->id]]);

        return true;
    }
}

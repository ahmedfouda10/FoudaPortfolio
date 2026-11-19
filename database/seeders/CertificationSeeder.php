<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Certification;
use Illuminate\Database\Seeder;

final class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $certifications = [
            [
                'title' => 'Full Stack PHP Development',
                'issuer' => 'Creativo Center, Mansoura',
                'started_on' => '2021-11-01',
                'completed_on' => '2022-06-01',
                'is_in_progress' => false,
                'credential_urls' => [
                    'https://drive.google.com/file/d/1ccomkKXgrEfu-eER1aVOvBjfRzBTdxJL/view',
                    'https://drive.google.com/file/d/1Mxfn4k9PyHU_RI5RurrtYffaFOUU1zkT/view',
                ],
                'description' => null,
            ],
            [
                'title' => 'Advanced Backend PHP Development',
                'issuer' => 'Itrax Academy',
                'started_on' => null,
                'completed_on' => '2022-01-01',
                'is_in_progress' => false,
                'credential_urls' => [],
                'description' => null,
            ],
            [
                'title' => 'Claude learning path',
                'issuer' => 'Anthropic Academy',
                'started_on' => null,
                'completed_on' => null,
                'is_in_progress' => true,
                'credential_urls' => [],
                'description' => 'Claude 101, AI Fluency, Claude Platform & API, Claude Code, Agent Skills, Subagents.',
            ],
        ];

        foreach ($certifications as $index => $certification) {
            Certification::query()->firstOrCreate(
                ['title' => $certification['title']],
                [...$certification, 'sort_order' => $index + 1, 'is_visible' => true],
            );
        }
    }
}

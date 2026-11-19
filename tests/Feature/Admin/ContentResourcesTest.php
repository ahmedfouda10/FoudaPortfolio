<?php

declare(strict_types=1);

use App\Filament\Resources\Certifications\Pages as Certifications;
use App\Filament\Resources\Education\Pages as Education;
use App\Filament\Resources\Experiences\Pages as Experiences;
use App\Filament\Resources\Projects\Pages as Projects;
use App\Filament\Resources\Services\Pages as Services;
use App\Filament\Resources\SkillGroups\Pages as SkillGroups;
use App\Models\Certification;
use App\Models\Education as EducationModel;
use App\Models\Experience;
use App\Models\Project;
use App\Models\Service;
use App\Models\SkillGroup;
use Livewire\Livewire;

beforeEach(fn () => actingAsAdmin());

/**
 * [model, list page, create page, edit page, valid form data, attribute to check]
 */
dataset('content resources', [
    'experience' => [
        Experience::class, Experiences\ListExperiences::class, Experiences\CreateExperience::class, Experiences\EditExperience::class,
        ['role' => 'Lead Backend Engineer', 'company' => 'Acme', 'start_date' => '2024-01-01'],
        'role',
    ],
    'skill group' => [
        SkillGroup::class, SkillGroups\ListSkillGroups::class, SkillGroups\CreateSkillGroup::class, SkillGroups\EditSkillGroup::class,
        ['name' => 'Cloud'],
        'name',
    ],
    'service' => [
        Service::class, Services\ListServices::class, Services\CreateService::class, Services\EditService::class,
        ['title' => 'Code Reviews', 'slug' => 'code-reviews', 'short_description' => 'Pragmatic reviews of Laravel codebases.'],
        'title',
    ],
    'project' => [
        Project::class, Projects\ListProjects::class, Projects\CreateProject::class, Projects\EditProject::class,
        ['title' => 'New Project', 'slug' => 'new-project'],
        'title',
    ],
    'education' => [
        EducationModel::class, Education\ListEducation::class, Education\CreateEducation::class, Education\EditEducation::class,
        ['degree' => 'M.Sc. in Software Engineering', 'institution' => 'Open University'],
        'degree',
    ],
    'certification' => [
        Certification::class, Certifications\ListCertifications::class, Certifications\CreateCertification::class, Certifications\EditCertification::class,
        ['title' => 'Laravel Certified Developer', 'issuer' => 'Laravel'],
        'title',
    ],
]);

it('lists the records', function (string $model, string $list): void {
    $records = $model::factory()->count(3)->create();

    Livewire::test($list)->assertOk()->assertCanSeeTableRecords($records);
})->with('content resources');

it('creates a record and appends it to the end of the list', function (string $model, string $list, string $create, string $edit, array $data, string $attribute): void {
    $existing = $model::factory()->create(['sort_order' => 7]);

    Livewire::test($create)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoFormErrors();

    $created = $model::query()->where($attribute, $data[$attribute])->sole();

    expect($created->sort_order)->toBe($existing->sort_order + 1);
})->with('content resources');

it('edits a record', function (string $model, string $list, string $create, string $edit, array $data, string $attribute): void {
    $record = $model::factory()->create();

    Livewire::test($edit, ['record' => $record->getRouteKey()])
        ->fillForm([$attribute => 'Updated value'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($record->refresh()->getAttribute($attribute))->toBe('Updated value');
})->with('content resources');

it('validates required fields', function (string $model, string $list, string $create, string $edit, array $data, string $attribute): void {
    Livewire::test($create)
        ->fillForm([$attribute => ''])
        ->call('create')
        ->assertHasFormErrors([$attribute => 'required']);
})->with('content resources');

it('saves a new order after drag and drop', function (): void {
    [$first, $second, $third] = Service::factory()->count(3)->sequence(
        ['sort_order' => 1],
        ['sort_order' => 2],
        ['sort_order' => 3],
    )->create();

    Livewire::test(Services\ListServices::class)
        ->call('reorderTable', [(string) $third->id, (string) $first->id, (string) $second->id]);

    expect(Service::query()->ordered()->pluck('id')->all())->toBe([$third->id, $first->id, $second->id]);
});

it('toggles visibility straight from the table', function (): void {
    $service = Service::factory()->create(['is_visible' => true]);

    Livewire::test(Services\ListServices::class)
        ->call('updateTableColumnState', 'is_visible', (string) $service->id, false);

    expect($service->refresh()->is_visible)->toBeFalse();
});

it('rejects a duplicate project slug', function (): void {
    Project::factory()->create(['slug' => 'taken']);

    Livewire::test(Projects\CreateProject::class)
        ->fillForm(['title' => 'Another', 'slug' => 'taken'])
        ->call('create')
        ->assertHasFormErrors(['slug' => 'unique']);
});

it('manages the skills of a group', function (): void {
    $group = SkillGroup::factory()->hasSkills(2)->create();

    Livewire::test(SkillGroups\EditSkillGroup::class, ['record' => $group->getRouteKey()])->assertOk();

    Livewire::test(App\Filament\Resources\SkillGroups\RelationManagers\SkillsRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => SkillGroups\EditSkillGroup::class,
    ])->assertCanSeeTableRecords($group->skills);
});

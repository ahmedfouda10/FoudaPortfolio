<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();

            // Overview
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('tagline')->nullable();
            $table->text('summary')->nullable();
            $table->string('company')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('status', 32)->nullable();

            // Case study
            $table->text('problem')->nullable();
            $table->text('my_role')->nullable();
            $table->longText('solution')->nullable();
            $table->longText('results')->nullable();
            $table->json('tech_stack')->nullable();
            $table->json('integrations')->nullable();

            // Media & links
            $table->string('cover_image')->nullable();
            $table->string('cover_image_alt')->nullable();
            $table->json('links')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();

            // Visibility, ordering & stats
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();

            $table->index(['is_published', 'is_featured', 'sort_order']);
        });

        Schema::create('project_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_images');
        Schema::dropIfExists('projects');
    }
};

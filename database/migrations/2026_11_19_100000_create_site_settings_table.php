<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Site-wide settings live in a single, strongly typed row (not key/value):
 * the fields are known up front, so each one gets a real column type, a cast
 * and validation, and the whole row can be cached as one object.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();

            // Identity
            $table->string('full_name');
            $table->string('headline')->nullable();
            $table->string('short_bio', 500)->nullable();
            $table->string('location')->nullable();
            $table->string('availability_status')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cv_file')->nullable();

            // Contact & social
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('whatsapp', 50)->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('freelancer_url')->nullable();
            $table->string('source_code_url')->nullable();

            // Hero stats (hidden on the site while empty)
            $table->unsignedTinyInteger('years_of_experience')->nullable();
            $table->unsignedSmallInteger('projects_shipped')->nullable();
            $table->unsignedSmallInteger('platforms_integrated')->nullable();

            // About section
            $table->longText('about_body')->nullable();
            $table->json('about_highlights')->nullable();

            // SEO
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

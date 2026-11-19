<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('inquiry_type', 32);
            $table->string('budget', 100)->nullable();
            $table->string('subject');
            $table->text('message');
            $table->string('status', 32)->default('new');
            $table->char('ip_hash', 64)->nullable()->comment('HMAC-SHA256 of the IP, never the raw address');
            $table->string('user_agent', 512)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
    }
};

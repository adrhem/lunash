<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $collection) {
            $collection->id();
            $collection->string('name');
            $collection->string('email')->unique();
            $collection->string('password');
            $collection->rememberToken();
            $collection->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $collection) {
            $collection->id();
            $collection->string('email')->index();
            $collection->string('token');
            $collection->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $collection) {
            $collection->id();
            $collection->foreignId('user_id')->nullable()->index();
            $collection->string('ip_address', 45)->nullable();
            $collection->text('user_agent')->nullable();
            $collection->longText('payload');
            $collection->integer('last_activity')->index();
            $collection->expire('expires_at', config('session.lifetime'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

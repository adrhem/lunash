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
        Schema::create('applications', function (Blueprint $collection) {
            $collection->id();
            $collection->string('name')->index();
            $collection->string('status')->index();
            $collection->number('services_count')->default(0);
            $collection->string('compose_file');
            $collection->json('services'); // embedded documents
            $collection->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

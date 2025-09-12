<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('docker_manifests', function (Blueprint $table) {
            $table->id();
            $table->string("image");
            $table->string("tag");
            $table->json("manifest");
            $table->timestamps();
            $table->index(['image', 'tag']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docker_manifests');
    }
};

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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('lastname');
            $table->string('address')->nullable();
            $table->string('type')->comment('empresa o persona');
            $table->string('dni')->unique()->nullable();
            $table->string('ruc')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('regular_publications')->default(0);
            $table->integer('featured_publications')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};

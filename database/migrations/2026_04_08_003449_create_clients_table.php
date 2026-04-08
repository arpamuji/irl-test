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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('src_code', 50)->nullable();
            $table->string('name');
            $table->string('short_name')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['src_code', 'deleted_at'])
                ->whereNotNull('src_code')
                ->whereNull('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

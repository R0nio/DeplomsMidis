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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->nullable();
            $table->string('title')->nullable();
            $table->string('short_description')->nullable();
            $table->text('full_description')->nullable();
            $table->json('category')->nullable();
            $table->string('status')->default('На модерации')->nullable();
            $table->bigInteger('total_investment')->nullable()->nullable();
            $table->bigInteger('collected_total_investment')->default(0)->nullable();
            $table->integer('number_date_realise')->nullable();
            $table->string('ownership')->nullable();
            $table->string('activity')->nullable();
            $table->string('type_build')->nullable();
            $table->bigInteger('count_new_job')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_moderated')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

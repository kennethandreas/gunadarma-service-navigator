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
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->foreignId('predicted_category_id')->nullable()
                ->constrained('categories')->nullOnDelete();
            $table->decimal('confidence', 5, 4)->nullable();
            $table->foreignId('service_id')->nullable()
                ->constrained('services')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};

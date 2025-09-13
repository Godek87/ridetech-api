<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Создаёт таблицу cars для хранения транспорта водителей.
     * Поля:
     * - driver_id: FK на users
     * - make, model, plate_number и др.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->string('make')->nullable();
            $table->string('model')->nullable();
            $table->string('plate_number')->unique();
            $table->string('color')->nullable();
            $table->unsignedTinyInteger('seats')->default(4);
            $table->timestamps();

            $table->index('driver_id');
        });
    }

    /**
     * Откат миграции — удаляет таблицу cars.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};

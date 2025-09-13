<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Создаёт таблицу trips для хранения информации о поездках.
     * Поля включают foreign keys на пользователей и машину, адреса, статус и временные метки.
     */
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passenger_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('car_id')->nullable()->constrained('cars')->nullOnDelete();
            $table->string('from_address');
            $table->string('to_address');
            $table->json('preferences')->nullable();
            $table->enum('status', ['pending','accepted','rejected','in_progress','completed','cancelled'])->default('pending');
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['passenger_id', 'driver_id']);
        });
    }

    /**
     * Откат миграции — удаляет таблицу trips.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};

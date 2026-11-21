<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('doctor_id')->nullable()->unique()->after('role');
            $table->integer('staff_id')->nullable()->unique()->after('doctor_id');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->nullOnDelete();

            $table->foreign('staff_id')
                ->references('id')
                ->on('staff')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['staff_id']);

            $table->dropUnique(['doctor_id']);
            $table->dropUnique(['staff_id']);

            $table->dropColumn(['doctor_id', 'staff_id']);
        });
    }
};
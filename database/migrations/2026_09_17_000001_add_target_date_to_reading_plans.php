<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reading_plans', function (Blueprint $table) {
            $table->date('target_date')->nullable()->after('finished_at');
        });

        // MySQL のみ: enum に 'expired' を追加（SQLite は VARCHAR として扱うため不要）
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reading_plans MODIFY COLUMN status ENUM('want','reading','done','expired') NOT NULL DEFAULT 'want'");
        }
    }

    public function down(): void
    {
        Schema::table('reading_plans', function (Blueprint $table) {
            $table->dropColumn('target_date');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE reading_plans MODIFY COLUMN status ENUM('want','reading','done') NOT NULL DEFAULT 'want'");
        }
    }
};

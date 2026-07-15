<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the "role" column to the users table if it does not already exist.
     *
     * This migration is safe to run even if the table was imported via phpMyAdmin
     * from schema.sql (which already includes the role column).
     */
    public function up(): void
    {
        if (!Schema::hasColumn("users", "role")) {
            Schema::table("users", function (Blueprint $table) {
                // Allowed values: student, teacher, admin
                $table->string("role")->default("student")->after("password");
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn("users", "role")) {
            Schema::table("users", function (Blueprint $table) {
                $table->dropColumn("role");
            });
        }
    }
};

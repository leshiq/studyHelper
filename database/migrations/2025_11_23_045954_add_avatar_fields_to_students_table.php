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
        Schema::table('students', function (Blueprint $table) {
            $table->string('avatar_original')->nullable()->after('email');
            $table->string('avatar_large')->nullable()->after('avatar_original');
            $table->string('avatar_medium')->nullable()->after('avatar_large');
            $table->string('avatar_small')->nullable()->after('avatar_medium');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['avatar_original', 'avatar_large', 'avatar_medium', 'avatar_small']);
        });
    }
};

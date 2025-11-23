<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert portal appearance settings
        $settings = [
            ['key' => 'portal_logo', 'value' => '', 'type' => 'string'],
            ['key' => 'login_bg_image', 'value' => '', 'type' => 'string'],
            ['key' => 'login_bg_gradient', 'value' => 'linear-gradient(135deg, #667eea, #764ba2)', 'type' => 'string'],
            ['key' => 'login_bg_color', 'value' => '#667eea', 'type' => 'string'],
            ['key' => 'pages_bg_image', 'value' => '', 'type' => 'string'],
            ['key' => 'pages_bg_gradient', 'value' => '', 'type' => 'string'],
            ['key' => 'pages_bg_color', 'value' => '#ffffff', 'type' => 'string'],
            ['key' => 'sidebar_color', 'value' => 'linear-gradient(180deg, #667eea 0%, #764ba2 100%)', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'type' => $setting['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $keys = [
            'portal_logo',
            'login_bg_image',
            'login_bg_gradient',
            'login_bg_color',
            'pages_bg_image',
            'pages_bg_gradient',
            'pages_bg_color',
            'sidebar_color',
        ];

        DB::table('settings')->whereIn('key', $keys)->delete();
    }
};

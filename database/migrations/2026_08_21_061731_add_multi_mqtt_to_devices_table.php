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
        Schema::table('devices', function (Blueprint $table) {
            $table->string('mqtt_topic_status')->nullable()->after('mqtt_topic');
            $table->string('mqtt_topic_output')->nullable()->after('mqtt_topic_status');
            $table->string('mqtt_topic_schedule')->nullable()->after('mqtt_topic_output');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn([
                'mqtt_topic_status',
                'mqtt_topic_output',
                'mqtt_topic_schedule'
            ]);
        });
    }
};

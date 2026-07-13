<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ports', function (Blueprint $table) {
            if (!Schema::hasColumn('ports', 'status')) {
                $table->string('status', 20)->default('Active')->after('is_active');
            }
        });
    }

    public function down()
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('ports', function (Blueprint $table) {
            if (!Schema::hasColumn('ports', 'name')) {
                $table->string('name')->nullable()->after('id');
            }
            if (!Schema::hasColumn('ports', 'code')) {
                $table->string('code', 10)->nullable()->after('name');
            }
        });
    }

    public function down()
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropColumn(['name', 'code']);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('deputies', function (Blueprint $table) {
            if (!Schema::hasColumn('deputies', 'uri')) {
                $table->string('uri')->nullable();
            }
            if (!Schema::hasColumn('deputies', 'party_uri')) {
                $table->string('party_uri')->nullable();
            }
            if (!Schema::hasColumn('deputies', 'legislature_id')) {
                $table->integer('legislature_id')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('deputies', function (Blueprint $table) {
            $table->dropColumn(['uri', 'party_uri', 'legislature_id']);
        });
    }
};

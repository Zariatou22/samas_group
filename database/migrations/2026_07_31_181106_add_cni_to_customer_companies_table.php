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
        if (Schema::hasColumn('customer_companies', 'cni')) {
            return;
        }

        Schema::table('customer_companies', function (Blueprint $table) {
            $table->string('cni', 100)->nullable()->after('nif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_companies', function (Blueprint $table) {
            $table->dropColumn('cni');
        });
    }
};

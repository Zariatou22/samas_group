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
        Schema::table('customer_docs', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_docs', 'company')) {
                $table->unsignedInteger('company')->nullable()->after('customer');
            }
            if (! Schema::hasColumn('customer_docs', 'type')) {
                $table->string('type', 30)->nullable()->after('name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_docs', function (Blueprint $table) {
            $table->dropColumn(['company', 'type']);
        });
    }
};

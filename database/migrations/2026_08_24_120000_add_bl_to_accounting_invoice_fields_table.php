<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend côté Laravel le lien `bl` ajouté sur `accounting_invoices` côté
 * CodeIgniter (facturation groupée par BL avec sous-total par BL sur la
 * facture imprimée) : chaque ligne de facture peut désormais être rattachée
 * à un BL précis.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounting_invoice_fields', function (Blueprint $table) {
            if (! Schema::hasColumn('accounting_invoice_fields', 'bl')) {
                $table->unsignedInteger('bl')->nullable()->after('invoice');
            }
        });
    }

    public function down(): void
    {
        Schema::table('accounting_invoice_fields', function (Blueprint $table) {
            $table->dropColumn('bl');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Champs "Avance reçu / Reste à payer" et "Arrêté le présent reçu à la
 * somme de / Reste à payer à destination" du reçu papier, ajoutés après
 * coup à la demande.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('invoice_advance_receipts', 'avance_recu')) {
            return;
        }

        Schema::table('invoice_advance_receipts', function ($table) {
            $table->double('avance_recu')->nullable()->after('destination');
            $table->double('reste_a_payer')->nullable()->after('avance_recu');
            $table->string('arrete_somme')->nullable()->after('reste_a_payer');
            $table->string('reste_a_payer_destination')->nullable()->after('arrete_somme');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_advance_receipts', function ($table) {
            $table->dropColumn(['avance_recu', 'reste_a_payer', 'arrete_somme', 'reste_a_payer_destination']);
        });
    }
};

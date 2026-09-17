<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Les opérations créées depuis le pipeline "non facturées -> Facturé"
 * (AccountingInvoiceOperationController) piochent leur désignation dans le
 * catalogue `invoice_labels` (celui des factures prestataires), pas dans
 * `accounting_invoice_labels` (celui du formulaire de facture directe) —
 * reprend exactement Invoice::operation_add() côté CodeIgniter. Le texte est
 * copié ici au lieu de garder une FK vers `invoice_labels`, pour qu'un
 * changement ultérieur du libellé ne modifie pas rétroactivement les
 * factures déjà émises (même logique que côté CodeIgniter, qui copie
 * `$label->name` dans la colonne `designation` au lieu de garder une
 * référence).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('accounting_invoice_fields', 'designation')) {
            Schema::table('accounting_invoice_fields', function (Blueprint $table) {
                $table->string('designation')->nullable()->after('label');
            });
        }

        DB::statement('ALTER TABLE `accounting_invoice_fields` MODIFY `label` int(11) NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE `accounting_invoice_fields` SET `label` = 0 WHERE `label` IS NULL');
        DB::statement('ALTER TABLE `accounting_invoice_fields` MODIFY `label` int(11) NOT NULL');

        Schema::table('accounting_invoice_fields', function (Blueprint $table) {
            $table->dropColumn('designation');
        });
    }
};

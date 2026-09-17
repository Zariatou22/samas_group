<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend côté Laravel le pipeline "opérations non facturées -> Facturé ->
 * opérations facturées" ajouté côté CodeIgniter sur `accounting_invoices`
 * (Invoice::operation_add()/operations_unbilled()/mark_operations_invoiced()) :
 * une ligne peut désormais exister sans facture (`invoice` nullable, brouillon
 * rattaché directement à un `customer` + `bl`) tant qu'elle n'a pas été
 * regroupée sous une référence de facture via le bouton "Facturé".
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('accounting_invoice_fields', 'customer')) {
            Schema::table('accounting_invoice_fields', function (Blueprint $table) {
                $table->unsignedInteger('customer')->nullable()->after('bl');
            });
        }

        DB::statement('ALTER TABLE `accounting_invoice_fields` MODIFY `invoice` int(11) NULL');
    }

    public function down(): void
    {
        DB::statement('UPDATE `accounting_invoice_fields` SET `invoice` = 0 WHERE `invoice` IS NULL');
        DB::statement('ALTER TABLE `accounting_invoice_fields` MODIFY `invoice` int(11) NOT NULL');

        Schema::table('accounting_invoice_fields', function (Blueprint $table) {
            $table->dropColumn('customer');
        });
    }
};

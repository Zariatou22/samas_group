<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Colonnes quantité / prix unitaire pour les lignes de reçu d'avance
 * transport, afin de matcher le format papier (Désignation, Quantité,
 * Prix unitaire, Prix total).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('invoice_advance_lines', 'quantity')) {
            return;
        }

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoice_advance_lines`
  ADD COLUMN `quantity` double NOT NULL DEFAULT 1 AFTER `designation`,
  ADD COLUMN `unit_price` double NOT NULL DEFAULT 0 AFTER `quantity`;
SQL);
    }

    public function down(): void
    {
        if (! Schema::hasColumn('invoice_advance_lines', 'quantity')) {
            return;
        }

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoice_advance_lines`
  DROP COLUMN `quantity`,
  DROP COLUMN `unit_price`;
SQL);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `accounting_invoice_fields_regular` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accounting_invoice_fields_regular')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `accounting_invoice_fields_regular` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `label` int(11) NOT NULL,
  `quantity` double NOT NULL DEFAULT 1,
  `unit_price` double NOT NULL,
  `amount` double NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `accounting_invoice_fields_regular`
  ADD PRIMARY KEY (`id`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `accounting_invoice_fields_regular`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_invoice_fields_regular');
    }
};

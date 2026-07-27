<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `accounting_invoices` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('accounting_invoices')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `accounting_invoices` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `reference` text NOT NULL,
  `amount` double NOT NULL,
  `fees` double NOT NULL,
  `vat` double NOT NULL,
  `amount_ttc` double NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `date_issued` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `accounting_invoices`
  ADD PRIMARY KEY (`id`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `accounting_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_invoices');
    }
};

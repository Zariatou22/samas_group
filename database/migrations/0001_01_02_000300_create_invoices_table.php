<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `invoices` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) NOT NULL,
  `bl` int(11) NOT NULL,
  `label` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` double NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `status` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`,`customer`,`bl`,`label`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

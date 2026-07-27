<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `invoice_payment` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_payment')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `invoice_payment` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `invoice` int(11) NOT NULL,
  `reference` varchar(200) NOT NULL,
  `amount` double NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoice_payment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user` (`user`,`invoice`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `invoice_payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payment');
    }
};

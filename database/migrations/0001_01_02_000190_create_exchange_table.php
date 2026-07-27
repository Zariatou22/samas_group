<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `exchange` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exchange')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `exchange` (
  `id` int(11) NOT NULL,
  `bl` int(11) NOT NULL,
  `date_received` date NOT NULL,
  `status` int(11) DEFAULT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `exchange`
  ADD PRIMARY KEY (`id`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `exchange`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange');
    }
};

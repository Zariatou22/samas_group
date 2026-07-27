<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `system_variables` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('system_variables')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `system_variables` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` mediumtext DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `system_variables`
  ADD PRIMARY KEY (`id`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `system_variables`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_variables');
    }
};

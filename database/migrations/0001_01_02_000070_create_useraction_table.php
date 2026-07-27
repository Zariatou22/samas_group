<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `useraction` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('useraction')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `useraction` (
  `idsessinfo` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `action` varchar(100) NOT NULL DEFAULT 'login',
  `platform` varchar(255) NOT NULL,
  `device` varchar(255) NOT NULL,
  `browser` varchar(255) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `date_session` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `useraction`
  ADD PRIMARY KEY (`idsessinfo`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `useraction`
  MODIFY `idsessinfo` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('useraction');
    }
};

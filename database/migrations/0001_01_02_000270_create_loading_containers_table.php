<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `loading_containers` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loading_containers')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `loading_containers` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `loading` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) NOT NULL,
  `bl` int(11) NOT NULL,
  `authorization` int(11) NOT NULL,
  `container` int(11) NOT NULL,
  `nb_package` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `status` int(11) DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `loading_containers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`,`loading`,`customer`,`customer_company`,`bl`,`authorization`,`container`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `loading_containers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('loading_containers');
    }
};

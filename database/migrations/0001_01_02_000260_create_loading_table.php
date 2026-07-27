<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `loading` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('loading')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `loading` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) DEFAULT NULL,
  `bl` int(11) NOT NULL,
  `nb_package` int(11) NOT NULL,
  `authorization` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `car` int(11) NOT NULL,
  `owner` int(11) NOT NULL,
  `driver` int(11) NOT NULL,
  `source` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  `loading_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `loading`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_UNIQUE` (`id`),
  ADD KEY `user` (`user`,`customer`,`customer_company`,`bl`,`authorization`,`car`,`owner`,`driver`,`source`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `loading`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('loading');
    }
};

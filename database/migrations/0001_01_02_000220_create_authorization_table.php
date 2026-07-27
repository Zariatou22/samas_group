<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `authorization` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('authorization')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `authorization` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `bl` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) NOT NULL DEFAULT 0,
  `auth_number` varchar(45) NOT NULL,
  `nb_package` int(11) NOT NULL,
  `nb_container` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `source` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `authorization`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bl` (`bl`,`customer`,`customer_company`),
  ADD KEY `user` (`user`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `authorization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('authorization');
    }
};

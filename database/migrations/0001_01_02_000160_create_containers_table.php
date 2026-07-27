<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `containers` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('containers')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `containers` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `bl` int(11) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) DEFAULT 0,
  `type_tc` varchar(100) NOT NULL,
  `numero` varchar(100) NOT NULL,
  `lead_number` varchar(200) DEFAULT NULL,
  `ship` varchar(100) NOT NULL,
  `eta` date NOT NULL,
  `product_type` int(11) NOT NULL,
  `nb_package` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `containers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`,`bl`,`customer`,`customer_company`,`product_type`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `containers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};

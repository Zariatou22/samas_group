<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend le schema exact de la table `bl` de la base legacy SAMAS
 * (export du 27/07/2026) : permet un `php artisan migrate` sur une base
 * neuve sans dependre d'un import SQL manuel. Sans effet si la table existe
 * deja (cas d'un import legacy classique).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('bl')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `bl` (
  `id` int(11) NOT NULL,
  `user` int(11) NOT NULL,
  `telex` tinyint(1) DEFAULT 0,
  `bl` varchar(255) NOT NULL,
  `customer` int(11) NOT NULL,
  `customer_company` int(11) DEFAULT NULL,
  `company` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `type_operation` varchar(255) NOT NULL,
  `movements` varchar(255) NOT NULL,
  `product_type` int(11) NOT NULL,
  `quantity` double NOT NULL,
  `nb_package` int(11) NOT NULL,
  `shipper` varchar(200) NOT NULL,
  `vessel` varchar(200) NOT NULL,
  `loading_date` date NOT NULL,
  `port_of_load` varchar(200) NOT NULL,
  `port_of_discharge` varchar(200) NOT NULL,
  `eta_date` date DEFAULT NULL,
  `consignee` varchar(200) DEFAULT NULL,
  `notify` varchar(200) DEFAULT NULL,
  `product_value` double NOT NULL DEFAULT 0,
  `tariff` double NOT NULL DEFAULT 0,
  `route` varchar(2500) DEFAULT NULL,
  `observation` text DEFAULT NULL,
  `agent` varchar(100) DEFAULT NULL,
  `is_urgent` tinyint(4) NOT NULL,
  `is_started` tinyint(1) NOT NULL DEFAULT 0,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `bl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user` (`user`,`customer`,`customer_company`,`company`,`product_type`);
SQL);

        DB::unprepared(<<<'SQL'
ALTER TABLE `bl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('bl');
    }
};

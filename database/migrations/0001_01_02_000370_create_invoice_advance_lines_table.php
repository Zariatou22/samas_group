<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Lignes (désignation + prix total) d'un reçu d'avance transport.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_advance_lines')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `invoice_advance_lines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `receipt` int(11) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `amount` double NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `receipt` (`receipt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_advance_lines');
    }
};

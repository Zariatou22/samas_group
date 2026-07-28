<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reçus d'avance transport (document papier remis au chauffeur), géré
 * séparément des factures clients/prestataires classiques : c'est une
 * sortie d'argent vers un transporteur, pas une facture émise à un client.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('invoice_advance_receipts')) {
            return;
        }

        DB::unprepared(<<<'SQL'
CREATE TABLE `invoice_advance_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `date_issued` date NOT NULL,
  `driver` int(11) DEFAULT NULL,
  `car` int(11) DEFAULT NULL,
  `bl` int(11) DEFAULT NULL,
  `contact_client` varchar(255) DEFAULT NULL,
  `contact_transitaire` varchar(255) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_advance_receipts');
    }
};

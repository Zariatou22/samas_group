<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Facture Pro Forma : document distinct de la facture client
 * (App\Models\AccountingInvoice), sans ligne de prix/quantité — juste un
 * en-tête (référence/client/date) et des informations logistiques
 * (conteneurs, marchandise) destinées à être imprimées avant la facture
 * définitive.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_invoices', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user');
            $table->unsignedInteger('customer');
            $table->string('reference', 100)->unique();
            $table->date('date_issued');
            $table->string('consignee_house')->nullable();
            $table->string('container_type')->nullable();
            $table->unsignedInteger('container_count')->nullable();
            $table->string('goods_nature')->nullable();
            $table->string('weight_value')->nullable();
            $table->unsignedInteger('status')->default(0);
            $table->dateTime('created')->useCurrent();
            $table->dateTime('modified')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proforma_invoices');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reprend côté Laravel le sous-menu "Dépotage" ajouté côté CodeIgniter
 * (Loading::unloadings()/unloading_edit()) : un chargement (`type`=0) et un
 * dépotage (`type`=1) partagent la même table et le même formulaire, seule
 * la disponibilité (déclarations/conteneurs/BL) est comptée séparément par
 * type — un conteneur déjà chargé reste disponible pour un dépotage et
 * inversement.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('loading', 'type')) {
            Schema::table('loading', function (Blueprint $table) {
                $table->tinyInteger('type')->default(0)->after('bl');
            });
        }
    }

    public function down(): void
    {
        Schema::table('loading', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

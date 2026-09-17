<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Reprend telle quelle la page d'accueil de l'ancienne application CodeIgniter
     * (public/admin/index.php) : 4 cartes de catégorie qui mènent chacune à une
     * page intermédiaire listant les vraies cartes (cf. operations/trackingMenu/
     * accounting/documentMenu ci-dessous).
     */
    public function index(): View
    {
        return view('admin.home');
    }

    /**
     * Reprend public/admin/operations.php : gestion des B/L + déclarations/chargements/T1.
     */
    public function operations(): View
    {
        $cards = [
            ['label' => 'Tous les BLS', 'icon' => 'fa-file-alt', 'url' => route('admin.bls.index', ['activeTab' => 'all'])],
            ['label' => "En attente d'arrivée", 'icon' => 'fa-file-alt', 'url' => route('admin.bls.index', ['activeTab' => 'waiting'])],
            ['label' => 'Opérations en cours', 'icon' => 'fa-file-contract', 'url' => route('admin.bls.index', ['activeTab' => 'ongoing'])],
            ['label' => 'Opérations clôturées', 'icon' => 'fa-file-archive', 'url' => route('admin.bls.index', ['activeTab' => 'completed'])],
            ['label' => 'Déclarations en cours', 'icon' => 'fa-clipboard', 'url' => route('admin.authorizations.index', ['activeTab' => 'ongoing']), 'color' => 'info'],
            ['label' => 'Déclarations soldées', 'icon' => 'fa-clipboard-check', 'url' => route('admin.authorizations.index', ['activeTab' => 'settled']), 'color' => 'info'],
            ['label' => 'Chargements', 'icon' => 'fa-truck', 'url' => route('admin.loadings.index'), 'color' => 'info'],
            ['label' => 'T1 effectué', 'icon' => 'fa-scroll', 'url' => route('admin.loading-t1s.index'), 'color' => 'info'],
        ];

        return view('admin.operations', ['title' => 'Opérations', 'cards' => $cards]);
    }

    /**
     * Reprend public/admin/tracking-menu.php.
     */
    public function trackingMenu(): View
    {
        $cards = [
            ['label' => 'Franchises', 'icon' => 'fa-truck-loading', 'url' => route('admin.container-tracking.index')],
            ['label' => "En attente d'opération", 'icon' => 'fa-file', 'url' => route('admin.bls.index', ['activeTab' => 'arrived'])],
            ['label' => 'En attente de T1', 'icon' => 'fa-trailer', 'url' => route('admin.loadings.index', ['activeTab' => 'without_t1'])],
        ];

        return view('admin.operations', ['title' => 'Suivi & contrôles', 'cards' => $cards, 'defaultColor' => 'warning']);
    }

    /**
     * Reprend public/admin/accounting.php.
     */
    public function accounting(): View
    {
        $cards = [
            ['label' => 'Factures prestataires', 'icon' => 'fa-file-invoice', 'url' => route('admin.mandataire-balances.index')],
            ['label' => 'Factures clients', 'icon' => 'fa-file-invoice', 'url' => route('admin.accounting-invoices.index')],
            ['label' => 'Facture pro forma', 'icon' => 'fa-file-invoice', 'url' => route('admin.proforma-invoices.index')],
        ];

        return view('admin.operations', ['title' => 'Comptabilité', 'cards' => $cards, 'defaultColor' => 'success']);
    }

    /**
     * Reprend public/admin/document-menu.php (vide pour le moment côté CI).
     */
    public function documentMenu(): View
    {
        return view('admin.operations', ['title' => 'Document', 'cards' => [], 'defaultColor' => 'dark']);
    }
}

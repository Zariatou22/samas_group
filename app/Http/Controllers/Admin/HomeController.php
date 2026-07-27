<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Reprend telle quelle la page d'accueil de l'ancienne appli CodeIgniter
     * (public/admin/home-icons-*.php), avec les mêmes libellés/couleurs/icônes,
     * et les mêmes 4 sections que App\Filament\Pages\Home::getSections()
     * (dont ce contrôleur reprend directement la logique/permissions).
     */
    public function index(): View
    {
        $user = Auth::user();

        $sections = [
            [
                'title' => 'Gestion des B/L',
                'color' => 'primary',
                'visible' => $user?->canAccessModule(['Saisie', 'Contrôle', 'Superivision']) ?? false,
                'cards' => [
                    ['label' => 'Tous les BLS', 'icon' => 'fa-file-text-o', 'url' => route('admin.bls.index', ['activeTab' => 'all'])],
                    ['label' => "En attente d'arrivée", 'icon' => 'fa-clock-o', 'url' => route('admin.bls.index', ['activeTab' => 'waiting'])],
                    ['label' => 'Opérations en cours', 'icon' => 'fa-refresh', 'url' => route('admin.bls.index', ['activeTab' => 'ongoing'])],
                    ['label' => 'Opérations clôturées', 'icon' => 'fa-archive', 'url' => route('admin.bls.index', ['activeTab' => 'completed'])],
                ],
            ],
            [
                'title' => 'Déclarations, chargements & T1',
                'color' => 'info',
                'visible' => $user?->canAccessModule(['Saisie', 'Contrôle', 'Superivision', 'Chargement']) ?? false,
                'cards' => [
                    ['label' => 'Déclarations en cours', 'icon' => 'fa-clipboard', 'url' => route('admin.authorizations.index', ['activeTab' => 'ongoing'])],
                    ['label' => 'Déclarations soldées', 'icon' => 'fa-check-square-o', 'url' => route('admin.authorizations.index', ['activeTab' => 'settled'])],
                    ['label' => 'Chargements', 'icon' => 'fa-truck', 'url' => route('admin.loadings.index')],
                    ['label' => 'T1 effectué', 'icon' => 'fa-certificate', 'url' => route('admin.loading-t1s.index')],
                ],
            ],
            [
                'title' => 'Suivi & contrôles',
                'color' => 'warning',
                'visible' => $user?->canAccessModule(['Saisie', 'Contrôle', 'Superivision', 'Chargement']) ?? false,
                'cards' => [
                    ['label' => 'Franchises', 'icon' => 'fa-truck', 'url' => route('admin.container-tracking.index')],
                    ['label' => "En attente d'opération", 'icon' => 'fa-file', 'url' => route('admin.bls.index', ['activeTab' => 'arrived'])],
                    ['label' => 'En attente de T1', 'icon' => 'fa-hourglass-half', 'url' => route('admin.loadings.index', ['activeTab' => 'without_t1'])],
                ],
            ],
            [
                'title' => 'Comptabilité',
                'color' => 'success',
                'visible' => $user?->canAccessModule(['Compatibilité']) ?? false,
                'cards' => [
                    ['label' => 'Factures prestataires', 'icon' => 'fa-file-text', 'url' => route('admin.mandataire-balances.index')],
                    ['label' => 'Factures clients', 'icon' => 'fa-usd', 'url' => route('admin.accounting-invoices.index')],
                ],
            ],
        ];

        return view('admin.home', ['sections' => $sections]);
    }
}

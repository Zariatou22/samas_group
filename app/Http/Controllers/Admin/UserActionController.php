<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAction;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

/**
 * Journal des connexions (lecture seule), comme Auth::sethistory() /
 * l'écran de journal en CI.
 */
class UserActionController extends Controller
{
    public function index(): View
    {
        return view('admin.user-actions.index');
    }

    public function data(): JsonResponse
    {
        $actions = UserAction::with('accountUser')
            ->orderByDesc('date_session')
            ->limit(500)
            ->get();

        $data = $actions->map(fn (UserAction $a) => [
            'user_name' => $a->accountUser?->displayName() ?? '-',
            'action' => $a->action,
            'platform' => $a->platform,
            'device' => $a->device,
            'browser' => $a->browser,
            'ip' => $a->ip,
            'date_session' => optional($a->date_session)->format('d/m/Y H:i'),
        ]);

        return response()->json(['data' => $data]);
    }
}

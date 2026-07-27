<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\Group;
use App\Models\Perm;
use App\Models\User;
use App\Models\UserVariable;
use App\Services\MobileAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private readonly MobileAuthService $mobileAuth) {}

    public function auth(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device' => ['required', 'string'],
            'uuid' => ['required', 'string'],
            'type' => ['required', 'string'],
        ]);

        $user = User::where('name', $data['name'])->first();

        if (! $user) {
            return response()->json(['success' => false, 'message' => "Nom d'utilisateur incorrect"]);
        }

        if ($user->banned) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas vous connecter. Veuillez contacter votre responsable.']);
        }

        if (! Hash::check($data['password'], $user->pass) && ! $this->checkLegacyPassword($data['password'], $user)) {
            return response()->json(['success' => false, 'message' => 'Mot de passe incorrect']);
        }

        $token = $this->mobileAuth->issueToken($user, $request->getHost());

        Device::updateOrCreate(
            ['user' => $user->id, 'uuid' => $data['uuid']],
            ['device' => $data['device'], 'type' => $data['type'], 'status' => 0],
        );

        return response()->json([
            'success' => true,
            'message' => [
                'user' => $this->userPayload($user, $token),
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->forceFill(['pass' => Hash::make($data['password'])])->save();

        return response()->json(['success' => true, 'message' => 'Mot de passe mis à jour']);
    }

    public function updateUserInfo(Request $request)
    {
        $data = $request->validate([
            'nom' => ['sometimes', 'string', 'max:100'],
            'prenoms' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();
        $user->fill($data);
        $user->fullname = trim("{$user->prenoms} {$user->nom}");
        $user->save();

        return response()->json(['success' => true, 'message' => $this->userPayload($user)]);
    }

    private function checkLegacyPassword(string $plain, User $user): bool
    {
        $legacy = hash('sha256', md5((string) $user->id).$plain);

        if (! hash_equals($user->pass, $legacy)) {
            return false;
        }

        $user->forceFill(['pass' => Hash::make($plain)])->save();

        return true;
    }

    private function userPayload(User $user, ?string $token = null): array
    {
        $vars = UserVariable::where('user_id', $user->id)->pluck('value', 'key')->all();

        if ($token) {
            $vars['user_auth'] = $token;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'nom' => $user->nom,
            'prenoms' => $user->prenoms,
            'fullname' => $user->fullname,
            'sexe' => $user->sexe,
            'banned' => (bool) $user->banned,
            'vars' => $vars,
            'groups' => Group::all(['id', 'name']),
            'user_groups' => $user->groups()->pluck('groups.id'),
            'perms' => Perm::all(['id', 'name']),
            'user_perms' => $user->directPerms()->pluck('perms.id'),
            'group_perms' => $user->allPermissions(),
        ];
    }
}

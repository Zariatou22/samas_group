@php $targetUser = $targetUser ?? null; @endphp
<div class="card">
    <div class="card-header"><h5>Identité</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Nom *</label>
                <input type="text" name="nom" class="form-control" value="{{ old('nom', $targetUser->nom ?? '') }}" required maxlength="100">
            </div>
            <div class="col-md-6 form-group">
                <label>Prénoms *</label>
                <input type="text" name="prenoms" class="form-control" value="{{ old('prenoms', $targetUser->prenoms ?? '') }}" required maxlength="100">
            </div>
            <div class="col-md-6 form-group">
                <label>Sexe *</label>
                <select name="sexe" class="form-control" required>
                    <option value="">Choisir...</option>
                    <option value="Homme" @selected(old('sexe', $targetUser->sexe ?? '') === 'Homme')>Homme</option>
                    <option value="Femme" @selected(old('sexe', $targetUser->sexe ?? '') === 'Femme')>Femme</option>
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label>Email *</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $targetUser->email ?? '') }}" required maxlength="100">
            </div>
            <div class="col-12 form-group">
                <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="banned" id="bannedCheck" value="1" @checked(old('banned', $targetUser->banned ?? false))>
                    <label for="bannedCheck">Compte bloqué</label>
                </div>
                <small class="form-text text-muted">Un compte bloqué ne peut plus se connecter à l'administration.</small>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h5>Mot de passe</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>{{ $targetUser ? 'Nouveau mot de passe' : 'Mot de passe *' }}</label>
                <input type="password" name="pass" class="form-control" minlength="8" {{ $targetUser ? '' : 'required' }}>
                @if ($targetUser)
                    <small class="form-text text-muted">Laisser vide pour conserver le mot de passe actuel.</small>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header"><h5>Rôles et permissions</h5><small class="text-muted">Un utilisateur hérite des permissions de ses groupes, et peut recevoir des permissions supplémentaires en direct.</small></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-6 form-group">
                <label>Groupes</label>
                <select name="groups[]" class="form-control" multiple size="6">
                    @php $selectedGroups = old('groups', $targetUser?->groups->pluck('id')->all() ?? []); @endphp
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" @selected(in_array($group->id, $selectedGroups))>{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 form-group">
                <label>Permissions directes</label>
                <select name="direct_perms[]" class="form-control" multiple size="6">
                    @php $selectedPerms = old('direct_perms', $targetUser?->directPerms->pluck('id')->all() ?? []); @endphp
                    @foreach ($perms as $perm)
                        <option value="{{ $perm->id }}" @selected(in_array($perm->id, $selectedPerms))>{{ $perm->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

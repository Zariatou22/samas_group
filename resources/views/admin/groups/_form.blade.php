@php $group = $group ?? null; @endphp
<div class="card">
    <div class="card-header"><h5>Groupe</h5></div>
    <div class="card-block">
        <div class="row">
            <div class="col-md-12 form-group">
                <label>Nom du groupe *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $group->name ?? '') }}" required maxlength="100">
            </div>
            <div class="col-md-12 form-group">
                <label>Description</label>
                <textarea name="definition" class="form-control">{{ old('definition', $group->definition ?? '') }}</textarea>
            </div>
            <div class="col-md-12 form-group">
                <label>Permissions du groupe</label>
                <select name="perms[]" class="form-control" multiple size="8">
                    @php $selectedPerms = old('perms', $group?->perms->pluck('id')->all() ?? []); @endphp
                    @foreach ($perms as $perm)
                        <option value="{{ $perm->id }}" @selected(in_array($perm->id, $selectedPerms))>{{ $perm->name }}</option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Tout utilisateur membre de ce groupe hérite automatiquement de ces permissions.</small>
            </div>
        </div>
    </div>
</div>

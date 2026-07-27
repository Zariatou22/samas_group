@extends('layouts.admin')

@section('title', 'Dépotage du BL '.$bl->bl)

@section('content')
    <p>
        <a href="{{ route('admin.bls.edit', $bl) }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour au BL</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Dépoter un conteneur</h5></div>
        <div class="card-block">
            <form method="POST" action="{{ route('admin.bls.unpot.store', $bl) }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Conteneur *</label>
                        <select name="container" class="form-control" required>
                            <option value="">Choisir...</option>
                            @foreach ($containers as $container)
                                <option value="{{ $container->id }}">{{ $container->type_tc }} — {{ $container->numero }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Date d'op&eacute;ration *</label>
                        <input type="date" name="date_unpot" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Conteneurs dépotés</h5></div>
        <div class="card-block">
            <div class="table-responsive">
                <table class="table table-sm table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>CONTENEUR</th>
                            <th>DATE</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unpots as $unpot)
                            <tr>
                                <td>{{ $unpot->parentContainer?->type_tc }} — {{ $unpot->parentContainer?->numero }}</td>
                                <td>{{ optional($unpot->date_unpot)->format('d/m/Y') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.bls.unpot.destroy', [$bl, $unpot]) }}" onsubmit="return confirm('Annuler ce dépotage ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Annuler"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">Aucun conteneur dépoté.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.admin')

@section('title', 'Transfert du mandataire '.$customer->customer_name)

@section('content')
    <p>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="card">
        <div class="card-header"><h5>Transférer tout le mandataire</h5></div>
        <div class="card-block">
            <p class="text-muted">
                Toutes les sociétés clientes de <strong>{{ $customer->customer_name }}</strong> et toutes les données
                qui leur sont rattachées (BL, conteneurs, déclarations, chargements, factures, documents) seront
                transférées vers le mandataire choisi. <strong>{{ $customer->customer_name }}</strong> sera ensuite archivé.
            </p>
            <form method="POST" action="{{ route('admin.customers.transfer', $customer) }}" onsubmit="return confirm('Transférer tout le portefeuille de {{ $customer->customer_name }} ? Cette action archive le mandataire d\'origine.')">
                @csrf
                <div class="form-group">
                    <label>Mandataire de substitution *</label>
                    <select name="target" class="form-control" required>
                        <option value="">Choisir...</option>
                        @foreach ($targets as $target)
                            <option value="{{ $target->id }}">{{ $target->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">Transférer tout le portefeuille</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5>Transférer une seule société cliente</h5></div>
        <div class="card-block">
            @forelse ($customer->companies as $company)
                <form method="POST" action="{{ route('admin.customers.companies.transfer', [$customer, $company]) }}" class="form-inline mb-2" onsubmit="return confirm('Transférer {{ $company->name }} ?')">
                    @csrf
                    <span class="mr-2"><strong>{{ $company->name }}</strong></span>
                    <select name="target" class="form-control mr-2" required style="min-width: 250px;">
                        <option value="">Vers le mandataire...</option>
                        @foreach ($targets as $target)
                            <option value="{{ $target->id }}">{{ $target->customer_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-warning">Transférer</button>
                </form>
            @empty
                <p class="text-muted mb-0">Ce mandataire n'a aucune société cliente.</p>
            @endforelse
        </div>
    </div>
@endsection

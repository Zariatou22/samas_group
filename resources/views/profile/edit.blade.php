@extends('layouts.admin')

@section('title', 'Mon profil')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Informations</h5></div>
                <div class="card-block">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Nom complet *</label>
                            <input type="text" name="fullname" class="form-control" value="{{ old('fullname', $user->fullname) }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h5>Mot de passe</h5></div>
                <div class="card-block">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Mot de passe actuel *</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nouveau mot de passe *</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="form-group">
                            <label>Confirmer le nouveau mot de passe *</label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                        </div>
                        <button type="submit" class="btn btn-primary">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

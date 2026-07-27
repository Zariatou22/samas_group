@extends('layouts.admin')

@section('title', 'Paramètres généraux')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.settings.general.update') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Identité du site</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nom du site</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $values['title']) }}" maxlength="255">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email de contact</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $values['email']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Téléphone de contact</label>
                        <input type="text" name="contact" class="form-control" value="{{ old('contact', $values['contact']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" class="form-control" value="{{ old('adresse', $values['adresse']) }}">
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Mots-clés</label>
                        <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $values['keywords']) }}">
                    </div>
                    <div class="col-md-12 form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control">{{ old('description', $values['description']) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Réseaux sociaux</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Facebook</label>
                        <input type="url" name="facebook" class="form-control" value="{{ old('facebook', $values['facebook']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Twitter / X</label>
                        <input type="url" name="twitter" class="form-control" value="{{ old('twitter', $values['twitter']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Instagram</label>
                        <input type="url" name="instagram" class="form-control" value="{{ old('instagram', $values['instagram']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>LinkedIn</label>
                        <input type="url" name="linkedin" class="form-control" value="{{ old('linkedin', $values['linkedin']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>YouTube</label>
                        <input type="url" name="youtube" class="form-control" value="{{ old('youtube', $values['youtube']) }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Maintenance</h5></div>
            <div class="card-block">
                <div class="checkbox checkbox-danger">
                    <input type="checkbox" name="maintenance" id="maintenanceCheck" value="1" @checked(old('maintenance', $values['maintenance']))>
                    <label for="maintenanceCheck">Mode maintenance actif</label>
                </div>
                <small class="form-text text-muted">Empêche les utilisateurs autres que les administrateurs de se connecter.</small>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

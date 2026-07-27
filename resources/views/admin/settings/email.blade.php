@extends('layouts.admin')

@section('title', 'Paramètres email')

@section('content')
    <p>
        <a href="{{ route('admin.home') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Retour</a>
    </p>

    <form method="POST" action="{{ route('admin.settings.email.update') }}">
        @csrf
        <div class="card">
            <div class="card-header"><h5>Serveur SMTP</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Serveur SMTP</label>
                        <input type="text" name="email-smtp-server" class="form-control" value="{{ old('email-smtp-server', $values['email-smtp-server']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Port</label>
                        <input type="number" name="email-smtp-port" class="form-control" value="{{ old('email-smtp-port', $values['email-smtp-port']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Utilisateur</label>
                        <input type="text" name="email-smtp-username" class="form-control" value="{{ old('email-smtp-username', $values['email-smtp-username']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="email-smtp-password" class="form-control" value="{{ old('email-smtp-password', $values['email-smtp-password']) }}">
                    </div>
                    <div class="col-12 form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="email-smtp-auth" id="smtpAuthCheck" value="1" @checked(old('email-smtp-auth', $values['email-smtp-auth']))>
                            <label for="smtpAuthCheck">Authentification requise</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h5>Expéditeur</h5></div>
            <div class="card-block">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nom expéditeur</label>
                        <input type="text" name="email-sender-name" class="form-control" value="{{ old('email-sender-name', $values['email-sender-name']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email expéditeur</label>
                        <input type="email" name="email-sender-email" class="form-control" value="{{ old('email-sender-email', $values['email-sender-email']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nom de réponse</label>
                        <input type="text" name="email-reply-name" class="form-control" value="{{ old('email-reply-name', $values['email-reply-name']) }}">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Email de réponse</label>
                        <input type="email" name="email-reply-email" class="form-control" value="{{ old('email-reply-email', $values['email-reply-email']) }}">
                    </div>
                    <div class="col-12 form-group">
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="email-is-html" id="isHtmlCheck" value="1" @checked(old('email-is-html', $values['email-is-html']))>
                            <label for="isHtmlCheck">Emails au format HTML</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="btn btn-primary btn-lg">Enregistrer</button>
        </div>
    </form>
@endsection

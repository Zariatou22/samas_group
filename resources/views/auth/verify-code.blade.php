<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code de sécurité - SAMAS Groupe</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/pages/waves/css/waves.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/icon/themify-icons/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/icon/font-awesome/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/able/assets/css/style.css') }}">
</head>
<body>
    <section class="login-block">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <form class="md-float-material form-material" method="POST" action="{{ route('password.verify') }}">
                        @csrf
                        <div class="text-center">
                            <img src="{{ asset('images/logo.png') }}" alt="SAMAS Groupe" style="max-height: 4rem;">
                        </div>
                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12">
                                        <h3 class="text-center">Code de sécurité</h3>
                                        <p class="text-center text-muted">Entrez le code reçu par email.</p>
                                    </div>
                                </div>

                                @if ($errors->any())
                                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                                @endif

                                <input type="hidden" name="email" value="{{ $email }}">
                                <div class="form-group form-primary">
                                    <input type="text" name="code" class="form-control" required autofocus maxlength="6">
                                    <span class="form-bar"></span>
                                    <label class="float-label">Code à 6 chiffres</label>
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-md btn-block waves-effect waves-light text-center m-b-20">
                                            Vérifier
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('login') }}">Retour à la connexion</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('vendor/able/assets/js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/popper.js/popper.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/js/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/able/assets/pages/waves/js/waves.min.js') }}"></script>
</body>
</html>

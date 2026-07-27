<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Code QR OTP - SAMAS Groupe</title>
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
                    <div class="text-center">
                        <img src="{{ asset('images/logo.png') }}" alt="SAMAS Groupe" style="max-height: 4rem;">
                    </div>
                    <div class="auth-box card">
                        <div class="card-block text-center">
                            <h3>Scannez ce code</h3>
                            <p class="text-muted">Avec Google Authenticator, Authy ou une application équivalente.</p>
                            <img src="{{ $qrCode }}" alt="QR code OTP" class="img-fluid">
                            <p class="mt-3">
                                <a href="{{ route('login-otp.show') }}" class="btn btn-primary">Aller à la connexion OTP</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>

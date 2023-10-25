<!doctype html>
<html lang="en">

<head>
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <title>Aursoft Private Limited</title>
    <meta name="description" content="Aursoft Private Limite| Make Your Business Easy">
    <meta name="keywords" content="POS, Inventory, Stock, Billing" />
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}" sizes="32x32">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/icon/192x192.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="manifest" href="__manifest.json">

</head>

<body>

    <!-- loader -->
    <div id="loader">
        <img src="assets/img/loading-icon.png" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->

   

    <!-- App Capsule -->
    <div id="appCapsule">
        <div class="section mt-2 text-center">
            @if (\Session::has('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-1 text-center" role="alert">
                    {!! \Session::get('error') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>
        <div class="section mt-2 text-center">
            <h1>Log in</h1>
        </div>
        <div class="section mb-5 p-2">

            <form method="POST" action="{{ route('login') }}">@csrf
                <div class="card">
                    <div class="card-body pb-1">
                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <input type="text" class="form-control @error('username') is-invalid  @enderror" value="{{ old('username') }}" id="username" name="username" placeholder="Your Username">
                                <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                                @if ($errors->has('username'))
                                    <span class="text-danger">{{ $errors->first('username') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group boxed">
                            <div class="input-wrapper">
                                <input type="password" class="form-control @error('password') is-invalid  @enderror" name="password" id="password1" autocomplete="off" placeholder="Your password">
                                <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-button-group  transparent">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
                </div>

            </form>
        </div>

    </div>
    <!-- * App Capsule -->



    <script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
    <!-- Ionicons -->
    <!-- Splide -->
    <script src="{{ asset('assets/js/plugins/splide/splide.min.js') }}"></script>
    <!-- Base Js File -->
    <script src="{{ asset('assets/js/base.js') }}"></script>


</body>

</html>
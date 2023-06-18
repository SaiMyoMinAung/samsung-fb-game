<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta property="og:url" content="{{ $imageUrl }}" /> --}}
    <meta property="og:url" content="{{ route('samsung-tv', ['id' => $gameUsedUser->photo]) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $textData['tv_first_title'] ?? 'Samsung TV' }}" />
    <meta property="og:description" content="{{ $textData['tv_second_title'] ?? 'Samsung TV' }}" />
    {{-- <meta property="og:image" content="{{ route('samsung-tv', ['id' => $gameUsedUser->photo]) }}" /> --}}
    <meta property="og:image" content="{{ $base64 }}" />
    <title>Samsung TV Game</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous">
    <style>
        .btn-facebook {
            color: #fff;
            background-color: #3b5998;
            border-color: rgba(0, 0, 0, 0.2);
        }

        .btn-social {
            position: relative;
            padding-left: 44px;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-social:hover {
            color: #eee;
        }

        .btn-social :first-child {
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 40px;
            padding: 7px;
            font-size: 1.6em;
            text-align: center;
            border-right: 1px solid rgba(0, 0, 0, 0.2);
        }

        body {
            height: 100vh;
        }

        .container {
            height: 100%;
        }

        .social-btn-sp #social-links {
            margin: 0 auto;
            max-width: 500px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row col justify-content-center">
            <h2>{{ $textData['tv_name'] ?? '-' }}</h2>
        </div>
        <div class="row">
            <div class="col">
                <div class="text-center">
                    @if (isset($textData['tv_first_title']) && isset($textData['tv_second_title']) && isset($textData['tv_third_title']))
                        <p>
                            {{ $textData['tv_first_title'] }}
                            {{ $textData['tv_second_title'] }}
                            {{ $textData['tv_third_title'] }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-2">
            @if ($gameUsedUser->shared == 1)
                <a href="{{ url('auth/facebook') }}" class="btn btn-lg btn-social btn-facebook m-2">
                    <i class="fa-brands fa-facebook-f"></i> Play this game
                </a>
            @else
                <a href="{{ route('share', ['id' => $gameUsedUser->photo]) }}" class="btn btn-lg btn-social btn-facebook m-2">
                    <i class="fa-solid fa-share"></i> Share to facebook
                </a>
            @endif
        </div>

        <div class="row align-items-center justify-content-center">
            <div class="text-center">
                <img width="80%" src="{{ $imageUrl }}">
            </div>
        </div>
    </div>
</body>

</html>

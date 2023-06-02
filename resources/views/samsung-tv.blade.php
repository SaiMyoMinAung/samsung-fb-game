<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:url" content="{{ $imageUrl }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $textData['first_title'] ?? 'Samsung TV' }}" />
    <meta property="og:description" content="{{ $textData['second_title'] ?? 'Samsung TV' }}" />
    <meta property="og:image" content="{{ $imageUrl }}" />
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
        <div class="row">
            <div class="col">
                <div class="text-center m-3">
                    @if (isset($textData['first_title']) && isset($textData['second_title']))
                        <p>{{ \Rabbit::zg2uni($textData['first_title']) }} {{ \Rabbit::zg2uni($textData['second_title']) }}</p>
                    @endif
                </div>
            </div>
        </div>


        <div class="row  justify-content-center">
            <div class="col-4">
                @if (isset($textData['funny_text']))
                    <div class="text-center mb-3">
                        <p class="">
                            @foreach ($textData['funny_text'] as $text)
                                {{ \Rabbit::zg2uni($text) }} <br>
                            @endforeach
                        <p>

                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-center">
            <div class="text-center">
                <img width="70%" src="{{ $imageUrl }}">
            </div>
        </div>

        <div class="row justify-content-center mt-2">
            <a href="{{ $facebookShareUrl }}&tryButton=show" class="btn btn-lg btn-social btn-facebook m-2">
                <i class="fa-solid fa-share"></i> Share to facebook
            </a>
            @if (isset(request()->tryButton))
                <a href="{{ url('auth/facebook') }}" class="btn btn-lg btn-social btn-facebook m-2">
                    <i class="fa-brands fa-facebook-f"></i> Login to play
                </a>
            @endif
        </div>
    </div>
</body>

</html>

@extends('errors::minimal')

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title>500 Server Error</title>

    <!-- Google font -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,900" rel="stylesheet">

    <!-- Custom stlylesheet -->
    @vite(['resources/css/error.css'])

</head>

<body>
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>500</h1>
                <h2>Server Error</h2>
            </div>
            <a href="{{ route('home') }}"></a>
        </div>
    </div>
</body>

</html>


@extends('errors::minimal')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>429 Too Many Request</title>
    @vite(['resources/css/error.css'])
</head>
<body>
    <div id="notfound">
        <div class="notfound">
            <div class="notfound-404">
                <h1>429</h1>
                <h2>Too many request</h2>
            </div>
            <a href="{{ route('home') }}">Take a chill pill</a>
        </div>
    </div>
</body>
</html>


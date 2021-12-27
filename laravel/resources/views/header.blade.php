<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <title>siev20~laravel</title>
</head>
<body>
    <header>
        <nav>
            <a href={{ url("/") }} class="{{ request()->path() == "/" ? 'bg-sky-700' : 'bg-sky-500 hover:bg-sky-700' }}"><span class="fas fa-home"></span></a>
            {{-- <a href={{ url("/register") }} class={{ request()->path() == "register" ? 'active' : '' }}>Register</a> --}}
            {{-- <a href={{ url("/login") }} class={{ request()->path() == "cities" ? 'active' : '' }}><span class="fas fa-sign-in-alt"></span>Login</a> --}}
            <a href={{ url("/cities") }} class="{{ request()->path() == "cities" ? 'bg-sky-700' : 'bg-sky-500 hover:bg-sky-700' }}"><span class="fas fa-city"></span></a>
            <a href={{ url("/customers") }} class="{{ request()->path() == "customers" ? 'bg-sky-700' : 'bg-sky-500 hover:bg-sky-700' }}"><span class="fas fa-users"></span></a>
            <a href={{ url("/bikes") }} class="{{ request()->path() == "bikes" ? 'bg-sky-700' : 'bg-sky-500 hover:bg-sky-700' }}"><span class="fas fa-bicycle"></span></a>
        </nav>
    </header>
<main>
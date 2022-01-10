<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" rel="stylesheet">
    <link href="{{asset('css/app.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.min.css" 
    integrity="sha512-fYyZwU1wU0QWB4Yutd/Pvhy5J1oWAwFXun1pt+Bps04WSe4Aq6tyHlT4+MHSJhD8JlLfgLuC4CbCnX5KHSjyCg==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js" 
    integrity="sha512-OFs3W4DIZ5ZkrDhBFtsCP6JXtMEDGmhl0QPlmWYBJay40TT1n3gt2Xuw8Pf/iezgW9CdabjkNChRqozl/YADmg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.4.0/socket.io.js" 
    integrity="sha512-nYuHvSAhY5lFZ4ixSViOwsEKFvlxHMU2NHts1ILuJgOS6ptUmAGt/0i5czIgMOahKZ6JN84YFDA+mCdky7dD8A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <title>E-bike</title>
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
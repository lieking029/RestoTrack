<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{  'Admin - ePickMeUp' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/epickmeup-logo.png') }}">

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --topbar-h: 56px;
        }

        .topbar {
            position: sticky;
            top: 0;
            z-index: 1030;
        }

        .sidebar-sticky {
            position: sticky;
            top: var(--topbar-h);
        }

        .vh-100-minus-top {
            height: calc(100vh - var(--topbar-h));
        }

        .user-card .dropdown-menu {
            position: absolute !important;
        }
    </style>
</head>

<body>
    @include('sweetalert::alert')
    <div class="container-fluid" style="background-color: #f5f6fa;">
        <div class="row flex-nowrap">
            <div style="max-width: 260px">
                @include('layouts.components.sidebar')
                @include('layouts.components.nav')
            </div>

            <main class="col mx-3 mt-5 pt-5" >
                @yield('content')
            </main>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @stack('scripts')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js" type="module"></script>
</body>

</html>

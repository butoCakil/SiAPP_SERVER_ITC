<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SiAPP') | SiAPP</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/css/adminlte.min.css">
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                        <i class="fas fa-user-circle mr-1"></i>
                        {{ session('admin_nama') }}
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </button>
                        </form>
                    </div>
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" class="brand-link">
                <i class="fas fa-wifi brand-image ml-3 elevation-3"
                    style="font-size:24px; opacity:.8; line-height:1.8;"></i>
                <span class="brand-text font-weight-light">SiAPP</span>
            </a>
            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-white ml-1"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ session('admin_nama') }}</a>
                    </div>
                </div>
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <li class="nav-item">
                            <a href="{{ route('dashboard') }}"
                                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('device') }}"
                                class="nav-link {{ request()->routeIs('device') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-mobile-alt"></i>
                                <p>Device <span class="badge badge-success badge-pill right" id="badge-online">-</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview {{ request()->routeIs('presensi*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->routeIs('presensi*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-th"></i>
                                <p>Presensi <i class="right fas fa-angle-left"></i></p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('presensi') }}"
                                        class="nav-link {{ request()->routeIs('presensi') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Harian</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('presensi.event') }}"
                                        class="nav-link {{ request()->routeIs('presensi.event') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Pembiasaan</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('siswa') }}"
                                class="nav-link {{ request()->routeIs('siswa*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Siswa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('setting') }}"
                                class="nav-link {{ request()->routeIs('setting*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cog"></i>
                                <p>Setting</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link text-left w-100">
                                    <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                                    <p class="text-danger">Logout</p>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                                <li class="breadcrumb-item active">@yield('page_title', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content">
                <div class="container-fluid">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <strong>SiAPP</strong> &mdash; Sistem Informasi Absensi Presensi Pintar
            <div class="float-right d-none d-sm-inline-block">
                <b>Laravel</b> 11
            </div>
        </footer>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.2.0/js/adminlte.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    @stack('scripts')
</body>

</html>

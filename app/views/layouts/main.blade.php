<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>@yield('title', 'RBGS - Royal Borough of Greenwich Sitting Services');</title>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/font-awesome.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/input.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/input-theme.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-base.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-buttons.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-grids.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-menus.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-forms.min.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/pure-extras.css"/>
        <link media="all" type="text/css" rel="stylesheet" href="/~nt206/public/css/style.css"/>
    </head>
    <body>
        <div id="layout" class="pure-g">
            <div id="nav" class="pure-u">
                <a href="#" class="nav-menu-button">Menu</a>

                <div class="nav-inner">
                    <div class="logo" >
                        <a href="{{URL::to('/')}}">
                            <span class="fa-stack fa-lg" style="text-align: center;">
                                <i class="fa fa-home fa-stack-2x logo-pos-1"></i>
                                <i class="fa fa-car fa-stack-1x text-danger logo-pos-2"></i>
                            </span>
                            <span class="logo-text sr-only">RBGSitting - Home</span>
                        </a>
                    </div>
                    <div class="logo-button">
                        @if (!Auth::check())
                        {{ HTML::linkRoute('user.login', 'Login', array()) }}
                        @else
                        <span class="login-left">{{empty(Auth::user()->name) ? Auth::user()->username : Auth::user()->name}}</span>

                        <span class="login-right user-button-2"><a href="{{URL::route('user.logout');}}">
                                <i class="fa fa-sign-out"></i></a></span>
                        <span class="login-right user-button-1"><a href="{{URL::route('user.settings');}}">
                                <i class="fa fa-cogs"></i></a></span>
                        @endif
                    </div>
                    @yield('sidebar-top')
                    <div class="pure-menu pure-menu-open">
                        <ul>
                            <li></li>
                            @yield('sidebar-menu')
                        </ul>
                    </div>
                </div>
            </div>

            @if (Session::has('success'))
            <div class="container pure-u-1 pure-u-md-1-1 pure-alert pure-alert-success">
                @foreach(Session::get('success') as $message)
                <p>{{$message}}</p>
                @endforeach
            </div>
            @endif
            @if(Session::has('warning'))
            <div class="container pure-u-1 pure-u-md-1-1pure-alert pure-alert-warning">
                @foreach(Session::get('warning') as $message)
                <p>{{$message}}</p>
                @endforeach
            </div>
            @endif
            @if(Session::has('error'))
            <div class="container pure-u-1 pure-u-md-1-1 pure-alert pure-alert-error">
                @foreach(Session::get('error') as $message)
                <p>{{$message}}</p>
                @endforeach
            </div>
            @endif
            @if(Session::has('message'))
            <div class="container pure-u-1 pure-u-md-1-1 pure-alert">
                @foreach(Session::get('message') as $message)
                <p>{{$message}}</p>
                @endforeach
            </div>
            @endif

            <div class="main pure-u-1">
                @yield('content')
            </div>
        </div>
        <script type="text/javascript" src="/~nt206/public/js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="/~nt206/public/js/jquery.validate.min.js"></script>
        <script type="text/javascript" src="/~nt206/public/js/additional-methods.min.js"></script>
        <script type="text/javascript" src="/~nt206/public/js/jquery.validate.laravel.js"></script>
        @yield('scripts')
    </body>
</html>
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/imageviewer.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
    @yield('css')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @if (Auth::guest())
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <!-- <li><a href="{{ route('register') }}">Register</a></li> -->
                        @else
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('change_pw') }}">
                                            Change Password
                                        </a>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                            @if (Auth::user()->role == 'admin')                            
                            <li><a href="{{ route('register') }}">Register</a></li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
        </nav>        
        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/imageviewer.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>    
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDViaUZiCsi7LfCkwkdpLRT4AmWzWP9CnM&libraries=places,geometry&callback=initAutocomplete" async defer></script>
    <script type="text/javascript">
        var initAutocomplete = function () {}
    </script>
    <script type="text/javascript">
        var apiGeolocationSuccess = function(position) {
            //alert("API geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
             
            window.current_lat = position.coords.latitude
            window.current_lng = position.coords.longitude   

            $.post('http://115.146.126.84/api/locationServices/pushGDVLocation', {position: position, gdv_id: "{{ isset(Auth::user()->gdv_id) ? Auth::user()->gdv_id : '' }}"}, function (data) {
                if (data.status == 1) {
                    $("#noti").show()   
                } else {
                    $("#failnoti").show()   
                }
                $("#sending").hide()
                
            })
            .fail(function(err) {
                console.log('failed')
            })
        };

        var tryAPIGeolocation = function() {
            jQuery.post( "https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyDViaUZiCsi7LfCkwkdpLRT4AmWzWP9CnM", function(success) {
                apiGeolocationSuccess({coords: {latitude: success.location.lat, longitude: success.location.lng}});
          })
          .fail(function(err) {
            console.log("API Geolocation error! \n\n"+err);
          });
        };

        var browserGeolocationSuccess = function(position) {
            //alert("Browser geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
            apiGeolocationSuccess(position);
        };

        var browserGeolocationFail = function(error) {
          let msg = ''
          switch (error.code) {
            case error.TIMEOUT:
              msg = "Browser geolocation error !\n\nTimeout.";
              break;
            case error.PERMISSION_DENIED:               
              // if(error.message.indexOf("Only secure origins are allowed") == 0) {
              //   tryAPIGeolocation();
              // }
              tryAPIGeolocation();
              break;
            case error.POSITION_UNAVAILABLE:
              msg = "Browser geolocation error !\n\nPosition unavailable.";
              break;
          }          
        };

        var tryGeolocation = function() {
          if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                browserGeolocationSuccess,
              browserGeolocationFail,
              {maximumAge: 50000, timeout: 20000, enableHighAccuracy: true});
          }
        };
        if ("{{ !Auth::guest() }}" == "1") {
            tryGeolocation();    
        }        

    </script>
    @yield('javascript')
</body>
</html>

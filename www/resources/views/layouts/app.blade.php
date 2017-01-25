<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <style>
        body {
            font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
        }
        
        .content {
            text-align: left;
        }
        
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 12px !;
            font-weight: 400;
            letter-spacing: .1rem;
            text-decoration: none;
        }
        
        .links > a:hover {
            color: #22b8eb;
            text-decoration: underline;
        }
        
        .links > a.selected {
            color: #22b8eb;
            text-decoration: underline;
        }
        
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <!-- Scripts -->
    </style>
    <script>
    window.Laravel = <?php echo json_encode([
        'csrfToken' => csrf_token(),
        ]); ?>
    </script>
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
                        {{ config('app.name', 'Dohmh') }}
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        &nbsp;
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="{{ url('/thaifood') }}">Thai Food</a></li>
                        <li><a href="{{ url('/bestplaces') }}">Best Places</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        @yield('content')
    </div>
    <!-- Scripts -->
    <script src="/js/app.js"></script>
    @if(Route::current()->getName() == 'best-places')
    <script>
    function initMap() {
        var newYork = {
            lat: 40.705565,
            lng: -74.1180854
        };
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: newYork
        });

        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: 'roadmap'
        };

        console.log('getting data');

        $.getJSON("/bestplacesmap", {
            format: "json"
        }).done(function(data) {
            // Multiple Markers
            var markers = data.markers;

            // Info Window Content
            var infoWindowContent = data.infoWindow;

            // Display multiple markers on a map
            var infoWindow = new google.maps.InfoWindow(),
                marker, i;

            // Loop through our array of markers & place each one on the map  
            for (i = 0; i < markers.length; i++) {
                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                bounds.extend(position);
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: markers[i][0]
                });

                // Allow each marker to have an info window    
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infoWindow.setContent(infoWindowContent[i][0]);
                        infoWindow.open(map, marker);
                    }
                })(marker, i));

                // Automatically center the map fitting all markers on the screen
                map.fitBounds(bounds);
            }

            // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
                this.setZoom(11);
                google.maps.event.removeListener(boundsListener);
            });
        });
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"></script>
    @endif 
    @if(Route::current()->getName() == 'thai-food' || Route::current()->getName() == 'home')
    <script>
    function initMap() {
        var newYork = {
            lat: 40.705565,
            lng: -74.1180854
        };
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 5,
            center: newYork
        });

        var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
            mapTypeId: 'roadmap'
        };

        console.log('getting data');

        $.getJSON("/thaifoodmap", {
            format: "json"
        }).done(function(data) {
            // Multiple Markers
            var markers = data.markers;

            // Info Window Content
            var infoWindowContent = data.infoWindow;

            // Display multiple markers on a map
            var infoWindow = new google.maps.InfoWindow(),
                marker, i;

            // Loop through our array of markers & place each one on the map  
            for (i = 0; i < markers.length; i++) {
                var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
                bounds.extend(position);
                marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: markers[i][0]
                });

                // Allow each marker to have an info window    
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() {
                        infoWindow.setContent(infoWindowContent[i][0]);
                        infoWindow.open(map, marker);
                    }
                })(marker, i));

                // Automatically center the map fitting all markers on the screen
                map.fitBounds(bounds);
            }

            // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
            var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
                this.setZoom(11);
                google.maps.event.removeListener(boundsListener);
            });
        });
    }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&callback=initMap"></script>
    @endif
    @if(Route::current()->getName() == 'show-restaurant-data')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script>
        var ctx = document.getElementById("myChart");

        var data = {
            labels: <?php echo json_encode($chartTitle); ?>,
            datasets: [
                {
                    label: "Score",
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: "rgba(75,192,192,0.4)",
                    borderColor: "rgba(75,192,192,1)",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "rgba(75,192,192,1)",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(75,192,192,1)",
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: <?php echo json_encode($chartData); ?>,
                    spanGaps: false,
                }
            ]
        };

        var options =  {
            scales: {
                xAxes: [{
                    display: true
                }]
            }
        }

        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: options
        });
    </script>
    @endif
</body>

</html>

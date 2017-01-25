@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="pull-left">
                        Best Places
                    </h4>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div id="map"></div>
                    <table class="table">
                        <thead class="thead-default">
                            <tr>
                                <th>#</th>
                                <th>Restaurant</th>
                                <th>Inspection Count</th>
                                <th>Street</th>
                                <th>Zip</th>
                                <th>Grade</th>
                                <th>Score</th>
                                <th>Inspection Date</th>
                            </tr>
                        </thead>
                        <?php $i = 1; ?>
                        <tbody>
                            @foreach($restaurants as $place)
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td><a href="{{ route('show-restaurant-data', $place['dba']) }}">{{ $place['dba'] }}</a></td>
                                <td>{{ $place['inspections'] }}</td>
                                <td>{{ $place['street'] }}</td>
                                <td>{{ $place['zip'] }}</td>
                                <td>{{ $place['grade'] }}</td>
                                <td>{{ $place['score'] }}</td>
                                <td>{{ $place['inspection-date'] }}</td>
                            </tr>
                            <?php ++$i; ?> @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
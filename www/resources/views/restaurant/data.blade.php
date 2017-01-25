@extends('layouts.app') 

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="pull-left">
                        {{ $restaurant }} Recent Inspections
                    </h4>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <canvas id="myChart" width="400" height="100"></canvas>
                    <br />
                    <table class="table">
                        <thead class="thead-default">
                            <tr>
                                <th>#</th>
                                <th>Grade</th>
                                <th>Score</th>
                                <th>Inspection Date</th>
                                <th>Violation Code</th>
                                <th>Violation Description</th>
                            </tr>
                        </thead>
                        <?php $i = 1; ?>
                        <tbody>
                         @if(!empty($restaurantData))
                            @foreach($restaurantData as $place)
                            <tr>
                                <td scope="row">{{ $i }}</td>
                                <td>{{ $place['grade'] }}</td>
                                <td>{{ $place['score'] }}</td>
                                <td>{{ $place['inspection-date'] }}</td>
                                <td>{{ $place['violation-code'] }}</td>
                                <td style="max-width: 500px">{{ $place['violation-description'] }}</td>
                            </tr>
                            <?php ++$i; ?>
                            @endforeach
                         @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

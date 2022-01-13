@include('header')

{{-- @if (isset($logs)) --}}

    <table class="table-auto">
        <th>Log ID</th>
        <th>Bike ID</th>
        <th>User ID</th>
        <th>Start Point</th>
        <th>End Point</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Total</th>
        <th>Cost</th>
        <th>Paid</th>
        @foreach ($logs as $log)
        <tr class="bg-emerald-200">
            <td>{{$log->log_id}}</td>
            <td>{{$log->bike_id}}</td>
            <td>{{$log->user_id}}</td>
            <td>{{$log->start_point}}</td>
            <td>{{$log->end_point}}</td>
            <td>{{$log->start_time}}</td>
            <td>{{$log->end_time}}</td>
            <td>{{$log->travel_time}}s</td>
            <td>{{$log->cost}}</td>
            <td>{{$log->paid}}</td>
        </tr>
        @endforeach
    </table>
{{-- @endif --}}

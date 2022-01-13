<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative scrollable">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="flex-col items-center relative">
                    <h1 class="py-3 text-xl font-semibold sticky-top-title">History - #{{ $user }}</h1>
<table>
    <tr>
        <th>ID</th>
        <th>Start Time</th>
        <th>Start Point</th>
        <th>End Time</th>
        <th>End Point</th>
        <th>Bike ID</th>
    </tr>
@foreach ($logs as $log)
<tr>
    <td>{{$log->log_id}}</td>
    <td>{{$log->start_time}}</td>
    <td>{{$log->start_point}}</td>
    <td>{{$log->end_time}}</td>
    <td>{{$log->end_point}}</td>
    <td>{{$log->bike_id}}</td>
</tr>    
@endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

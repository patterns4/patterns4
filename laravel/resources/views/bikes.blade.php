@include('header')

{{-- @if (isset($bikes)) --}}

    <table class="table-auto">
        <th>id</th>
        <th>position</th>
        <th>speed</th>
        <th>battery</th>
        <th>status</th>
        <th>state</th>
        <th>city name</th>
        @foreach ($bikes as $bike)
        <tr class="bg-emerald-200">
            <td> {{ $bike->bike_id }}</td>
            <td> {{ $bike->position }}</td>
            <td> {{ $bike->speed }}</td>
                 @if (($bike->battery) < 10)
                    <td style="background:red;"> {{ $bike->battery }}</td>
                @else
                    <td> {{ $bike->battery }}</td>
                @endif
            <td> {{ $bike->status }}</td>
            <td> {{ $bike->state }}</td>
            <td> {{ $bike->city_name }}</td>
        </tr>
        @endforeach
    </table>
{{-- @endif --}}

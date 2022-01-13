<x-app-layout>

    <div class="w-full mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative scrollable scroll-x">
            <div class="w-full mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="flex-col items-center relative">
                    <h1 class="py-3 text-xl font-semibold sticky-top-title">History - #{{ $user }}</h1>
                    <form method="POST" class="w-full">
                        @csrf <!-- {{ csrf_field() }} -->
                        <input name="user_id" type="hidden" value="{{ Auth::user()->user_id}}">
                        <table class="w-full">
                            <tr>
                                <th style="text-align:center">#</th>
                                <th style="text-align:center">Bike ID</th>
                                <th style="text-align:center">Start Point</th>
                                <th style="text-align:center">End Point</th>
                                <th style="text-align:center">Start Time</th>
                                <th style="text-align:center">End Time</th>
                                <th style="text-align:center">Total</th>
                                <th style="text-align:center">Cost</th>
                                @if(Auth::user()->payment === "Invoice")
                                <th style="text-align:center"></th>
                                @endif
                            </tr>
                        @foreach ($logs as $log)
                        <tr>
                            <td>{{$log->log_id}}</td>
                            <td>{{$log->bike_id}}</td>
                            <td>{{$log->start_point}}</td>
                            <td>{{$log->end_point}}</td>
                            <td>{{$log->start_time}}</td>
                            <td>{{$log->end_time}}</td>
                            <td>{{$log->travel_time}}s</td>
                            <td>{{number_format($log->cost, 2)}} kr</td>
                            @if(Auth::user()->payment === "Invoice")
                            @if($log->paid == 0)
                            <input name="cost{{$log->log_id}}" type="hidden" value="{{ $log->cost }}">
                            <td><button formaction="history/pay/{{$log->log_id}}" type="submit" class="block bg-blue-500 my-3 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-auto">Pay</button></td>
                            @else
                            <td><button type="button" class="block bg-blue-200 my-3 text-white font-bold py-2 px-4 rounded mx-auto" disabled>Paid</button></td>
                            @endif
                            @endif
                        </tr>    
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

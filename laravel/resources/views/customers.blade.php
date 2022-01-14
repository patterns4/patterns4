@include('header')
<div class="flex justify-center w-full">
<form>
    @csrf <!-- {{ csrf_field() }} -->
    <input name="user_search" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" placeholder="search user">
    <div class="w-full flex justify-center">
    <button type="submit" class="mr-2 block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Search</button>
    </div>
</form>
</div>

{{-- @if (isset($customers)) --}}

    <table class="table-auto">
        <th>id</th>
        <th>name</th>
        <th>phone</th>
        <th>email</th>
        <th>balance</th>
        <th>manage</th>
        @foreach ($customers as $customer)
        <tr class="bg-emerald-200">
            <td> {{ $customer->user_id }}</td>
            <td class="p-8">{{ $customer->first_name }} {{ $customer->last_name }}</td>
            <td> {{ $customer->phone }}</td>
            <td> {{ $customer->email }}</td>
            <td> {{ $customer->saldo }}</td>
            <td> <a href="customers/{{$customer->user_id }}"><i class="fas fa-edit"></i></a></td>
        </tr>
        @endforeach
    </table>
{{-- @endif --}}

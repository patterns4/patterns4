@include('header')
<<<<<<< HEAD
@php
    var_dump(request()->url());
@endphp
=======

>>>>>>> Dockerized3
<div class="flex justify-center w-full">
<form method="POST" class="w-min">
    @csrf <!-- {{ csrf_field() }} -->
    <input name="first_name" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->first_name}}">
    <input name="last_name" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->last_name}}">
    <input name="email" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->email}}">
    <input name="birth_year" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->birth_year}}">
    <input name="phone" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->phone}}">
    <input name="payment" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->payment}}">
    <input name="saldo" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="text" value="{{$customer->saldo}}">
    <div class="w-full flex justify-center">
    
        <input type="hidden" value="{{$customer->user_id}}" name="user_id">
        <button formaction="customer/save" type="submit" class="mr-2 block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save</button>
        <button formaction="customer/delete" type="submit" class="ml-2 block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Delete</button>

    </div>
</form>
</div>
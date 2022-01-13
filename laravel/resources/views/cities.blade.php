@include('header')

<div class="w-full flex justify-center">
@foreach ($cities as $city)
    <div class="w-2/6 bg-teal-600 rounded text-gray-50 p-5 m-1">
        <a href="/cities/{{ $city->city_id }}" class="w-full flex justify-center">
        <span class="mr-2 fas fa-city text-2xl"></span>
        <span class="text-center text-2xl">{{ $city->city_name }}</span>
        </a>
    </div>
@endforeach
</div>
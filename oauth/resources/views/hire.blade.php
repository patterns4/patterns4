<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">

                    <h1 class="py-3 text-xl font-semibold">Hiring</h1>
                    @foreach ($cities as $city)
                    <div class="w-2/6 bg-teal-600 rounded text-gray-50 p-5 m-1">
                        <a href="/hire/{{ $city->city_id }}" class="w-full flex justify-center">
                        <span class="mr-2 fas fa-city text-2xl"></span>
                        <span class="text-center text-2xl">{{ $city->city_name }}</span>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

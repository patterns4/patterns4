@include('header')

<h1>{{ json_decode($city)->city_name }}</h1>

<div class="w-full grid grid-cols-2">
    <div id="map" class="mb-4"></div>
    <div class="w-full grid justify-center">
        <div class="w-full grid gap-y-6 justify-center grid-rows-8 h-3/6">
            <div class="cursor-pointer w-full h-12 bg-blue-500 text-white font-bold py-2 px-4 rounded justify-center flex items-center" id="lediga">Lediga</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="hyrda">Hyrda</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="parked">Parkerade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="depleted">Urladdade</div>
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold rounded justify-center flex items-center" id="parking">Parkering</div>
            <input id="bike_search" class="w-auto m-2 block box-border p-4 border-2 border-purple-300 rounded" type="number" placeholder="cykel ID">
            <div class="cursor-pointer shadow w-full h-12 bg-blue-500 text-white font-bold px-4 rounded justify-center flex items-center" id="search">Sök</div>
            <form class="w-full" method="POST" action="addparking">
                @csrf <!-- {{ csrf_field() }} -->
                <h4>Lägg till parkeringsplats</h4>
                <input type="hidden" value={{ json_decode($city)->city_id }} name="redirect_url">
                <input type="hidden" value='{{ json_decode($city)->city_name }}' name="city_name">
                <input type="text" required class="w-auto m-4 block box-border p-4 border-2 border-purple-300 rounded" name="parking_name" placeholder="Välj namn">
                <input id="newspot" required type="text" class="w-auto m-4 block box-border p-4 border-2 border-purple-300 rounded" name="parking_position" placeholder="Välj plats på kartan">
                <button type="submit" class="w-full h-12 mr-2 block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Lägg till</button>
            </form>
        </div>
    </div>
</div>
<h2>Parkeringsplatser</h2>
<table class="table-auto">
    @foreach (json_decode($parking) as $spot)
        <tr>
            <td> {{ $spot->parking_name }} </td>
            <td> {{ $spot->position }}  </td>
            <td> {{ $spot->parking_id }} </td>
            <td>
                <form method="POST" action="deleteparking">
                    @csrf <!-- {{ csrf_field() }} -->
                    <input type="hidden" name="city_id" value={{ json_decode($city)->city_id }}>
                    <input type="hidden" name="parking_id" value={{ $spot->parking_id }}>
                    <button type="submit" action>
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</table>

<script>
    const city = JSON.parse('<?= $city; ?>');
    const parking = JSON.parse('<?= $parking ?>');
    const freeBikesBtn = document.getElementById("lediga");
    const movingBtn = document.getElementById("hyrda");
    const parkingBtn = document.getElementById("parking");
    const searchBtn = document.getElementById("search");
    const searchField = document.getElementById("bike_search");
    const parkedBikesBtn = document.getElementById("parked");
    const depletedBikesBtn = document.getElementById("depleted");
    const newParkingSpot = document.getElementById("newspot");
    const classListActive = ["bg-blue-500", "bg-blue-700"];
    const classListHide = ["bg-blue-700", "bg-blue-500"];

    let bikeData;
    let trackMovingBikes = false;
    let trackFreeBikes = false;
    let trackDepletedBikes = false;
    let trackParkedBikes = false;
    let newSpot = L.marker();

    const bikeMarkers = {};
    const bikePopups = {};
    const bikeLayer = L.layerGroup();
    const parkingSpots = L.layerGroup();
</script>

<script src="{{asset('js/citymap.js')}}"></script>
<script src="{{asset('js/cityfunctions.js')}}"></script>
<script src="{{asset('js/citysocket.js')}}"></script>

<script>
    freeBikesBtn.addEventListener("click", plotFreeBikes);
    movingBtn.addEventListener("click", plotMoving);
    searchBtn.addEventListener("click", searchBike);
    parkingBtn.addEventListener("click", plotParking);
    parkedBikesBtn.addEventListener("click", plotParkedBikes);
    depletedBikesBtn.addEventListener("click", plotDepletedBikes);
    prepParking();
</script>

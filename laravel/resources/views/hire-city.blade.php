<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">
                    <h1>{{ json_decode($city)->city_name }}</h1>
                    <div class="w-full">
                        <div id="mobile-map"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>

const city = JSON.parse('<?= $city; ?>');
const user = JSON.parse('<?= $user ?>');

let bikeData;
let trackMovingBikes = false;
let trackFreeBikes = false;
let trackDepletedBikes = false;
let trackParkedBikes = false;

const bikeMarkers = {};
const bikePopups = {};
const bikeLayer = L.layerGroup();
</script>

<script src="{{asset('js/citymapmobile.js')}}"></script>
<script src="{{asset('js/cityfunctionsmobile.js')}}"></script>
<script src="{{asset('js/citysocketmobile.js')}}"></script>

</x-app-layout>

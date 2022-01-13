<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">
                    <h1 class="py-3 text-xl font-semibold">Payment method</h1>
                    <div>
                        <div class="flex gap-3">
                            <div>
                                <label class="labl">
                                    <input type="radio" name="payment_method" value="prepaid" checked="checked"/>
                                    <div>Prepaid</div>
                                </label>
                            </div>
                            <div>
                                <label class="labl">
                                    <input type="radio" name="payment_method" value="monthly"/>
                                    <div>Monthly</div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button class="button my-3">Change</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

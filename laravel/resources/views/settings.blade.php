<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">
                    <div class="flex-col">
                        <p>
                            Name: {{ Auth::user()->name }}
                        </p>
                        <p>
                            Email: {{ Auth::user()->email }}
                        </p>
                    </div>
                   
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
                 <form method="POST" action="{{ route('logout') }}" class="py-3 my-3">
                        @csrf

                        <x-jet-dropdown-link href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-jet-dropdown-link>
                    </form>
            </div>
        </div>
    </div>
</x-app-layout>

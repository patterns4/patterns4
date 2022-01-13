<x-app-layout>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg relative">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 text-center h-80">
                <div class="main-flex items-center">
                    <div class="flex-col">
                        <p>
                            Name: {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </p>
                        <p>
                            Email: {{ Auth::user()->email }}
                        </p>
                        <p>
                            Number: {{ Auth::user()->phone }}
                        </p>
                        <p>
                            Birth year: {{ Auth::user()->birth_year }}
                        </p>
                        <p>
                            Saldo: {{ Auth::user()->saldo }}
                        </p>
                    </div>
                   
                    <div>
                        <h1 class="py-3 text-xl font-semibold">Payment method</h1>
                        <div class="flex gap-3">
                            <div>
                                <label class="labl">
                                    @if (Auth::user()->payment === "Invoice")
                                    <input type="radio" name="payment_method" value="prepaid" checked/>
                                    @else
                                    <input type="radio" name="payment_method" value="prepaid"/>
                                    @endif
                                    <div>Prepaid</div>
                                </label>
                            </div>
                            <div>
                                <label class="labl">
                                    @if (Auth::user()->payment === "Monthly")
                                    <input type="radio" name="payment_method" value="monthly" checked/>
                                    @else
                                    <input type="radio" name="payment_method" value="monthly"/>
                                    @endif
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

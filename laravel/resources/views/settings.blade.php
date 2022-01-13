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
                            Saldo: {{ Auth::user()->saldo }} kr
                        </p>
                    </div>
                   
                    <div>
                        <h1 class="py-3 text-xl font-semibold">Payment method</h1>
                        <div class="flex gap-3">
                            <form method="POST" class="w-min">
                                @csrf <!-- {{ csrf_field() }} -->
                                <input name="user_id" type="hidden" value="{{ Auth::user()->user_id}}">
                                <input name="first_name" type="hidden" value="{{ Auth::user()->first_name}}">
                                <input name="last_name" type="hidden" value="{{ Auth::user()->last_name}}">
                                <input name="email" type="hidden" value="{{ Auth::user()->email}}">
                                <input name="birth_year" type="hidden" value="{{ Auth::user()->birth_year}}">
                                <input name="phone" type="hidden" value="{{ Auth::user()->phone}}">
                                <input name="saldo" type="hidden" value="{{ Auth::user()->saldo}}">

                                <div class="mb-3">
                                    <small>current: {{ Auth::user()->payment }}</small>
                                </div>
                                <div class="flex">
                                    <label class="labl">
                                        @if (Auth::user()->payment === "Invoice")
                                        <input type="radio" name="payment" value="Invoice" checked/>
                                        @else
                                        <input type="radio" name="payment" value="Invoice"/>
                                        @endif
                                        <div>Invoice</div>
                                    </label>
                                    
                                    <label class="labl">
                                        @if (Auth::user()->payment === "Monthly")
                                        <input type="radio" name="payment" value="Monthly" checked/>
                                        @else
                                        <input type="radio" name="payment" value="Monthly"/>
                                        @endif
                                        <div>Monthly</div>
                                    </label>
                                </div>

                                <button formaction="settings/update" type="submit" class="block bg-blue-500 my-3 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-auto">Change</button>
                            </form>

                        </div>
                    </div>
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

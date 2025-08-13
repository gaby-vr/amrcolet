<x-guest-layout>

@push('styles')
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="{{ asset('fonts/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/sweetalert/sweetalert.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/form-wizard.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/form-home.css') }}">
<style>
    .gradient {
        background: linear-gradient(90deg, #0038cc 0%, #0038cc 100%);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('fonts/fontawesome/js/all.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/vendors.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
<script src="{{ asset('js/pages/form-home.js') }}"></script>
@endpush

    <x-jet-banner />
    @livewire('navigation-menu')
    <!--Hero-->
    <section class="border-b py-8 bg-white" style="min-height: calc(100vh - 289px);">
      	<div class="max-w-7xl mx-auto m-8">
	        <div class="w-full mb-4">
	          	<div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
	        </div>
	        <div class="flex flex-wrap">
		        <div class="p-6 text-black w-full">
		            <h3 class="text-3xl font-bold leading-none mb-3 pl-2">
		              	Cautare preturi pentru servicii de curierat
		            </h3>
		            <p class="pl-2">Preturile pot varia in functie de optiunile selectate la crearea unei comenzi.</p>
		            <div class="row">
                        <div class="curieri text-black">
                            {!! $curieriTable !!}
                        </div>
                    </div>
                    <div class="row text-center">
                    	<a href="{{ route('order.index') }}" class="mx-auto lg:mx-0 hover:underline inline-block bg-blue-700 text-white font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
			                Comanda acum
			            </a>
                    </div>
		      	</div>
		    </div>
		</div>
    </section>
    <!--Footer-->
    @livewire('footer')

</x-guest-layout>
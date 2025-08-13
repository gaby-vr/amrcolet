@yield('layouts.guest')

@push('styles')
{{-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" /> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/sweetalert/sweetalert.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/plugins/countrySelect/css/countrySelect.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/form-home.css') }}">
<style>
    .gradient {
        background: linear-gradient(90deg, #0038cc 0%, #0038cc 100%);
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/vendors/mat/vendors.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ asset('js/plugins/countrySelector/js/countrySelect.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script src="{{ asset('js/pages/form-home.js') }}"></script>
@if(session()->has('orderConfirmed') || isset($orderId) && isset($status))
	<script type="text/javascript">
		var el = document.createElement('div');
		@switch($status)
			@case('1')
				el.innerHTML = '{!! __('Veți primi un email de confirmare în care veți găsi atașată factura, <b>dacă plata a fost efectuată cu succes</b>.') !!}';
				swal({
						title: "{{ __('Comanda a fost confirmată') }}",
						content: el,
						icon: "{{ asset('img/logo.png') }}"
					});
				@break

			@case('2')
				el.innerHTML = '{!! __('Comanda nu a putut fi efectuată. Vă rugăm reîncercați mai târziu.') !!}';
				swal({
						title: "{{ __('Comanda a fost anulată') }}",
						content: el,
						icon: "{{ asset('img/logo.png') }}"
					});
				@break
			@default
				el.innerHTML = '{!! __('Comanda nu a putut fi efectuată. Vă rugăm reîncercați mai târziu.') !!}';
				swal({
						title: "{{ __('Comanda a fost respinsă') }}",
						content: el,
						icon: "{{ asset('img/logo.png') }}"
					});
		@endswitch
	</script>
	@php session()->pull('orderConfirmed') @endphp
@endif
@if(session()->has('expiration_date_passed'))
	<script type="text/javascript">
		var el = document.createElement('div');
		el.innerHTML = '{!! __(session()->get('expiration_date_passed')) !!}'
		swal({
				title: "{{ __('Comanda nu a fost trimisa') }}",
				content: el,
				icon: "error"
			});
	</script>
@endif
@isset($dataOrder)
	<script type="text/javascript">
		swal({
				title: "{{ __('Veți fi redirecționat în câteva momente.') }}",
				text: "",
				icon: "{{ asset('img/logo.png') }}"
			});
	</script>
@endisset
@endpush

<x-guest-layout>
    <x-jet-banner />
    @livewire('navigation-menu')
    <!--Hero-->
    <div class="pt-16 max-w-7xl mx-auto">
        <div class="px-3 py-5 mx-auto flex flex-wrap flex-col md:flex-row items-center">
            <!--Left Col-->
            <div class="flex flex-col w-full md:w-2/5 justify-center items-start text-center text-white md:text-left">
              <p class="uppercase tracking-loose w-full">Ai de trimis un plic/colet?</p>
              <h1 class="my-4 text-5xl font-bold leading-tight">
                Suntem aici !
                </h1>
                <p class="leading-normal text-2xl mb-8">
                    <b>Trimite colete prin comandă online rapid și ieftin oriunde în</b> România. De ce să ne alegi pe noi? Pentru că îți oferim:
                </p>
                <div class="text-left justify-center md:justify-start flex flex-col items-center w-full sm:flex-row prose">
	                {{-- <button class="mx-0 hover:underline bg-white text-gray-800 font-bold rounded-full my-3 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
	                Subscribe
	                </button>
	                <span class="inline mx-4">or</span>
	                <button class="mx-0 hover:underline border border-white text-white bg-transparent hover:text-gray-800 font-bold rounded-full my-3 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:bg-white hover:scale-105 duration-300 ease-in-out">
	                Subscribe
	                </button> --}}
	                <ul class="text-white">
	                	<li>Taxă 0 lei pentru rambursurile în cont;</li>
	                	<li>Taxă 0 lei pentru kilometri suplimentari;</li>
	                	<li>Taxă 0 lei pentru retur (colet nelivrat);</li>
	                	<li>Taxă 0 lei pentru recântărire;</li>
	                	<li>Pungi gratis pentru trimiterile prin DPD sau Cargus;</li>
	                	<li>Posibilitate contract persoana fizică/persoană juridică;</li>
	                	<li>Primirea rambursurilor în 48 h de la livrare.</li>
	                </ul>
	            </div>
            </div>
            <!--Right Col-->
            <div class="w-full md:w-3/5 py-6 text-center">
                <img class="w-full md:w-4/5 z-50" src="{{ asset('img/hero.png') }}" />
            </div>
        </div>
    </div>
    <div class="relative -mt-12 lg:-mt-24">
      <svg viewBox="0 0 1428 174" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
          <g transform="translate(-2.000000, 44.000000)" fill="#FFFFFF" fill-rule="nonzero">
            <path d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496" opacity="0.100000001"></path>
            <path
              d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
              opacity="0.100000001"
            ></path>
            <path d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z" id="Path-4" opacity="0.200000003"></path>
          </g>
          <g transform="translate(-4.000000, 76.000000)" fill="#FFFFFF" fill-rule="nonzero">
            <path
              d="M0.457,34.035 C57.086,53.198 98.208,65.809 123.822,71.865 C181.454,85.495 234.295,90.29 272.033,93.459 C311.355,96.759 396.635,95.801 461.025,91.663 C486.76,90.01 518.727,86.372 556.926,80.752 C595.747,74.596 622.372,70.008 636.799,66.991 C663.913,61.324 712.501,49.503 727.605,46.128 C780.47,34.317 818.839,22.532 856.324,15.904 C922.689,4.169 955.676,2.522 1011.185,0.432 C1060.705,1.477 1097.39,3.129 1121.236,5.387 C1161.703,9.219 1208.621,17.821 1235.4,22.304 C1285.855,30.748 1354.351,47.432 1440.886,72.354 L1441.191,104.352 L1.121,104.031 L0.457,34.035 Z"
            ></path>
          </g>
        </g>
      </svg>
    </div>
    <section class="bg-white border-b py-8">
    	<div class="container max-w-5xl mx-auto m-8">
    		<div class="flex flex-wrap my-8 mb-4 justify-center">
          		<div class="sm:w-1/3 md:w-1/4 lg:w-1/5 mb-4 px-2">
          			<div class="rounded-md mx-auto gradient p-4" style="height: 180px; max-width: 180px;">
	          			<div class="bg-white py-4 rounded shadow-md">
	          				<img class="rounded mx-auto" src="{{ asset('img/cargus-logo.png') }}" style="max-width: 100px;" alt="DPD">
	          			</div>
	          			{{-- <p class="text-center text-white text-lg mt-2"><b>From 12.32 lei</b></p> --}}
	          		</div>
          		</div>
		        <div class="sm:w-1/3 md:w-1/4 lg:w-1/5 mb-4 px-2">
          			<div class="rounded-md mx-auto gradient p-4" style="height: 180px; max-width: 180px;">
	          			<div class="bg-white py-4 rounded shadow-md">
	          				<img class="rounded mx-auto" src="{{ asset('img/dpd-logo.png') }}" style="max-width: 100px;" alt="DPD">
	          			</div>
	          			{{-- <p class="text-center text-white text-lg mt-2"><b>From 12.32 lei</b></p> --}}
	          		</div>
          		</div>
	        </div>
	        <div class="text-center mb-8">
	        	<p class=""><b>Partenerii noștri sunt: Cargus și DPD</b></p>
	        	<a href="{{ route('register') }}" class="mx-auto lg:mx-0 hover:underline inline-block gradient text-white font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
                {{ __('Înregistreaza-te') }}
            </a>
	        </div>
	        <div class="px-3 mx-auto flex flex-wrap flex-col md:flex-row items-center">
	            <!--Left Col-->
	            <div class="w-full md:w-3/5 py-6 text-center">
	                <img class="w-full md:w-4/5 z-50" src="https://colete.smartzao.eu/img/hero.png">
	            </div>
	            <!--Right Col-->
	            <div class="flex flex-col w-full md:w-2/5 justify-center items-start text-center md:text-left">
	              	{{-- <h2 class="mt-4 mb-0 text-3xl font-bold leading-tight">
	                Lorem ipsum dolor sit amet, consectetur adipiscing elit,
	                </h2> --}}
	                <p class="leading-normal text-xl mb-4">
	                    Alege cea mai bună ofertă pentru tine! Plasând comanda online vei începe să <b>economisești bani și timp</b> chiar de acum!
	                </p>
	                <p class="tracking-loose text-xl w-full mb-8">Cum funcționează platforma noastră?</p>
	                <ol class="list-decimal ml-5 text-lg">
	                	<li><span class="ml-3">Introduceți datele expeditorului;</span></li>
	                	<li><span class="ml-3">Introduceți datele destinatarului;</span></li>
	                	<li><span class="ml-3">Introduceți informații despre expediere;</span></li>
	                	<li><span class="ml-3">Alegeți curierul cel mai potrivit pentru această expediere;</span></li>
	                	<li><span class="ml-3">Predați coletul curierului și urmăriți expedierea lui în istoricul comenzilor;</span></li>
	                	<li><span class="ml-3">Economisiți timp și bani.</span></li>
	                </ol>
	                {{-- <table class="table-fixed w-full">
									  <thead>
									    <tr class="text-3xl">
									      <th class="w-3/7 text-center">Discount</th>
									      <th class="w-4/7">Sends</th>
									    </tr>
									  </thead>
									  <tbody>
									    <tr class="border-t text-3xl">
									      <td class="text-center py-2"><b>-5%</b></td>
									      <td><b>10-24</b></td>
									    </tr>
									    <tr class="border-t text-3xl">
									      <td class="text-center py-2"><b>-10%</b></td>
									      <td><b>25-49</b></td>
									    </tr>
									    <tr class="border-t text-3xl">
									      <td class="text-center py-2"><b>-13%</b></td>
									      <td><b>50-99</b></td>
									    </tr>
									    <tr class="border-t text-3xl">
									      <td class="text-center py-2"><b>-15%</b></td>
									      <td><b>100-499</b></td>
									    </tr>
									    <tr class="border-t text-3xl">
									      <td class="text-center py-2"><b>-18%</b></td>
									      <td><b> peste 500</b></td>
									    </tr>
									  </tbody>
									</table> --}}
	            </div>
	        </div>
	    </div>
    </section>
    <section class="border-b py-8">
      	<div class="container max-w-5xl mx-auto m-8">
	        <div class="w-full mb-4">
	          	<div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
	        </div>
	        <div class="flex flex-wrap">
		        <div class="p-6 text-white w-full">
		            <h3 class="text-3xl font-bold leading-none mb-3 pl-2">
		              	Cautare preturi pentru servicii de curierat
		            </h3>
		            <div class="row">
		            	<form action="{{ route('search.services') }}" method="get">
			            	<div class="col m6 s12">
			            		<label class="text-lg text-white font-bold pl-3">De la:</label>
			                <div class="input-field col s12 text-black">
			                    <input id="sender_country" placeholder="" name="sender_country" type="text" class="text-black" required value="{{ old('sender_country') }}" style="box-shadow: none;box-sizing: border-box;background-color:#fff;">
			                    <label for="sender_country" class="higher">{{ __('Tara') }}</label>
			                    <input type="hidden" name="sender_country_code" value="{{ old('sender_country_code') ?? 'ro' }}">
			                    {{-- <small>Change the country for import!</small> --}}
			                </div>
			                <div class="input-field col s12">
			                    <input id="sender_county" data-person="sender"  placeholder="" name="sender_county" type="text" class="input-white px-2" required value="{{ old('sender_county') }}" style="box-shadow: none;" data-url="{{ route('search.county') }}">
			                    <label for="sender_county" class="higher">{{ __('Judet') }}</label>
			                </div>
			                <div class="input-field col s12">
			                    <input id="sender_locality" data-person="sender" placeholder="" name="sender_locality" type="text" class="input-white px-2" required value="{{ old('sender_locality') }}" style="box-shadow: none;" data-url="{{ route('search.locality') }}">
			                    <label for="sender_locality" class="higher">{{ __('Localitate') }}</label>
			                </div>
				            </div>
				            <div class="col m6 s12">
				            	<label class="text-lg text-white font-bold pl-3">Pana la:</label>
				                <div class="input-field col s12 text-black">
				                    <input id="receiver_country" placeholder="" name="receiver_country" type="text" class="text-black" required value="{{ old('receiver_country') }}" style="box-shadow: none;box-sizing: border-box;background-color:#fff;">
				                    <label for="receiver_country" class="higher">{{ __('Tara') }}</label>
				                    <input type="hidden" name="receiver_country_code" value="{{ old('receiver_country_code') ?? 'ro' }}">
				                    {{-- <small>Change the country for import!</small> --}}
				                </div>
				                <div class="input-field col s12">
				                    <input id="receiver_county" data-person="receiver" placeholder="" name="receiver_county" type="text" class="input-white" required value="{{ old('receiver_county') }}" style="box-shadow: none;" data-url="{{ route('search.county') }}">
				                    <label for="receiver_county" class="higher">{{ __('Judet') }}</label>
				                </div>
				                <div class="input-field col s12">
				                    <input id="receiver_locality" data-person="receiver" placeholder="" name="receiver_locality" type="text" class="input-white" required value="{{ old('receiver_locality') }}" style="box-shadow: none;" data-url="{{ route('search.locality') }}">
				                    <label for="receiver_locality" class="higher">{{ __('Localitate') }}</label>
				                </div>
				            </div>
				            <div class="col s12 text-center">
				                <div class="input-field col s12 text-white">
				                	<label class="mr-3" style="display: inline-block;position: relative; left: 0;">
				                    <input id="package_1" name="package_type" type="radio" class="text-white with-gap package-type" required {{ old('package_type') == '1' ? 'checked' : '' }} value="1" style="box-shadow: none;box-sizing: border-box;"> <span class="text-white pl-2">Plic</span>
				                	</label>
				                	<label class="mr-3" style="display: inline-block; position: relative; left: 0;">
				                    <input id="package_2" name="package_type" type="radio" class="text-white with-gap package-type" required {{ old('package_type') == '2' ? 'checked' : '' }} value="2" style="box-shadow: none;"> <span class="text-white">Colet</span>
				                  </label>
				                </div>
				                <div class="input-field col s12 show-colet text-white">
				                  <input type="text" placeholder="Greutate totala" name="total_weight" class="input-white col s12 m6 offset-m3">
				                </div>
				                <button role="submit" class="mx-auto lg:mx-0 hover:underline inline-block bg-white text-blue-700 font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
						            Cauta
						        </button>
				            </div>
			            </form>
		            </div>
		      	</div>
		    </div>
		</div>
    </section>
    <section class="bg-white py-8">
      <div class="container mx-auto flex flex-wrap pt-4 pb-12">
        {{-- <h1 class="w-full my-2 text-5xl font-bold leading-tight text-center text-gray-800">
          Title
        </h1> --}}
        <div class="w-full mb-4">
          <div class="h-1 mx-auto gradient w-64 opacity-25 my-0 py-0 rounded-t"></div>
        </div>
        {{-- <div class="w-full sm:w-1/2 lg:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-tags fa-stack-2x"></i>
	            		<i class="fas fa-percent fa-stack-1x fa-inverse fa-flip-vertical"></i>
	            	</span>
	                <span class="text-xl">Lorem ipsum dolor sit amet.</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam at ipsum eu nunc commodo posuere et sit amet ligula.
	            </p>	
          	</div>
          <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden px-6 py-2">
            <div class="flex items-center justify-center">
              <button class="mx-auto lg:mx-0 hover:underline gradient text-white font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
                Action
              </button>
            </div>
          </div>
        </div> --}}
        {{-- <div class="w-full sm:w-1/2 lg:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-paper-plane fa-2x"></i>
	            	</span>
	                <span class="text-xl">Lorem ipsum dolor sit amet.</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam at ipsum eu nunc commodo posuere et sit amet ligula.
	            </p>	
          	</div>
          <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden px-6 py-2">
            <div class="flex items-center justify-center">
              <button class="mx-auto lg:mx-0 hover:underline gradient text-white font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
                Action
              </button>
            </div>
          </div>
        </div> --}}
        <div class="w-full p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-star-half-alt fa-2x"></i>
	            	</span>
	                <span class="text-xl">Algoritmi de evaluare a curierilor</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Integrarea algoritmilor de evaluare a curierilor, care verifică și acordă punctaje în funcție de durata de ridicare a comenzii, dar și cea de livrare a acesteia, acordă un plus în utilizarea platformei noastre. Performanța curierilor se poate verifica și în ceea ce privește timpul de plată al rambursului din momentul în care a fost coletul livrat. Astfel, puteți face cea mai bună alegere în ceea ce privește curierul care vă va livra coletul.
	            </p>	
          	</div>
        </div>
        {{-- <div class="w-full sm:w-1/2 lg:w-1/3 p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-undo fa-stack-2x"></i>
	            		<i class="fas fa-dollar-sign fa-stack-1x"></i>
	            	</span>
	                <span class="text-xl">Lorem ipsum dolor sit amet.</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam at ipsum eu nunc commodo posuere et sit amet ligula.
	            </p>	
          	</div>
          <div class="flex-none mt-auto bg-white rounded-b rounded-t-none overflow-hidden px-6 py-2">
            <div class="flex items-center justify-center">
              <button class="mx-auto lg:mx-0 hover:underline gradient text-white font-bold rounded-full my-2 py-3 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
                Action
              </button>
            </div>
          </div>
        </div> --}}
        <div class="w-full p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-credit-card fa-2x"></i>
	            	</span>
	                <span class="text-xl">Plata online</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Un alt avantaj legat de utilizarea platformei AMRCOLET îl constituie opțiunea de care puteți beneficia atunci când vine vorba de plata comenzilor. Aceasta se poate realiza la fiecare comandă în parte, sau în avans, folosind o anumită sumă pentru care se va emite factura când se realizează plata și o altă factură la final de lună cu anexa corespunzătoare. Astfel, se va reduce  numărul facturilor, iar banii se vor putea gestiona mai ușor putând observa în mod continuu situația tuturor plăților realizate din contul de pe platformă.
	            </p>	
          	</div>
        </div>
        <div class="w-full p-6 flex flex-col flex-grow flex-shrink">
          	<div class="flex-1 bg-white rounded-t rounded-b-none overflow-hidden">
	            <div class="w-full font-bold text-xl text-gray-800 px-6">
	            	<span class="fa-stack" style="vertical-align: top;">
	            		<i class="fas fa-headset fa-2x"></i>
	            	</span>
	                <span class="text-xl">Echipa de suport clienți</span>
	            </div>
	            <p class="text-gray-800 text-base px-6 pl-20 mb-5">
	                Pentru soluționarea oricărei situații legate de livrarea coletelor noi vă stăm la dispoziție, nefiind nevoie de contactarea de către dumneavoastră a firmei de curierat. Noi vă vom ajuta și ne vom strădui să vă oferim servicii de care să fiți pe deplin mulțumiți.
	            </p>	
          	</div>
        </div>
      </div>
    </section>
    <!-- Change the colour #f8fafc to match the previous section colour -->
    <svg class="wave-top" viewBox="0 0 1439 147" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
      <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        <g transform="translate(-1.000000, -14.000000)" fill-rule="nonzero">
          <g class="wave" fill="#ffffff">
            <path
              d="M1440,84 C1383.555,64.3 1342.555,51.3 1317,45 C1259.5,30.824 1206.707,25.526 1169,22 C1129.711,18.326 1044.426,18.475 980,22 C954.25,23.409 922.25,26.742 884,32 C845.122,37.787 818.455,42.121 804,45 C776.833,50.41 728.136,61.77 713,65 C660.023,76.309 621.544,87.729 584,94 C517.525,105.104 484.525,106.438 429,108 C379.49,106.484 342.823,104.484 319,102 C278.571,97.783 231.737,88.736 205,84 C154.629,75.076 86.296,57.743 0,32 L0,0 L1440,0 L1440,84 Z"
            ></path>
          </g>
          <g transform="translate(1.000000, 15.000000)" fill="#FFFFFF">
            <g transform="translate(719.500000, 68.500000) rotate(-180.000000) translate(-719.500000, -68.500000) ">
              <path d="M0,0 C90.7283404,0.927527913 147.912752,27.187927 291.910178,59.9119003 C387.908462,81.7278826 543.605069,89.334785 759,82.7326078 C469.336065,156.254352 216.336065,153.6679 0,74.9732496" opacity="0.100000001"></path>
              <path
                d="M100,104.708498 C277.413333,72.2345949 426.147877,52.5246657 546.203633,45.5787101 C666.259389,38.6327546 810.524845,41.7979068 979,55.0741668 C931.069965,56.122511 810.303266,74.8455141 616.699903,111.243176 C423.096539,147.640838 250.863238,145.462612 100,104.708498 Z"
                opacity="0.100000001"
              ></path>
              <path d="M1046,51.6521276 C1130.83045,29.328812 1279.08318,17.607883 1439,40.1656806 L1439,120 C1271.17211,77.9435312 1140.17211,55.1609071 1046,51.6521276 Z" opacity="0.200000003"></path>
            </g>
          </g>
        </g>
      </g>
    </svg>
    {{-- <section class="container mx-auto text-center py-6 mb-12">
      <h1 class="w-full my-2 text-5xl font-bold leading-tight text-center text-white">
        Call to Action
      </h1>
      <div class="w-full mb-4">
        <div class="h-1 mx-auto bg-white w-1/6 opacity-25 my-0 py-0 rounded-t"></div>
      </div>
      <h3 class="my-4 text-3xl leading-tight">
        Main Hero Message to sell yourself!
      </h3>
      <button class="mx-auto lg:mx-0 hover:underline bg-white text-gray-800 font-bold rounded-full my-6 py-4 px-8 shadow-lg focus:outline-none focus:shadow-outline transform transition hover:scale-105 duration-300 ease-in-out">
        Action!
      </button>
    </section> --}}
    @isset($dataOrder)
    	{!! $dataOrder['form'] !!}
    @endisset
    <!--Footer-->
    @livewire('footer')
</x-guest-layout>
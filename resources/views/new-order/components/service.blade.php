
@forelse($curieri as $curier)
<div class="input-field col l12 m6 s12 mb-1 mt-0">
    <div class="card m-0">
        <div class="card-content pt-1 pb-1 m-0">
            <div class="row mb-0">
                <div class="input-field col l1 m12 s12 items-center flex p-0" style="min-width: 105px;">
                    <div class="inline-flex curier">
                        <label style="height: 25px;">
                            <input type="radio" class="with-gap" name="curier" data-price="{{ $discounts[$curier->id] ? round(($prices[$curier->id] * (100 - $discounts[$curier->id])/100), 2) : round($prices[$curier->id], 2) }}" value="{{ $curier->name }}">
                            <span style="padding-left: 22.5px;"></span>
                        </label>
                    </div>
                    {{-- <i class="material-icons inline-block" style="font-size: 2rem;">info_outline</i> --}}
                    <img class="inline-block" style="width: 50px;" src="{{ asset('img/curieri/'.$curier->logo ) }}">
                </div>
                <div class="input-field col l1 m12 s12 text-center p-0 relative" style="min-width: 135px;">
                	@if($discounts[$curier->id])
                	<p class="absolute font-weight-700 text-red-500" style="top: -27px; left: 0;
                	right: 0; left: 15px;">Promotie -{{ $discounts[$curier->id] }}%</p>
                	@endif
                	{{-- <p class="absolute font-weight-700 text-red-500" style="top: -12px; left: 0;
                	right: 0; font-size: 0.75rem;">Valabil pana la 01.06 </p> --}}
                    <p><b>{{ $curier->name }}</b></p>
                </div>
                @if($discounts[$curier->id])
                <div class="input-field col l1 m5 s5 text-right p-0 pr-1 relative" style="min-width: 70px;">
                	<p class="text-red-500 relative" style="font-size: 0.75rem;"><s>{{ round(($prices[$curier->id] * 100)/(100 + $curier->tva), 2) }}</s></p>
                    <p class="text-red-500 relative" style="font-size: 1.1rem; top: 4px;"><s>{{ round($prices[$curier->id], 2) }}</s></p>
                </div>
                <div class="input-field col l1 m7 s7 text-left p-0 relative" style="min-width: 130px;">
                    <p class="text-green-500 absolute" style="top: -4px;">{{ round(($prices[$curier->id] * 100)/(100 + $curier->tva) * (100 - $discounts[$curier->id])/100, 2) }} ron + tva</p>
                    <p class="text-green-500 relative" style="font-size: 1.8rem; bottom: -9px;">{{ round(($prices[$curier->id] * (100 - $discounts[$curier->id])/100), 2) }} ron</p>
                </div>
                @else
                <div class="input-field col l1 m7 s7 text-left p-0 relative" style="min-width: 130px;">
                    <p class="text-green-500 absolute" style="top: -4px;">{{ round(($prices[$curier->id] * 100)/(100 + $curier->tva), 2) }} ron + tva</p>
                    <p class="text-green-500 relative" style="font-size: 1.8rem; bottom: -9px;">{{ round($prices[$curier->id], 2) }} ron</p>
                </div>
                @endif
                <div class="input-field col l2 m12 s12 text-center" style="min-width: 160px;">
                	<p class="absolute" style="top: -20px; left: 0;
                	right: 0;">Performanta serviciu</p>
                	<p class="relative" style="">Ridicare<span class="inline-block pr-6"></span> Livrare</p>
                	<div class="relative" style="">
                		<b>{{ $curier->performance_pickup }}</b>/5
                    	<div class="star" style="--rating: 5;" aria-label="Rating of this product is {{ $curier->performance_pickup }} out of 5."></div>
                    	<span class="inline-block pr-6"></span>
                    	<b>{{ $curier->performance_delivery }}</b>/5
                    	<div class="star" style="--rating: 5;" aria-label="Rating of this product is {{ $curier->performance_delivery }} out of 5."></div>
                    </div>
                </div>
                <div class="input-field col l1 m12 s12 p-0 text-center lg:text-left">
                    <p>Livrare in<br> 1-{{ $curier->max_order_days }} zile</p>
                </div>
                <div class="input-field col l1 m12 s12 p-0 text-center lg:text-left">
                    <p>Max {{ $curier->max_package_weight }} kg <br>per colet</p>
                </div>
                <div class="input-field col l2 m12 s12 p-0 text-center lg:text-left" style="min-width: 200px;">
                	<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                    	<span class="fa-layers fa-fw fa-lg {{ $curier->work_saturday ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">
						    <i class="far fa-calendar"></i>
						    <span class="fa-layers-text" data-fa-transform="shrink-11 down-2" style="font-weight:900">SAT</span>
						</span>
						<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->work_saturday ? __('Livrare sambata') : __('Fara livrare sambata') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
					</div>
					<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                		<i class="fas fa-print fa-lg {{ $curier->require_awb ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                		<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->require_awb ? __('Nu necesita printarea awb-ul') : __('Necesita printarea awb-ul') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
                	</div>
                	<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                		<i class="fas fa-money-bill-wave fa-lg {{ $curier->ramburs_cash ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                		<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->ramburs_cash ? __('Ramburs cash') : __('Nu ofera optiunea de ramburs cash') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
                	</div>
                	<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                		<i class="far fa-credit-card fa-lg {{ $curier->ramburs_cont ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                		<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->ramburs_cont ? __('Ramburs in cont') : __('Nu ofera optiunea de ramburs in cont') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
                	</div>
                	<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                		<i class="fas fa-file-invoice fa-lg {{ $curier->assurance ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false"></i>
                		<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->assurance ? __('Asigurare') : __('Nu ofera asigurare') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
                	</div>
                	<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 30px;">
                    	<span class="fa-layers fa-fw fa-lg {{ $curier->open_when_received ? '' : 'opacity-30' }}" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">
						    <i class="far fa-eye" data-fa-transform="shrink-8 up-4"></i>
						    <i class="fas fa-box-open" data-fa-transform="shrink-5 down-5"></i>
						</span>
						<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ $curier->open_when_received ? __('Deschidere la primire') : __('Nu ofera optiunea de deschidere la primire') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
					</div>
					<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 50px;">
						<span class="fa-layers fa-fw fa-lg mr-9" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">
						    <i class="far fa-clock" data-fa-transform="down-2"></i>
						    <i class="fas fa-phone fa-flip-horizontal" data-fa-transform="shrink-8 down-4 left-3"></i>
						    <span class="fa-layers-text" data-fa-transform="shrink-8 right-17 down-7" style="font-weight:900">{{ $curier->last_order_hour }}:00</span>
						</span>
						<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ __('Ora ultimei comenzi') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
					</div>
					<div class="col s1 p-0" x-data="{ tooltip: false }" style="min-width: 50px;">
						<span class="fa-layers fa-fw fa-lg" x-on:mouseover="tooltip = true" x-on:mouseleave="tooltip = false">
						    <i class="far fa-clock" data-fa-transform="down-2"></i>
						    <i class="fas fa-truck" data-fa-transform="shrink-8 down-6 left-4"></i>
						    <span class="fa-layers-text" data-fa-transform="shrink-8 right-17 down-7" style="font-weight:900">{{ $curier->last_pick_up_hour }}:00</span>
						</span>
						<div class="relative inline" x-cloak x-show.transition.origin.top="tooltip">
                            <div class="absolute bottom-8 z-10 w-32 p-2 -mt-1 text-sm leading-tight text-white transform -translate-x-12 bg-blue-500 rounded-lg text-center shadow-lg">
                                {{ __('Ora ultimei ridicari') }}
                            </div>
                            <svg class="absolute z-10 w-6 h-6 text-blue-500 transform -translate-x-1 -translate-y-11 fill-current stroke-current" width="8" height="8">
                                <rect x="12" y="-10" width="8" height="8" transform="rotate(45)" />
                            </svg>
                        </div>
					</div>
                </div>
            </div>
            {{-- <div class="row mb-0 curier-more-information hidden" data-curier="info">
                <div class="input-field col l12 m12 s12 p-0 prose min-w-full">
                	<ul>
                		<li class="inline-block w-full">
                			<div class="m-0"><b>Nota ridicare pentru Locatie Ridicare</b> <div class="stars" style="--rating: {{ $curier->performance_pickup }};" aria-label="Rating of this product is {{ $curier->performance_pickup }} out of 5."></div></div>
                			<table class="table-fixed col l4 m6 s12 m-0">
								<thead>
								    <tr class="text-md">
								      	<th class="w-3/7 text-center">Ziua comenzii</th>
								      	<th class="w-3/7">Prima zi</th>
								      	<th class="w-3/7">A 2a zi</th>
								      	<th class="w-3/7">3 zile si peste</th>
								    </tr>
								</thead>
								<tbody>
								    <tr class="border-t text-md">
								      	<td class="text-center py-2"><b>94%</b></td>
								      	<td class="text-center py-2"><b>6%</b></td>
								      	<td class="text-center py-2"><b>1%</b></td>
								      	<td class="text-center py-2"><b>0%</b></td>
								    </tr>
								</tbody>
							</table>
                		</li>
                		<li class="inline-block w-full">
                			<div class="m-0"><b>Nota livrare pentru Locatie Livrare</b> <div class="stars" style="--rating: {{ $curier->performance_delivery }};" aria-label="Rating of this product is {{ $curier->performance_pickup }} out of 5."></div></div>
                			<table class="table-fixed col l4 m6 s12 m-0">
								<thead>
								    <tr class="text-md">
								      	<th class="w-3/7 text-center">Ziua comenzii</th>
								      	<th class="w-3/7">Prima zi</th>
								      	<th class="w-3/7">A 2a zi</th>
								      	<th class="w-3/7">4 zile si peste</th>
								    </tr>
								</thead>
								<tbody>
								    <tr class="border-t text-md">
								      	<td class="text-center py-2"><b>82%</b></td>
								      	<td class="text-center py-2"><b>17%</b></td>
								      	<td class="text-center py-2"><b>1%</b></td>
								      	<td class="text-center py-2"><b>0%</b></td>
								    </tr>
								    <tr class="border-t text-md">
								      	<td colspan="4" class="text-center py-2">statistici calculate pentru ultimele 30 de zile</td>
								    </tr>
								</tbody>
							</table>
                		</li>
                		<li>Comanda pana la ora 12:00  pentru a fi (aproape) sigur ca va veni curierul in aceeasi zi (in orase mari).</li>
                		<li>Pentru o preluare rapida, tipareste Eticheta de trasnport (AWB) si lipeste-o pe colet</li>
                		<li>Preturi prohibitive pentru preturi livrari peste 31kg (greutate sau greutate volumetrica). Daca aveti mai multe colete care trebuiesc trimise impreuna si greutatea totala depaseste 31kg, va recomanda sa le trimiteti cu comezi de transport separate.</li>
                	</ul>
                </div>
            </div> --}}
        </div>
    </div>
</div>
@empty
<div class="input-field col l12 m6 s12 mb-1 mt-0">
    <div class="card m-0">
        <div class="card-content pt-1 pb-1 m-0">
            <div class="row mb-0">
            	{{ __('Nu a fost gasit nici un serviciu care sa indeplineasca cerintele dorite.') }}
            </div>
        </div>
    </div>
</div>
@endforelse

@if(auth()->check() && auth()->id() == 1)

@endif
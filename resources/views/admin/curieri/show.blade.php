@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/creativetimofficial/tailwind-starter-kit/compiled-tailwind.min.css" /> --}}
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Curieri') }}</x-slot>
        <x-slot name="href">{{ route('admin.curieri.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza curier') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        @if(session()->has('success'))
                            <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3" role="alert">
                                <p>{{ session()->get('success') }}</p>
                            </div>
                        @endif
                        <table class="w-full flex flex-row flex-no-wrap lg:bg-white rounded-lg overflow-hidden lg:shadow-lg my-5">
                            <thead class="text-white">
                                @forelse($curieri as $curier)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('Nume') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('TVA') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Pret per volum') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Pret per plic') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Greutate maxima colet') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Greutate maxima totala') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Ora ultimei comenzi') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Ora ultimei ridicari') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" width="110px">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-td>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('Nume') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('TVA') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Pret per volum') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Pret per plic') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Greutate maxima colet') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Greutate maxima totala') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Ora ultimei comenzi') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Ora ultimei ridicari') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </thead>
                            <tbody class="flex-1 lg:flex-none">
                                @forelse($curieri as $curier)
                                    <x-jet-tr>
                                        <x-jet-td>{{ $curier->name }}</x-jet-td>
                                        <x-jet-td>{{ $curier->tva }}%</x-jet-td>
                                        <x-jet-td>{{ $curier->volum_price }} RON</x-jet-td>
                                        <x-jet-td>{{ $curier->plic_price }} RON</x-jet-td>
                                        <x-jet-td>{{ $curier->max_package_weight }} Kg</x-jet-td>
                                        <x-jet-td>{{ $curier->max_total_weight }} Kg</x-jet-td>
                                        <x-jet-td>{{ $curier->last_order_hour }}:00</x-jet-td>
                                        <x-jet-td>{{ $curier->last_pick_up_hour }}:00</x-jet-td>
                                        <x-jet-td class="sm:text-center">
                                            <a href="{{ route('admin.curieri.edit', $curier->id) }}" class="text-blue-300"><i class="fas fa-edit"></i></a>
                                            /
                                            @if($curier->external_orders)
                                                <a href="{{ route('admin.curieri.edit.rates', $curier->id) }}" class="text-blue-300"><i class="fas fa-flag"></i></a>
                                                /
                                            @endif
                                            <form method="POST" action="{{ route('admin.curieri.destroy', $curier->id) }}" class="inline">
                                                @method('delete')
                                                @csrf
                                                <a href="{{ route('admin.curieri.destroy', $curier->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                    this.closest('form').submit();" target="curier_{{ $curier->id }}"><i class="fas fa-trash-alt"></i></a>
                                            </form>
                                        </x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr class="border-0" style="height: 100%;">
                                        <x-jet-td colspan="9" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost creat nici un curier') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </tbody>
                            {{ $curieri->links() }}
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
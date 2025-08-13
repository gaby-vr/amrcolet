@props(['cancel_orders' => App\Models\Livrare::where('status', '6')->count()])

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}"></link>
@endpush

<div class="flex flex-col lg:flex-row bg-gray-100 pt-16">
    <div class="bg-black shadow-xl h-14 fixed bottom-0 lg:relative lg:h-auto lg:min-h-screen z-10 w-full lg:w-56 nav-admin">
        <div class="lg:mt-16 lg:w-56 lg:fixed lg:left-0 lg:top-0 content-center lg:content-start text-left justify-between">
            <ul class="list-reset flex flex-row lg:flex-col py-0 lg:py-3 px-1 lg:px-2 text-center lg:text-left browser-default">
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.users.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-orange-500 @if(request()->routeIs('admin.view') || request()->routeIs('admin.users.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-users"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Utilizatori</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.orders.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.orders.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-check-circle"></i>
                        <span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Comenzi
                            @if($cancel_orders)
                                <span class="bg-yellow-700 text-white px-1 rounded-full text-sm lg:float-right inline lg:hidden">{{ $cancel_orders }}</span>
                            @endif
                        </span>
                        @if($cancel_orders)
                            <span class="bg-yellow-700 text-white px-1 rounded-full text-sm hidden float-right lg:inline">{{ $cancel_orders }}</span>
                        @endif
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.pending_orders.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.pending_orders.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-hourglass"></i>
                        <span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Comenzi in asteptare
                            @if($cancel_orders)
                                <span class="bg-yellow-700 text-white px-1 rounded-full text-sm lg:float-right inline lg:hidden">{{ $cancel_orders }}</span>
                            @endif
                        </span>
                        @if($cancel_orders)
                            <span class="bg-yellow-700 text-white px-1 rounded-full text-sm hidden float-right lg:inline">{{ $cancel_orders }}</span>
                        @endif
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.invoices.show') }}" class="block py-1 lg:py-3 pl-0 lg:pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.invoices.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-file-invoice"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Istoric plati</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.repayments.show') }}" class="block py-1 lg:py-3 pl-0 lg:pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.repayments.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-money-check"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Rambursuri</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.curieri.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.curieri.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-truck"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Curieri</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.pages.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.pages.*')) border-blue-700 text-white @endif">
                        <i class="far fa-newspaper"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Pagini</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.borderouri.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.borderouri.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-clipboard-list"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Borderouri</span>
                    </a>
                </li>
                <li class="mr-3 flex-1">
                    <a href="{{ route('admin.invoice-sheets.show') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.invoice-sheets.*')) border-blue-700 text-white @endif">
                        <i class="fas fa-clipboard-list"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Fise facturi</span>
                    </a>
                </li>
                {{-- <li class="mr-3 flex-1">
                    <a href="{{ route('admin.announcement.edit') }}" class="block py-1 lg:py-3 pl-1 align-middle text-white no-underline hover:text-white border-b-2 border-gray-800 hover:border-blue-700 @if(request()->routeIs('admin.announcement.*')) border-blue-700 text-white @endif">
                        <i class="far fa-newspaper"></i><span class="hidden sm:block pb-1 lg:pb-0 pl-0 lg:pl-3 text-xs lg:text-base text-white lg:text-gray-400 block lg:inline-block">Anunt</span>
                    </a>
                </li> --}}
            </ul>
        </div>
    </div>
    <div class="main-content flex-1 bg-gray-100 pb-24 lg:pb-5">
        <div class="bg-black">
            <div class="rounded-tl-3xl bg-gradient-to-r from-blue-700 to-black p-4 shadow text-2xl text-white">
                <h3 class="font-bold pl-2 inline-block">{{ $title }}</h3>
                @if(isset($btnName) && $btnName != '')
                <a href="{{ $href }}" class="bg-black mt-2 table sm:inline-block sm:mt-0 sm:bg-blue-700 hover:bg-blue-900 text-sm text-white font-bold py-2 px-3 rounded-full sm:float-right"><i class="fas fa-plus"></i> {{ $btnName }}</a>
                @endif
            </div>
        </div>
        {{ $slot }}
    </div>
</div>

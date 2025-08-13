<footer class="bg-white">
    <div class="container max-w-7xl mx-auto px-8">
        <div class="w-full flex flex-col md:flex-row py-6">
            <div class="flex-3 mb-3 text-black">
                <a class="text-blue-500 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="javascript:void(0)">
                    <img alt="Amrcolet" src="{{ asset('img/logo.png') }}" style="max-height: 100px;">
                   <!--  Colete -->
                </a>
                <a class="text-blue-500 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="javascript:void(0)">
                    <img alt="Netopia" class="mt-4" src="{{ asset('img/netopia_banner.jpg') }}" style="max-height: 100px; max-width: 370px;">
                   <!--  Netopia -->
                </a>
            </div>
            <div class="flex-1 pl-3 mb-3">
                <p class="uppercase text-gray-500 md:mb-6">Links</p>
                <ul class="list-reset">
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="{{ route('terms.show') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Terms of Service') }}</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="{{ route('packaging.show') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Packaging Policy') }}</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="{{ route('policy.show') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Privacy Policy') }}</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="{{ route('cookies.show') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Cookie Policy') }}</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        {{-- <a href="{{ route('postal.show') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Postal Conditions') }}</a> --}}
                        <a href="{{ route('page', 'postal-policy') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Postal Conditions') }}</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="{{ route('contact') }}" class="no-underline hover:underline text-gray-800 hover:text-blue-500">{{ __('Contact') }}</a>
                    </li>
                </ul>
            </div>
            <div class="flex-1 text-black">
                <a class="text-blue-500 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" 
                    href="https://ec.europa.eu/consumers/odr/main/index.cfm?event=main.home2.show&lng=RO">
                    <img alt="Solutionarea online a litigiilor" src="{{ asset('img/sol_online.png') }}" class="ml-auto" style="max-width: 250px;">
                   <!--  Solutionarea online a litigiilor -->
                </a>
                <a class="text-blue-500 no-underline hover:no-underline font-bold text-2xl lg:text-4xl" href="https://anpc.ro/ce-este-sal/">
                    <img alt="Solutionarea alternativa a litigiilor" src="{{ asset('img/sal.png') }}" class="mt-4 ml-auto" style="max-width: 250px;">
                   <!--  Solutionarea alternativa a litigiilor -->
                </a>
            </div>
            {{-- <div class="flex-1">
                <p class="uppercase text-gray-500 md:mb-6">Legal</p>
                <ul class="list-reset mb-6">
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Terms</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Privacy</a>
                    </li>
                </ul>
            </div>
            <div class="flex-1">
                <p class="uppercase text-gray-500 md:mb-6">Social</p>
                <ul class="list-reset mb-6">
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Facebook</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Linkedin</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Twitter</a>
                    </li>
                </ul>
            </div>
            <div class="flex-1">
                <p class="uppercase text-gray-500 md:mb-6">Company</p>
                <ul class="list-reset mb-6">
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Official Blog</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">About Us</a>
                    </li>
                    <li class="mt-2 inline-block mr-2 md:block md:mr-0">
                        <a href="#" class="no-underline hover:underline text-gray-800 hover:text-blue-500">Contact</a>
                    </li>
                </ul>
            </div> --}}
        </div>
        <p class="text-gray-500 text-center py-2">Copyright {{ date('Y') }} - {{ config('app.name') }}</p>
    </div>
</footer>
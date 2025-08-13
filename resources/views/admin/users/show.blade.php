@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Utilizatori') }}</x-slot>
        <x-slot name="href">{{ route('admin.users.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza utilizator') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        <div class="px-5 w-full pb-3">
                            @if(session()->has('success'))
                                <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3" role="alert">
                                    <p>{{ session()->get('success') }}</p>
                                </div>
                            @endif
                            <form action="{{ route('admin.users.show') }}" method="get">
                                <div class="grid grid-cols-12 gap-3 w-full">
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Nume') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['name'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="name" name="name" />
                                    </div>
                                    <div class="col-span-12 md:col-span-3">
                                        <x-jet-label value="{{ __('Email') }}" />
                                        <x-jet-input type="text" class="w-full" value="{{ $condtitions['email'] ?? '' }}" placeholder="{{ __('Incepe cu...') }}" id="email" name="email" />
                                    </div>
                                    <div class="col-span-12 md:col-span-2 self-end pb-1">
                                        <button type="submit" class="inline-flex px-4 py-2 bg-black border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-black disabled:opacity-25 transition">{{ __('Cauta') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <x-jet-table>
                            {{ $users->links() }}
                            <x-slot name="thead">
                                @forelse($users as $user)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('ID') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Nume') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Email') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" style="height: 55px; line-height: 32px;">{{ __('Tip') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" width="110px">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('ID') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Nume') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Email') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Tip') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </x-slot>
                            @forelse($users as $user)
                                <x-jet-tr>
                                    <x-jet-td>{{ $user->id }}</x-jet-td>
                                    <x-jet-td>{{ $user->name }}</x-jet-td>
                                    <x-jet-td>{{ $user->email }}</x-jet-td>
                                    <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;">
                                        @switch($user->role)
                                            @case(2)
                                                <span class="bg-green-200 text-green-600 py-2 px-3 rounded-full text-sm">{{ __('Contractant') }}</span>
                                                @break
                                            @default
                                                <span class="bg-blue-200 text-blue-600 py-2 px-3 rounded-full text-sm">{{ __('Normal') }}</span>
                                                @break
                                        @endswitch
                                    </x-jet-td>
                                    <x-jet-td class="sm:text-center">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-blue-300"><i class="fas fa-edit"></i></a>
                                        /
                                        <a href="{{ route('admin.users.invoice', $user->id) }}" class="text-blue-300"><i class="fas fa-file-invoice"></i></a>
                                        /
                                        <form method="POST" action="{{ route('admin.users.login.as', $user->id) }}" class="inline">
                                            @csrf
                                            <a href="{{ route('admin.users.login.as', $user->id) }}" class="text-green-500" onclick="event.preventDefault();
                                                this.closest('form').submit();" target="user_{{ $user->id }}"><i class="fas fa-user-alt"></i></a>
                                        </form>
                                        /
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="inline">
                                            @method('delete')
                                            @csrf
                                            <a href="{{ route('admin.users.destroy', $user->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                if(confirm('Vrei sa stergi user-ul {{ $user->name }}?')){this.closest('form').submit();}" target="user_{{ $user->id }}"><i class="fas fa-trash-alt"></i></a>
                                        </form>
                                    </x-jet-td>
                                </x-jet-tr>
                            @empty
                                <x-jet-tr class="border-0" style="height: 100%;">
                                    <x-jet-td colspan="5" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasit nici o utilizator') }}</x-jet-td>
                                </x-jet-tr>
                            @endforelse
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
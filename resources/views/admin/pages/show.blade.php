@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<x-app-layout>
    <x-jet-banner />
    <x-jet-admin-navigation>
        <x-slot name="title">{{ __('Paginii') }}</x-slot>
        <x-slot name="href">{{ route('admin.pages.create') }}</x-slot>
        <x-slot name="btnName">{{ __('Creaza pagina') }}</x-slot>
        <div class="">
            <section class="relative py-8 bg-gray-100">
                <div class="container mx-auto px-4">
                    <div class="flex flex-wrap justify-center">
                        @if(session()->has('success'))
                            <div class="flex items-center bg-green-500 text-white text-sm font-bold px-4 py-3" role="alert">
                                <p>{{ session()->get('success') }}</p>
                            </div>
                        @endif
                        <x-jet-table>
                            {{ $pages->links() }}
                            <x-slot name="thead">
                                @forelse($pages as $page)
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('ID') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Titlu') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Slug') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" style="height: 55px; line-height: 32px;">{{ __('Tip') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black" class="sm:text-center" width="110px">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @empty
                                    <x-jet-tr location='thead' bg="black">
                                        <x-jet-td location='thead' border="black">{{ __('ID') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Titlu') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Slug') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Tip') }}</x-jet-td>
                                        <x-jet-td location='thead' border="black">{{ __('Optiuni') }}</x-jet-td>
                                    </x-jet-tr>
                                @endforelse
                            </x-slot>
                            @forelse($pages as $page)
                                <x-jet-tr>
                                    <x-jet-td>{{ $page->id }}</x-jet-td>
                                    <x-jet-td>{{ $page->title }}</x-jet-td>
                                    <x-jet-td>{{ $page->slug }}</x-jet-td>
                                    <x-jet-td class="sm:text-center" style="height: 55px; line-height: 32px;">
                                        @switch($page->main)
                                            @case(1)
                                                <span class="bg-green-200 text-green-600 py-2 px-3 rounded-full text-sm">{{ __('Default') }}</span>
                                                @break
                                            @default
                                                <span class="bg-blue-200 text-blue-600 py-2 px-3 rounded-full text-sm">{{ __('Custom') }}</span>
                                                @break
                                        @endswitch
                                    </x-jet-td>
                                    <x-jet-td class="sm:text-center">
                                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-blue-300"><i class="fas fa-edit"></i></a>
                                        /
                                        <a href="{{ route('admin.pages.editor', $page->id) }}" class="text-blue-300"><i class="fas fa-paint-brush"></i></a>
                                        @if($page->main != '1')
                                            /
                                            <form method="POST" action="{{ route('admin.pages.destroy', $page->id) }}" class="inline">
                                                @method('delete')
                                                @csrf
                                                <a href="{{ route('admin.pages.destroy', $page->id) }}" class="text-red-500" onclick="event.preventDefault();
                                                    this.closest('form').submit();" target="page_{{ $page->id }}"><i class="fas fa-trash-alt"></i></a>
                                            </form>
                                        @endif
                                    </x-jet-td>
                                </x-jet-tr>
                            @empty
                                <x-jet-tr class="border-0" style="height: 100%;">
                                    <x-jet-td colspan="5" class="rounded-r-md text-center" style="height: 100%; margin-bottom: 1.25px;">{{ __('Nu a fost gasit nici o pagina') }}</x-jet-td>
                                </x-jet-tr>
                            @endforelse
                        </x-jet-table>
                    </div>
                </div>
            </section>
        </div>
    </x-jet-admin-navigation>
</x-app-layout>
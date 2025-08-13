@props(['id', 'maxWidth', 'button_text' => null, 'title' => null])

@php
$id = $id ?? 'modal-'.md5(mt_rand(100,999));

$maxWidth = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
    '3xl' => 'max-w-3xl',
    '4xl' => 'max-w-4xl',
][$maxWidth ?? '4xl'];
@endphp

<div {{ $attributes->merge(['class' => 'leading-none mr-2']) }} class="float-left sm:float-right inline-block leading-none mr-2" x-data="{expanded: false}">

    <x-jet-button class="" type="button" x-on:click="expanded = true">{{ $button_text ?? __('Deschide') }}</x-jet-button>

    <div x-show="expanded" class="fixed inset-0 overflow-y-auto z-50" id="{{ $id }}" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title" style="display: none;">
        <div x-show="expanded" x-transition.opacity="" class="fixed inset-0 bg-black bg-opacity-50" style="display: none;"></div>

        <div x-show="expanded" x-transition="" x-on:click="expanded = false" class="relative flex min-h-screen items-center justify-center p-4" style="display: none;">
            <div x-on:click.stop="" x-trap.noscroll.inert="expanded" class="relative w-full {{ $maxWidth }} overflow-y-auto rounded-xl bg-white p-6 shadow-lg" style="max-height: 80vh;">
            	@if($title)
                	<h2 class="text-3xl font-bold" id="{{ $id }}-title">{{ $title }}</h2>
                @endif
        		{{ $slot }}
        		<div class="mt-4 flex space-x-2">
                    <x-jet-button class="" type="button" x-on:click="expanded = false">{{ __('Inchide') }}</x-jet-button>

                    {{ $buttons }}
                </div>
        	</div>
        </div>
    </div>
</div>
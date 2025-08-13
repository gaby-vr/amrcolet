@push('styles')
<!-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700" rel="stylesheet" /> -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('fonts/fontawesome/css/all.min.css') }}"> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/materialize.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/theme/mat/style.min.css') }}">
{{-- <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mat/materialize-stepper/materialize-stepper.min.css') }}"> --}}
<link rel="stylesheet" type="text/css" href="{{ asset('css/pages/profile-menu.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('fonts/fontawesome/js/all.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/vendors.min.js') }}"></script>
<script src="{{ asset('js/vendors/mat/plugins.js') }}"></script>
{{-- <script src="{{ asset('js/vendors/mat/materialize-stepper/materialize-stepper.min.js') }}"></script> --}}
<script src="{{ asset('js/vendors/mat/formatter/formatter.js') }}"></script>

@endpush

<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight m-0">
        {!! $this->title !!}
    </h2>
</x-slot>

@livewire('profile-menu')

<div id="main" class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            @livewire($this->section, ['subsection' => $this->subsection, 'id' => $this->itemId])
        </div>
    </div>
</div>

<!--Footer-->
@livewire('footer')
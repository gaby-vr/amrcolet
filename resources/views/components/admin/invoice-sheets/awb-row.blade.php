@props(['livrare' => '', 'name' => 'livrari' , 'i' => 0, 'disabled' => false, 'all_checked' => true])

<div class="grid gap-2 grid-cols-12 items-group pb-2 awb-row">
    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <x-jet-input class="block mt-1 w-full" type="text" placeholder="{{ __('AWB') }}" 
            :hasError="$errors->has($name.'.'.$i.'.awb')" :disabled="$disabled"
            value="{{ $livrare['api_shipment_awb'] ?? $livrare['awb'] ?? '' }}" name="{{ $name }}[awb][]" />
    </div>
    {{-- @if(empty($livrare) || !empty($livrare['optional_product'])) --}}
        <div class="col-span-12 md:col-span-9">
            <x-jet-input class="block mt-1 w-full" type="text" placeholder="{{ __('Produs (optional, trebuie completat cand awb este gol)') }}" 
                :hasError="$errors->has($name.'.'.$i.'.optional_product')" :disabled="$disabled"
                value="{{ $livrare['optional_product'] ?? '' }}" name="{{ $name }}[optional_product][]" />
        </div>
    {{-- @endif --}}

    {{-- <div @class(['col-span-12 lg:col-span-3 select2-container', 'select2-error' => $errors->has($name.'.'.$i.'.awb')])>
        <x-jet-select class="block mt-1 w-full select2-awb" type="text" data-placeholder="{{ __('AWB') }}" 
            :hasError="$errors->has($name.'.'.$i.'.awb')" name="{{ $name }}[awb][]" 
            data-url="{{ route('admin.borderouri.get') }}" data-default="{{ $livrare['awb'] ?? '' }}" data-tags="true" >
            @isset($livrare['awb'])
                <option value="{{ $livrare['awb'] ?? '' }}">{{ $livrare['awb'] ?? '' }}</option>
            @endif
        </x-jet-select>
    </div> --}}
    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <x-jet-input class="block mt-1 w-full" type="text" placeholder="{{ __('Nume expeditor') }}" 
            :hasError="$errors->has($name.'.'.$i.'.sender_name')" :disabled="$disabled"
            value="{{ $livrare['sender_name'] ?? $livrare['sender']['name'] ?? '' }}" name="{{ $name }}[sender_name][]" />
    </div>
    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <x-jet-input class="block mt-1 w-full" type="text" placeholder="{{ __('Nume destinatar') }}" 
            :hasError="$errors->has($name.'.'.$i.'.receiver_name')" :disabled="$disabled"
            value="{{ $livrare['receiver_name'] ?? $livrare['receiver']['name'] ?? '' }}" name="{{ $name }}[receiver_name][]" />
    </div>
    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <x-jet-input class="block mt-1 w-full datepicker" type="text" placeholder="{{ __('Data creare livrare') }}"
            data-default="{{ $livrare['order_created_at'] ?? $livrare['created_at'] ?? '' }}" 
            :hasError="$errors->has($name.'.'.$i.'.order_created_at')" :disabled="$disabled"
            value="{{ $livrare['order_created_at'] ?? $livrare['created_at'] ?? '' }}" name="{{ $name }}[order_created_at][]" required />
    </div>
    <div class="col-span-12 md:col-span-6 lg:col-span-2">
        <x-jet-input class="block mt-1 w-full" type="number" placeholder="{{ __('Valoare') }}" 
            :hasError="$errors->has($name.'.'.$i.'.payment')" :disabled="$disabled"
            value="{{ $livrare['payment'] ?? $livrare['amount'] ?? '' }}" step="0.01" min="0" name="{{ $name }}[payment][]" />
    </div>
    @if(!empty($livrare['status_color']))
        <div class="col-span-12 md:col-span-6 lg:col-span-2">
            <x-jet-order-status :order="$livrare->order ?? (object)$livrare" class="mt-1.5" />
        </div>
    @endif
    <div class="col-span-2 lg:col-span-2 w-full mx-auto">
        <x-jet-button type="button" class="mt-1 w-full remove-item {{ !$all_checked ? 'hidden' : '' }}">
            <i class="fa fa-minus w-full" style="line-height: 24px;"></i>
        </x-jet-button>
    	@if(!$all_checked)
    		<x-jet-label class="flex items-center py-3.5">
                <x-jet-checkbox value="1" name="{{ $name }}[include][]" />
                <div class="ml-2">{!! __('Include') !!}</div>
            </x-jet-label>
	    @endif
    </div>
</div>
@props(['thead','pagination'])

<div class="md:grid md:grid-cols-2 md:gap-3 px-5 w-full">
    <div class="md:mt-0 mb-1 md:col-span-2" style="overflow-y: auto;">
        <table {{ $attributes->merge(['class' => 'table-comp w-full flex flex-row flex-no-wrap sm:bg-white rounded-lg overflow-hidden sm:shadow-md my-5']) }}>
            <thead class="text-white">
                {{ $thead ?? '' }}
            </thead>
            <tbody class="flex-1 sm:flex-none">
                {{ $slot }}
            </tbody>
        </table>
        {{ $pagination ?? '' }}
    </div>
</div>
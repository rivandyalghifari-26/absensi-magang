<x-filament-widgets::widget>
    <x-filament::section>
        @if (empty(auth()->user()->intern?->face_descriptor))

<div class="bg-yellow-500/10 border border-yellow-500 text-yellow-300 rounded-xl p-4">
    Mohon setup wajah terlebih dahulu sebelum melakukan presensi.
</div>

@endif
    </x-filament::section>
</x-filament-widgets::widget>

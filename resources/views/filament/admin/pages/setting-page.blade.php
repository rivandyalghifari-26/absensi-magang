<x-filament-panels::page>
   
<form wire:submit="save">

{{ $this->form }}

<div class="mt-6">
<x-filament::button type="submit" icon="heroicon-o-check" wire:loading.attr="disabled">
    <span wire:loading:remove>Simpan Pengaturan</span>
    <span wire:loading>Menyimpan</span>
</x-filament::button>

</div>

</form>

</x-filament-panels::page>

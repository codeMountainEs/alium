<x-filament-panels::page>
    @if($this->hasHeader())
        {{ $this->getHeader() }}
    @endif

    {{ $this->getHeaderWidgets() }}
</x-filament-panels::page> 
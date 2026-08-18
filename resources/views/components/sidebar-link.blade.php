@props(['active', 'icon'])

@php
$baseClasses = 'flex items-center px-4 py-2.5 text-[13px] font-bold rounded-xl transition-all duration-300 group relative overflow-hidden italic';

$activeClasses = ($active ?? false)
    ? 'bg-agri-l_card text-agri-green dark:bg-agri-green dark:text-white shadow-lg'
    : 'text-slate-500 dark:text-white/60 hover:bg-agri-l_card/50 dark:hover:bg-white/10 hover:text-agri-green dark:hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => "$baseClasses $activeClasses"]) }} wire:navigate>
    <div class="w-8 flex justify-center items-center shrink-0">
        <i class="{{ $icon }} text-lg transition-transform duration-500 group-hover:scale-110"></i>
    </div>
    <span class="ms-3 whitespace-nowrap" x-show="!sidebarCollapsed || mobileOpen" x-transition:enter="transition delay-100 duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        {{ $slot }}
    </span>
</a>

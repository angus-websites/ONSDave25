<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center">
                <x-greeting>
                    {{ Auth::user()->name }}
                </x-greeting>
                <p class="mb-6 mt-2 text-lg leading-8 text-base-content/70">
                    Today you have worked...
                </p>
                <livewire:pages.dashboard.clock-display />
            </div>

            <div class="divider my-10"></div>

            <livewire:pages.dashboard.clock />
        </div>
    </div>
</x-app-layout>

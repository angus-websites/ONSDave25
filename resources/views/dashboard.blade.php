<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="text-center">
                <h1
                    class="text-4xl font-bold tracking-tight text-base-content sm:text-4xl"
                >
                    Good morning User
                </h1>
                <p class="mb-6 mt-2 text-lg leading-8 text-base-content/70">
                    Today you have worked...
                </p>
                <p
                    class="text-5xl font-bold tracking-tight text-accent sm:text-8xl"
                >
                    00:00:00
                </p>
                <div class="divider"></div>
            </div>
        </div>
    </div>
</x-app-layout>

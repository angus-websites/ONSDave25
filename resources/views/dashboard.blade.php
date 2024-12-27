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

            <!-- Time picker -->
            <div
                   class="card"
               >
                <div class="relative card-body flex flex-col items-center justify-center">
                    <div class="flex items-center space-x-6">
                        <!-- Hour Selector -->
                        <div class="flex flex-col items-center gap-y-2" data-input-number>
                            <button class="btn btn-square btn-text"
                                    aria-label="Increment button" data-input-number-increment>
                                <span class="icon-[tabler--caret-up-filled] size-10"></span>
                            </button>
                            <input
                                type="text"
                                class="w-24 text-center text-2xl input input-lg "
                                data-input-number-input
                            />
                            <button class="btn btn-square btn-text"
                            aria-label="Decrement button" data-input-number-decrement>
                                <span class="icon-[tabler--caret-down-filled] size-10"></span>
                            </button>
                        </div>

                        <span class="text-3xl">:</span>

                        <!-- Minute Selector -->
                        <div class="flex flex-col items-center gap-y-2" data-input-number>
                            <button class="btn btn-square btn-text"
                                    aria-label="Increment button" data-input-number-increment>
                                <span class="icon-[tabler--caret-up-filled] size-10"></span>
                            </button>
                            <input
                                type="text"
                                class="w-24 text-center text-2xl input input-lg "
                                data-input-number-input
                            />
                            <button class="btn btn-square btn-text"
                            aria-label="Decrement button" data-input-number-decrement>
                                <span class="icon-[tabler--caret-down-filled] size-10"></span>
                            </button>
                        </div>

                    </div>
                    <button
                        class="btn btn-square btn-primary btn-soft absolute right-4 top-1/2 -translate-y-1/2 flex items-center justify-center"
                        aria-label="Icon Button"
                    >
                        <span class="icon-[tabler--refresh]"></span>
                    </button>
                </div>

                <div class="card-footer">
                </div>

               </div>

            <!-- Button -->
            <div class="my-10 text-center">
                <button class="btn btn-primary btn-lg btn-success">Clock in</button>

            </div>
        </div>
    </div>
</x-app-layout>

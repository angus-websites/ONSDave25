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
                <livewire:pages.dashboard.clock-display />
            </div>

            <div class="divider my-10"></div>

            <!-- Specify time button -->
            <div class="text-center mb-5">
               <button type="button" class="collapse-toggle link link-primary inline-flex items-center" id="show-specify-time" aria-expanded="false" aria-controls="show-specify-time-block" data-collapse="#specify-time-block" >
                 <span class="collapse-open:hidden">Specify clock in time</span>
                 <span class="collapse-open:block hidden">Use current time</span>
                 <span class="icon-[tabler--chevron-down] collapse-open:rotate-180 ms-2 size-4"></span>
               </button>
            </div>

            <!-- Time picker -->
            <div id="specify-time-block" class="collapse hidden w-full overflow-hidden transition-[height] duration-300" aria-labelledby="show-specify-time" >
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
                                    type="number"
                                    class="w-24 text-center text-2xl input input-lg"
                                    min="0" max="23"
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
                                    type="number"
                                    class="w-24 text-center text-2xl input input-lg "
                                    min="0" max="59"
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

                    <div class="card-footer text-center ">
                        <p class="text-sm text-base-content/75">Please select a specific time to clock in from, use the reset button on the right to set the time to now</p>
                    </div>

                   </div>
            </div>

            <!-- Button -->
            <div class="my-10 text-center">
                <button class="btn btn-primary btn-lg btn-success">Clock in</button>
            </div>
        </div>
    </div>
</x-app-layout>

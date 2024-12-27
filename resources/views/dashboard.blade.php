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
                   class="relative flex flex-col mx-auto items-center justify-center p-8 bg-base-100 rounded-lg  w-full lg:w-3/4 max-w-2xl"
               >
                   <div class="flex items-center space-x-6">
                       <!-- Hour Selector -->
                       <div class="flex flex-col items-center">
                           <button
                               class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                               @click="incrementHours"
                           >
                               &#9650;
                           </button>
                           <input
                               type="text"
                               class="w-24 text-center text-2xl input input-lg "
                               v-model="hours"
                               @input="manualUpdate"
                           />
                           <button
                               class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                               @click="decrementHours"
                           >
                               &#9660;
                           </button>
                       </div>

                       <span class="text-3xl">:</span>

                       <!-- Minute Selector -->
                       <div class="flex flex-col items-center">
                           <button
                               class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                               @click="incrementMinutes"
                           >
                               &#9650;
                           </button>
                           <input
                               type="text"
                               class="w-24 text-center text-2xl input input-lg "
                               v-model="minutes"
                               @input="manualUpdate"
                           />
                           <button
                               class="text-2xl text-gray-600 hover:text-gray-800 focus:outline-none"
                               @click="decrementMinutes"
                           >
                               &#9660;
                           </button>
                       </div>
                   </div>


                <button class="btn btn-square btn-primary btn-soft absolute right-4 top-1/2 -translate-y-1/2" aria-label="Icon Button">
                    <span class="icon-[tabler--refresh]"></span>
                </button>
               </div>
        </div>
    </div>
</x-app-layout>

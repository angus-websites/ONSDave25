<p class="text-5xl font-bold tracking-tight text-accent sm:text-8xl">
    <span id="timeWorked">{{ $timeWorked }}</span>
</p>

@push('scripts')
<script>
    let timer; // Variable to hold the timer interval
    let secondsWorked = 0; // Track the time worked in seconds

    // Convert the initial displayed time to seconds for the timer
    function convertTimeToSeconds(time) {
        const [hours, minutes, seconds] = time.split(':').map(Number);
        return hours * 3600 + minutes * 60 + seconds;
    }

    // Listen for the 'clockedIn' event from Livewire
    Livewire.on('clockedIn', () => {
        // Get the current timeWorked element
        const timeWorkedElement = document.getElementById('timeWorked');

        // Convert the current time displayed in the UI to seconds
        const currentTime = timeWorkedElement.innerText;
        secondsWorked = convertTimeToSeconds(currentTime);

        // If there's already an active timer, clear it
        if (timer) {
            clearInterval(timer);
        }

        // Start a new timer
        timer = setInterval(() => {
            secondsWorked++;

            // Convert seconds to hours, minutes, and seconds
            const hours = Math.floor(secondsWorked / 3600);
            const minutes = Math.floor((secondsWorked % 3600) / 60);
            const seconds = secondsWorked % 60;

            // Format the time as HH:MM:SS
            const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

            // Update the timeWorked element with the new time
            timeWorkedElement.innerText = formattedTime;
        }, 1000);
    });

    // Listen for the 'clockedOut' event from Livewire
    Livewire.on('clockedOut', () => {
        // Stop the timer when clocking out
        if (timer) {
            clearInterval(timer);
        }
    });
</script>
@endpush

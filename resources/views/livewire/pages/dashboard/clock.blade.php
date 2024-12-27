<div>
    @error('clock')
    <div class="alert alert-soft alert-error" role="alert">
      {{ $message }}
    </div>
    @enderror
    @if($nextTimeRecordType == \App\Enums\TimeRecordType::CLOCK_IN)
        <!-- Button -->
        <div class="my-10 text-center">
            <button
                wire:click="clock"
                class="btn btn-lg btn-success">
                Clock in
            </button>
        </div>
    @else
        <!-- Button -->
        <div class="my-10 text-center">
            <button
                wire:click="clock"
                class="btn btn-lg btn-error">
                Clock out
            </button>
        </div>
    @endif

</div>

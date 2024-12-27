<?php

namespace App\Livewire\Pages\Dashboard;

use App\Enums\TimeRecordType;
use App\Exceptions\ShortSessionDurationException;
use App\Services\TimeRecordService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Clock extends Component
{
    public TimeRecordType $nextTimeRecordType;

    protected TimeRecordService $timeRecordService;

    public function __construct()
    {
        $this->timeRecordService = app(TimeRecordService::class);
    }

    public function mount(): void
    {
        $this->nextTimeRecordType = $this->timeRecordService->getNextTimeRecordType(Auth::id());
    }

    /**
     * Clock in the user
     */
    public function clock(): void
    {
        $userID = Auth::id();
        try {
            $this->timeRecordService->handleClock($userID, 'Europe/London');
        } catch (ShortSessionDurationException $e) {
            $this->addError("clock", "The session duration is too short. It has been deleted");
        }

        // Refresh the next time record type
        $this->nextTimeRecordType = $this->timeRecordService->getNextTimeRecordType($userID);

        // If the next time record type is clock OUT, emit the "clockedOut" event
        if ($this->nextTimeRecordType === TimeRecordType::CLOCK_OUT) {
            $this->dispatch('clockedIn');
        }else{
            // If the next time record type is clock IN, emit the "clockedIn" event
            $this->dispatch('clockedOut');
        }

        $this->dispatch('timeWorkedUpdated');
    }

    public function render()
    {
        return view('livewire.pages.dashboard.clock');
    }
}

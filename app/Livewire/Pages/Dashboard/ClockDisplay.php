<?php

namespace App\Livewire\Pages\Dashboard;

use App\Services\TimeRecordService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class ClockDisplay extends Component
{
    public string $timeWorked = "00:00:00";
    protected TimeRecordService $timeRecordService;

    public function __construct()
    {
        $this->timeRecordService = app(TimeRecordService::class);
    }

    #[On('timeWorkedUpdated')]
    public function updateTimeWorked(): void
    {
        $userID = Auth::id();
        // This method will be called when the event is emitted
        $secondsWorked = $this->timeRecordService->getSecondsWorkedToday($userID);
        $this->timeWorked = gmdate("H:i:s", $secondsWorked);
    }

    public function mount()
    {
        $this->updateTimeWorked();
    }

    public function render()
    {
        return view('livewire.pages.dashboard.clock-display');
    }
}

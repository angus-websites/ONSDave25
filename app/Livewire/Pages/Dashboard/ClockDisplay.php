<?php

namespace App\Livewire\Pages\Dashboard;

use App\Services\TimeRecordService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClockDisplay extends Component
{
    public $timeWorked = "00:01:00";
    protected TimeRecordService $timeRecordService;

    public function __construct()
    {
        $this->timeRecordService = app(TimeRecordService::class);
    }

    public function mount()
    {
        $userID = Auth::id();
        $timeWorked = $this->timeRecordService->getTimeWorkedToday($userID);
    }

    public function render()
    {
        return view('livewire.pages.dashboard.clock-display');
    }
}

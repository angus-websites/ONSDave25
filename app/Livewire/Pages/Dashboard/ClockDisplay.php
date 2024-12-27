<?php

namespace App\Livewire\Pages\Dashboard;

use Livewire\Component;

class ClockDisplay extends Component
{
    public $timeWorked = "00:01:00";

    public function render()
    {
        return view('livewire.pages.dashboard.clock-display');
    }
}

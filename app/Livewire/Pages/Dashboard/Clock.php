<?php

namespace App\Livewire\Pages\Dashboard;

use Livewire\Component;

class Clock extends Component
{
    public int $currentHour;
    public int $currentMinute;

    public bool $useCurrentTime;

    public function mount()
    {
        $currentHour = date('H');
        $currentMinute = date('i');

        $this->currentHour = (int) $currentHour;
        $this->currentMinute = (int) $currentMinute;
        $this->useCurrentTime = true;
    }

     public function updatedSpecifiedTime($value)
     {
         if (!$this->validateTime($value)) {
             $this->addError('specifiedTime', 'Invalid time format. Use HH:MM.');
         }
     }

     public function toggleTimeInput()
     {
         $this->useCurrentTime = !$this->useCurrentTime;

         if ($this->useCurrentTime) {
             $this->specifiedTime = $this->currentTime;
         }
     }

     public function resetTime()
     {
         $this->specifiedTime = $this->currentTime;
     }

     private function validateTime($time)
     {
         return preg_match('/^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/', $time);
     }

    public function render()
    {
        return view('livewire.pages.dashboard.clock');
    }
}

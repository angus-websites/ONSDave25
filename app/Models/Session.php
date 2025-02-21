<?php

namespace App\Models;

use Carbon\Carbon;

class Session
{
    public Carbon $clockIn;
    public Carbon|null $clockOut;


    public function __construct(Carbon $clockIn, ?Carbon $clockOut)
    {
        $this->clockIn = $clockIn;
        $this->clockOut = $clockOut;
    }

    public function isOnGoing(): bool
    {
        return $this->clockOut === null;
    }

    /**
     * Get the duration of the session in seconds.
     */
    public function getDurationInSeconds(): int
    {
        if ($this->isOnGoing()) {
            return $this->clockIn->diffInSeconds(Carbon::now());
        }
        return $this->clockIn->diffInSeconds($this->clockOut);
    }

}

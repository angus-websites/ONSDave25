<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class SessionCollection extends Collection
{
    /**
     * Calculate the total duration of all sessions in seconds.
     */
    public function getTotalDurationInSeconds(): int
    {
        return $this->sum(fn($session) => $session->getDurationInSeconds());
    }

    /**
     * Calculate the total duration of all sessions in minutes.
     */
    public function getTotalDurationInMinutes(): float
    {
        return $this->getTotalDurationInSeconds() / 60;
    }
}

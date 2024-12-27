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
     * Get the total duration of all sessions as a formatted string (H:i:s).
     */
    public function getTotalDurationFormatted(): string
    {
        return gmdate('H:i:s', $this->getTotalDurationInSeconds());
    }

    /**
     * Filter sessions based on a condition (e.g., only sessions longer than a certain duration).
     */
    public function filterByDuration(int $minSeconds): self
    {
        return $this->filter(fn($session) => $session->getDurationInSeconds() > $minSeconds);
    }
}

<?php

namespace App\Collections;

use App\Enums\TimeRecordType;
use Illuminate\Support\Collection;

/**
 * A time record collection is a collection of time records that are grouped into sessions
 */
class TimeRecordCollection extends Collection
{
    /**
     * Create a TimeRecordCollection from raw records.
     */
    public static function fromRecords(Collection $records): self
    {
        $sessions = [];
        $currentSession = null;

        foreach ($records as $record) {
            if ($record->type === TimeRecordType::CLOCK_IN) {
                // Start a new session
                $currentSession = ['clock_in' => $record];
            } elseif ($record->type === TimeRecordType::CLOCK_OUT) {
                if ($currentSession) {
                    // Complete the session with the clock-out
                    $currentSession['clock_out'] = $record;
                    $sessions[] = $currentSession;
                    $currentSession = null;
                }
            }
        }

        // Add incomplete session (e.g., missing clock-out)
        if ($currentSession) {
            $sessions[] = $currentSession;
        }

        return new self($sessions);
    }
}

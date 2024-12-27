<?php

namespace App\Repositories;

use App\Collections\SessionCollection;
use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\Session;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * The TimeRecordRepository class is responsible for handling database operations
 * related to time records. It is a concrete implementation of the TimeRecordRepositoryInterface.
 */
class TimeRecordRepository implements TimeRecordRepositoryInterface
{
    public function createTimeRecord(array $data)
    {
        return TimeRecord::create($data);
    }

    public function getLastRecordForUser(int $userId): ?TimeRecord
    {
        return TimeRecord::where('user_id', $userId)->latest()->first();
    }

    public function removeLastRecordForUser(int $userId): void
    {
        $lastRecord = $this->getLastRecordForUser($userId);
        $lastRecord?->delete();
    }

    public function getAllRecordsForUser(int $userId): Collection
    {
        return TimeRecord::where('user_id', $userId)->get();
    }

    /**
     * Get the sessions for the specified day and user.
     */
    public function getSessionsForDay(int $userId, Carbon $day): SessionCollection
    {
        // Fetch time records for the day, ordered by 'recorded_at'
        $records = TimeRecord::where('user_id', $userId)
            ->whereDate('recorded_at', $day->toDateString())
            ->orderBy('recorded_at')
            ->get();

        // Initialize an empty collection to store sessions
        $sessions = collect();

        // Process the records to create sessions
        $currentSession = null;
        $records->each(function ($record) use (&$sessions, &$currentSession) {
            if ($record->type === TimeRecordType::CLOCK_IN) {
                // Start a new session
                $currentSession = ['clock_in' => $record];
            } elseif ($record->type === TimeRecordType::CLOCK_OUT && $currentSession) {
                // Complete the session
                $sessions->push(new Session(
                    $currentSession['clock_in']->recorded_at,
                    $record->recorded_at
                ));
                $currentSession = null;
            }
        });

        // Handle any remaining session that doesn't have a clock-out
        if ($currentSession) {
            $sessions->push(new Session(
                $currentSession['clock_in']->recorded_at,
                null
            ));
        }

        // Return a SessionCollection
        return new SessionCollection($sessions);
    }

}

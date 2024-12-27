<?php

namespace App\Repositories;

use App\Collections\TimeRecordCollection;
use App\Contracts\TimeRecordRepositoryInterface;
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
     * Get time records for the specified day and user.
     */
    public function getTimeRecordsForDay(int $userId, Carbon $day): TimeRecordCollection
    {
        // Fetch all the time records for the specified day and user
        $records = TimeRecord::where('user_id', $userId)
            ->whereDate('recorded_at', $day->toDateString())
            ->orderBy('recorded_at')
            ->get();

        // Group the records into sessions (clock-in and clock-out pairs)
        return TimeRecordCollection::fromRecords($records);
    }
}

<?php

namespace App\Contracts;

use App\Collections\TimeRecordCollection;
use App\Models\TimeRecord;
use Carbon\Carbon;

/**
 * The TimeRecordRepositoryInterface is a contract that defines the methods that should be implemented
 * for a TimeRecordRepository, this is used to easily switch between different implementations of the
 * TimeRecordRepository class and also mock the repository in tests.
 */
interface TimeRecordRepositoryInterface
{
    public function createTimeRecord(array $data);

    public function getLastRecordForUser(int $userId): ?TimeRecord;

    public function removeLastRecordForUser(int $userId): void;

    public function getAllRecordsForUser(int $userId): iterable;

    public function getTimeRecordsForDay(int $userId, Carbon $day): TimeRecordCollection;
}

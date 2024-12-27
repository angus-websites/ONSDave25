<?php

namespace App\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Exceptions\InvalidTimeProvidedException;
use App\Exceptions\ShortSessionDurationException;
use App\Models\TimeRecord;
use Carbon\Carbon;
use DateTime;
use Exception;

/**
 * The TimeRecordService class is responsible for handling the business logic
 * related to time records.
 */
class TimeRecordService
{
    protected TimeRecordRepositoryInterface $timeRecordRepository;

    public function __construct(TimeRecordRepositoryInterface $timeRecordRepository)
    {
        $this->timeRecordRepository = $timeRecordRepository;
    }

    /**
     * Get the number of seconds worked today by the given user.
     */
    public function getSecondsWorkedToday(int $userId): int
    {
        $sessionsToday = $this->timeRecordRepository->getSessionsForDay($userId, Carbon::now());

        // Calculate the total minutes worked today
        return $sessionsToday->getTotalDurationInSeconds();

    }

    /**
     * Get the next time record type for the given
     * i.e if they have just clocked in, the next type will be clock out
     */
    public function getNextTimeRecordType(int $userId): TimeRecordType
    {
        $lastTimeRecord = $this->timeRecordRepository->getLastRecordForUser($userId);

        if (!$lastTimeRecord || in_array($lastTimeRecord->type, [TimeRecordType::CLOCK_OUT, TimeRecordType::AUTO_CLOCK_OUT])) {
            return TimeRecordType::CLOCK_IN;
        }

        return TimeRecordType::CLOCK_OUT;
    }


    /**
     * Handle the clock in/out operation for the given user,
     * the userProvidedTime is optional and can be used to override the current time
     *
     * @throws Exception
     */
    public function handleClock(int $userId, string $userLocation, ?Carbon $userProvidedTime = null): void
    {
        // Use the current time if the user didn't provide one, and convert it to UTC
        $userProvidedTime = $this->convertToUtc($userProvidedTime ?? Carbon::now(), $userLocation);

        // Get the user's last time record
        $lastTimeRecord = $this->timeRecordRepository->getLastRecordForUser($userId);

        // Check if the provided time is before the last time record
        if ($lastTimeRecord && $userProvidedTime->lt($lastTimeRecord->recorded_at)) {
            throw new InvalidTimeProvidedException;
        }

        // If the session duration is too short, remove the last time record and return
        if ($lastTimeRecord && $this->isSessionDurationTooShort($lastTimeRecord->recorded_at, $userProvidedTime)) {
            $this->timeRecordRepository->removeLastRecordForUser($userId);
            throw new ShortSessionDurationException;
        }

        // Determine whether to clock in or out
        $this->clockInOrOut($userId, $lastTimeRecord, $userProvidedTime);
    }

    /**
     * Determine whether to clock in or out the user.
     */
    private function clockInOrOut(int $userId, ?TimeRecord $lastTimeRecord, Carbon $userProvidedTime): void
    {
        if (! $lastTimeRecord || $lastTimeRecord->type === TimeRecordType::CLOCK_OUT) {
            // If there's no record or the last record is clock out, clock in
            $this->clockIn($userId, $userProvidedTime);
        } else {
            // Otherwise, clock out
            $this->clockOut($userId, $userProvidedTime);
        }
    }

    /**
     * Clock in the user.
     */
    private function clockIn(int $userId, Carbon $providedTime): void
    {

        // Use the timeRecordRepository to clock in the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $providedTime,
                'type' => TimeRecordType::CLOCK_IN,
            ]
        );
    }

    /**
     * Clock out the user.
     */
    private function clockOut(int $userId, Carbon $providedTime): void
    {
        // Use the timeRecordRepository to clock out the user
        $this->timeRecordRepository->createTimeRecord(
            [
                'user_id' => $userId,
                'recorded_at' => $providedTime,
                'type' => TimeRecordType::CLOCK_OUT,
            ]
        );
    }

    /**
     * Check if the user clocks out too soon after clocking in. if so return true.
     */
    private function isSessionDurationTooShort(Carbon $clockInTime, Carbon $clockOutTime): bool
    {
        return $clockInTime->diffInSeconds($clockOutTime) < TimeRecord::$minimumSessionSeconds;
    }

    /**
     * Convert the provided clock time to UTC based on the user's time zone.
     *
     * @throws Exception
     */
    private function convertToUtc(DateTime $clockTime, string $userTimeZone): Carbon
    {
        // Validate the provided timezone
        if (! in_array($userTimeZone, timezone_identifiers_list())) {
            $userTimeZone = 'Europe/London';
        }

        // Convert the time to UTC
        try {
            return Carbon::parse($clockTime, $userTimeZone)->setTimezone('UTC');
        } catch (Exception $e) {
            throw new Exception('Error converting time to UTC: '.$e->getMessage());
        }
    }
}

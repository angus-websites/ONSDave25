<?php

namespace Tests\Unit\Services;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Exceptions\InvalidTimeProvidedException;
use App\Exceptions\ShortSessionDurationException;
use App\Models\TimeRecord;
use App\Models\User;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Exception;
use Tests\TestCase;

class TimeRecordServiceTest extends TestCase
{
    protected TimeRecordRepositoryInterface $timeRecordRepository;

    protected User $user;

    protected Carbon $testNow;

    /**
     * @throws Exception
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up common objects for all tests
        $this->timeRecordRepository = $this->createMock(TimeRecordRepositoryInterface::class);
        $this->user = UserFactory::new()->create();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Reset the time after each test
        Carbon::setTestNow();
    }

    private function assertTimeRecord(array $data, int $userId, Carbon $expectedTime, TimeRecordType $expectedType): void
    {
        $this->assertEquals($userId, $data['user_id']);
        $this->assertInstanceOf(Carbon::class, $data['recorded_at']);
        $this->assertTrue($data['recorded_at']->eq($expectedTime));
        $this->assertEquals($expectedType, $data['type']);
    }

    /**
     * Test a new user clocking in for the first time in the UK without providing a time
     *
     * @throws Exception
     */
    public function testHandleClockUkNewUserFirstTimeNow()
    {

        // Mock the current time
        $now = Carbon::parse('2024-01-01 10:00:00');
        Carbon::setTestNow($now);

        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($now) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $now
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK
        $timeRecordService->handleClock($this->user->id, 'Europe/London');

    }

    /**
     * Test a new user clocking in for the first time in the UK providing a specific time
     *
     * @throws Exception
     */
    public function testHandleClockUkNewUserFirstTimeSpecificTime()
    {

        // Create a custom time
        $customTime = Carbon::parse('2024-01-01 9:00:00', 'Europe/London');

        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($customTime) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $customTime
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK and a specific time
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $customTime);

    }

    /**
     * Test a new user clocking in for the first time in the UK summer providing a specific time
     *
     * @throws Exception
     */
    public function testHandleClockUkSummerTimeNewUserFirstTimeSpecificTime()
    {
        // Create a custom time in UK Summer Time (BST)
        $customTime = Carbon::parse('2024-06-01 10:00:00', 'Europe/London');

        // Create the UTC equivalent of the custom time
        $utcTime = Carbon::parse('2024-06-01 09:00:00');

        // Expect the createTimeRecord method to be called once with the correct data
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($utcTime) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at'] == $utcTime
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method with the user id located in the UK and a specific time
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $customTime);
    }

    /**
     * Test a new user clocking in, then clocking out in the UK without providing a time
     *
     * @throws Exception
     */
    public function testHandleClockUkNewUserTwiceNow()
    {
        // Mock the current time for clock in (start of the day in the UK timezone)
        $start = Carbon::parse('2024-01-01 09:00:00', 'Europe/London');
        $end = Carbon::parse('2024-01-01 17:00:00', 'Europe/London');

        // Define the expected calls to the mock repository, this should be called twice
        // The first call should be for clock in and the second call should be for clock out
        $matcher = $this->exactly(2);
        $this->timeRecordRepository->expects($matcher)
            ->method('createTimeRecord')
            ->willReturnCallback(function ($data) use ($start, $end, $matcher) {

                // Match parameters based on the invocation count
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertTimeRecord($data, $this->user->id, $start, TimeRecordType::CLOCK_IN),
                    2 => $this->assertTimeRecord($data, $this->user->id, $end, TimeRecordType::CLOCK_OUT),
                };
            });

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Set the current time to the start of the day
        Carbon::setTestNow($start);

        // Call the handleClock method to clock in
        $timeRecordService->handleClock($this->user->id, 'Europe/London');

        // Mock the time for clock out
        Carbon::setTestNow($end);

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        // This will be used to mock the last record for the user
        $clock_in_mock = TimeRecord::make([
            'recorded_at' => $start,
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock_in_mock);

        // Call the handleClock method to clock out
        $timeRecordService->handleClock($this->user->id, 'Europe/London');
    }

    /**
     * Test a new user clocking in automatically clocking out manually with a time that is before the clock in time
     * throws an exception
     *
     * @throws Exception|Exception
     */
    public function testHandleClockUkNewUserClockOutBeforeClockIn()
    {
        // Mock the current time for clock in (start of the day in the UK timezone)
        $start = Carbon::parse('2024-01-01 09:00:00', 'Europe/London');
        $end = Carbon::parse('2024-01-01 08:00:00', 'Europe/London');

        // Define the expected calls to the mock repository, this should be called only once, for clock in
        $matcher = $this->exactly(1);
        $this->timeRecordRepository->expects($matcher)
            ->method('createTimeRecord')
            ->willReturnCallback(function ($data) use ($start, $matcher) {

                // Match parameters based on the invocation count
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertTimeRecord($data, $this->user->id, $start, TimeRecordType::CLOCK_IN),
                };
            });

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Set the current time to the start of the day
        Carbon::setTestNow($start);

        // Call the handleClock method to clock in
        $timeRecordService->handleClock($this->user->id, 'Europe/London');

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        // This will be used to mock the last record for the user
        $clock_in_mock = TimeRecord::make([
            'recorded_at' => $start,
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock_in_mock);

        // Assert an exception is thrown
        $this->expectException(InvalidTimeProvidedException::class);

        // Call the handleClock method to clock out but manually provide a time that is before the clock in time
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $end);

    }

    /**
     * Test that when a clock out is called within the minimum session duration, the last record is deleted and the
     * session is not created
     *
     * @throws Exception
     */
    public function testHandleClockUkNewUserClockOutWithinMinimumSession()
    {
        // Mock the start and end times
        $start = Carbon::parse('2024-01-01 09:00:00', 'Europe/London');
        $end = Carbon::parse('2024-01-01 9:00:15', 'Europe/London');

        // Define the expected calls to the mock repository
        // The first call should be to createTimeRecord to clock
        // The second call should be to removeLastRecordForUser to delete the last record
        // Expect `createTimeRecord` to be called once for clocking in
        $this->timeRecordRepository->expects($this->once())
            ->method('createTimeRecord')
            ->with($this->callback(function ($data) use ($start) {
                return $data['user_id'] === $this->user->id
                    && $data['recorded_at'] instanceof Carbon
                    && $data['recorded_at']->eq($start)
                    && $data['type'] === TimeRecordType::CLOCK_IN;
            }));

        // Expect `removeLastRecordForUser` to be called once for removing the last record
        $this->timeRecordRepository->expects($this->once())
            ->method('removeLastRecordForUser')
            ->with($this->user->id);

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method to clock in
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $start);

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        // This will be used to mock the last record for the user
        $clock_in_mock = TimeRecord::make([
            'recorded_at' => $start,
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock_in_mock);

        // Assert the ShortSessionDurationException is thrown
        $this->expectException(ShortSessionDurationException::class);

        // Call the handleClock method to clock out
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $end);

    }

    /**
     * Test clock with different time zones, test that when a user clocks in and out in different time zones,
     *
     * @throws Exception
     */
    public function testHandleClockDifferentTimeZones()
    {
        // Clock in at 9:00 AM in the UK
        $start = Carbon::parse('2024-01-01 09:00:00', 'Europe/London');
        $expectedStart = Carbon::parse('2024-01-01 09:00:00', 'UTC');

        // Clock out at 5pm in France
        $end = Carbon::parse('2024-01-01 17:00:00', 'Europe/Paris');
        $expectedEnd = Carbon::parse('2024-01-01 16:00:00', 'UTC');

        // Define the expected calls to the mock repository
        // The first call should be to createTimeRecord to clock in
        // The second call should be to createTimeRecord to clock out
        // The times should be converted to UTC before saving
        $matcher = $this->exactly(2);
        $this->timeRecordRepository->expects($matcher)
            ->method('createTimeRecord')
            ->willReturnCallback(function ($data) use ($expectedStart, $expectedEnd, $matcher) {

                // Match parameters based on the invocation count
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertTimeRecord($data, $this->user->id, $expectedStart, TimeRecordType::CLOCK_IN),
                    2 => $this->assertTimeRecord($data, $this->user->id, $expectedEnd, TimeRecordType::CLOCK_OUT),
                };
            });

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Call the handleClock method to clock in
        $timeRecordService->handleClock($this->user->id, 'Europe/London', $start);

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        // This will be used to mock the last record for the user
        $clock_in_mock = TimeRecord::make([
            'recorded_at' => $expectedStart,
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock_in_mock);

        // Call the handleClock method to clock out
        $timeRecordService->handleClock($this->user->id, 'Europe/Paris', $end);
    }

    public function testGetNextTimeRecordType()
    {
        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Create a mock TimeRecord object with the type of TimeRecordType::CLOCK_IN
        // This will be used to mock the last record for the user
        $clock_in_mock = TimeRecord::make([
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        // Mock the getLastRecordForUser method to return the mock TimeRecord object
        $this->timeRecordRepository->method('getLastRecordForUser')->willReturn($clock_in_mock);

        // Call the getUserNextTimeRecordType method
        $nextType = $timeRecordService->getNextTimeRecordType($this->user->id);

        // Assert the next type is TimeRecordType::CLOCK_OUT
        $this->assertEquals(TimeRecordType::CLOCK_OUT, $nextType);

    }

    public function testGetMinutesWorkedToday()
    {

        $today = Carbon::parse('2024-01-01');

        // Create a new instance of TimeRecordService
        $timeRecordService = new TimeRecordService($this->timeRecordRepository);

        // Mock the getTimeRecordsForDay method to return an array of TimeRecord objects
        $this->timeRecordRepository->method('getTimeRecordsForDay')->willReturn(
            // TODO
        );

        // Call the getMinutesWorkedToday method
        $minutesWorked = $timeRecordService->getMinutesWorkedToday($this->user->id);

        // Assert the minutes worked is 480 (8 hours)
        $this->assertEquals(480, $minutesWorked);
    }


}

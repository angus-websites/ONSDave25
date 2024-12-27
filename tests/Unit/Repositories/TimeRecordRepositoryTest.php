<?php

namespace Tests\Unit\Repositories;

use App\Contracts\TimeRecordRepositoryInterface;
use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
use App\Models\User;
use App\Repositories\TimeRecordRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimeRecordRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TimeRecordRepositoryInterface $timeRecordRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->timeRecordRepository = new TimeRecordRepository;
    }

    public function testCreateTimeRecord()
    {
        $user = User::factory()->create();
        $data = [
            'user_id' => $user->id,
            'recorded_at' => now(),
            'type' => TimeRecordType::CLOCK_IN,
            'notes' => 'Test note',
        ];

        $timeRecord = $this->timeRecordRepository->createTimeRecord($data);

        $this->assertDatabaseHas('time_records', $data);
        $this->assertInstanceOf(TimeRecord::class, $timeRecord);
    }

    public function testGetLastRecordForUser()
    {
        $user = User::factory()->create();
        $timeRecord = TimeRecord::factory()->create(['user_id' => $user->id]);

        $lastRecord = $this->timeRecordRepository->getLastRecordForUser($user->id);

        $this->assertEquals($timeRecord->id, $lastRecord->id);
    }

    public function testRemoveLastRecordForUser()
    {
        $user = User::factory()->create();
        $timeRecord = TimeRecord::factory()->create(['user_id' => $user->id]);

        $this->timeRecordRepository->removeLastRecordForUser($user->id);

        $this->assertDatabaseMissing('time_records', ['id' => $timeRecord->id]);
    }

    public function testGetAllRecordsForUser()
    {
        $user = User::factory()->create();
        $timeRecords = TimeRecord::factory()->count(3)->create(['user_id' => $user->id]);

        $records = $this->timeRecordRepository->getAllRecordsForUser($user->id);

        $this->assertCount(3, $records);
    }

    public function testGetTimeRecordsForDaySingle()
    {
        $user = User::factory()->create();

        // Set fake day as january 1st, 2021
        $today = Carbon::create(2021, 1, 1);

        // Create a clock in at 9am and clock out at 5pm
        TimeRecord::factory()->create([
            'user_id' => $user->id,
            'recorded_at' => $today->setHour(9),
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        TimeRecord::factory()->create([
            'user_id' => $user->id,
            'recorded_at' => $today->setHour(17),
            'type' => TimeRecordType::CLOCK_OUT,
        ]);


        $records = $this->timeRecordRepository->getTimeRecordsForDay($user->id, $today);
        $this->assertCount(1, $records);

        // Assert the structure of the returned collection
        $this->assertArrayHasKey('clock_in', $records[0]);
        $this->assertArrayHasKey('clock_out', $records[0]);

    }

    public function testGetTimeRecordsForDaySingleNoClockOut()
    {
        $user = User::factory()->create();

        // Set fake day as january 1st, 2021
        $today = Carbon::create(2021, 1, 1);

        // Create a clock in at 9am and clock out at 5pm
        TimeRecord::factory()->create([
            'user_id' => $user->id,
            'recorded_at' => $today->setHour(9),
            'type' => TimeRecordType::CLOCK_IN,
        ]);

        $records = $this->timeRecordRepository->getTimeRecordsForDay($user->id, $today);
        $this->assertCount(1, $records);

        // Assert the structure of the returned collection
        $this->assertArrayHasKey('clock_in', $records[0]);

    }
}

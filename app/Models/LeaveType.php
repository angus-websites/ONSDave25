<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'description', 'paid'];

    public function leaveRecords()
    {
        return $this->hasMany(LeaveRecord::class);
    }
}

<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class HourAttendance extends Model
{
    protected $table = 'hours_attendance_management';
	protected $primaryKey = 'id';
	protected $html = '';
}

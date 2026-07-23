<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Learner;
use App\Models\LearnerAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LearnerAttendanceController extends Controller
{
    public function index()
    {
        // $learners = Learner::all();
        // return view('admin.attendance.index', compact('learners'));
        $learners = Learner::all();
        $today = Carbon::today()->toDateString();

       $attendances = LearnerAttendance::with('learner')
        ->where('date', $today)
        ->orderByDesc('am_in')
        ->paginate(10); // or any number of entries per page

        return view('admin.attendance.index', compact('learners', 'attendances', 'today'));
    }

   public function store(Request $request)
    {
        $request->validate([
            'learner_id' => 'required|exists:learners,id',
            'session' => 'required|in:am_in,am_out,pm_in,pm_out',
        ]);

        // Record the attendance
        $attendance = LearnerAttendance::firstOrCreate([
            'learner_id' => $request->learner_id,
            'date' => today(),
        ]);

        // Prevent duplicate logging for the same session
        if (!is_null($attendance->{$request->session})) {
            return $request->expectsJson()
                ? response()->json(['status' => 'warning', 'message' => 'Sesi ini sudah tercatat sebelumnya.'], 200)
                : redirect()->back()->with('warning', 'Sesi ini sudah tercatat sebelumnya.');
        }

        $attendance->{$request->session} = now()->format('H:i:s');
        $attendance->save();

        return $request->expectsJson()
            ? response()->json(['status' => 'success', 'message' => 'Absensi berhasil dicatat.'], 200)
            : redirect()->back()->with('success', 'Absensi berhasil dicatat.');
    }

}


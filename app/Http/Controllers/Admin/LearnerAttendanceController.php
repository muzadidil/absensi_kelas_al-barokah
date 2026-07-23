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

    public function lookupLearner(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $learner = Learner::where('qr_code', $request->qr_code)->first();

        if (!$learner) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => 'found',
            'learner' => [
                'id' => $learner->id,
                'name' => $learner->lname . ', ' . $learner->fname,
            ]
        ]);
    }

   public function store(Request $request)
    {
        // Determine if learner_id is already provided (preferred for AJAX) 
        if (!$request->has('learner_id') && $request->has('qr_code')) {
            $learner = Learner::where('qr_code', $request->qr_code)->first();

            if (!$learner) {
                return $request->expectsJson()
                    ? response()->json(['status' => 'warning', 'message' => 'Invalid or unregistered QR code.'], 400)
                    : redirect()->back()->with('warning', 'Invalid or unregistered QR code.');
            }

            // Manually add learner_id to the request object (this works only with traditional forms)
            $request->request->add(['learner_id' => $learner->id]);
        }

        // Validate the request (will work if learner_id is present — either from QR or direct)
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
                ? response()->json(['status' => 'warning', 'message' => 'This session is already logged.'], 200)
                : redirect()->back()->with('warning', 'This session is already logged.');
        }

        $attendance->{$request->session} = now()->format('H:i:s');
        $attendance->save();

        return $request->expectsJson()
            ? response()->json(['status' => 'success', 'message' => 'Attendance logged successfully.'], 200)
            : redirect()->back()->with('success', 'Attendance logged successfully.');
    }

}


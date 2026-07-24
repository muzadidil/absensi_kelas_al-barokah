<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MailLog;   
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $learnerCount = DB::table('learners')->count();
        $guruCount = User::whereHas('roles', fn ($q) => $q->where('name', 'guru'))->count();
        $mailLogCount = DB::table('email_logs')->count();
        $attendanceCount = DB::table('attendances')->count();

        return view('admin.dashboard', compact(
            'userCount',
            'learnerCount',
            'guruCount',
            'attendanceCount',
            'mailLogCount'
        ));
    }
}

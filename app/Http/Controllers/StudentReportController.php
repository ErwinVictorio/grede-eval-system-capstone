<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\Quiz_exam_activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentReportController extends Controller
{
    /**
     * Show combined report for a student (attendance + quiz + exam)
     */
    public function show(Request $request, $studentId)
    {
        $student = Student::findOrFail($studentId);
        
        // Check authorization - student must belong to logged-in teacher
        if ($student->teacher_id !== Auth::id()) {
            abort(403);
        }

        // Allow filtering by semester and year via query params
        $semester = (int) $request->query('semester', 0); // 0 = all
        $year = (int) $request->query('year', Carbon::now()->year);

        // compute date range for semester if selected (two semesters per year)
        // semesters: 1 => Jan-Jun, 2 => Jul-Dec
        if ($semester === 1) {
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 6, 30)->endOfDay();
        } elseif ($semester === 2) {
            $start = Carbon::create($year, 7, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
        } else {
            // full year
            $start = Carbon::create($year, 1, 1)->startOfDay();
            $end = Carbon::create($year, 12, 31)->endOfDay();
        }

        // Get attendance records within range
        $attendanceRecords = Attendance::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        // Get quiz records within range
        $quizRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'quiz')
            ->whereBetween('date_taken', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date_taken', 'desc')
            ->get();

        // Get exam records within range
        $examRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'exam')
            ->whereBetween('date_taken', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date_taken', 'desc')
            ->get();

        // Get activity records within range
        $activityRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'activity')
            ->whereBetween('date_taken', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date_taken', 'desc')
            ->get();

        // Calculate attendance stats
        $totalAttendanceDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('present', true)->count();
        $attendancePercentage = $totalAttendanceDays > 0 ? ($presentDays / $totalAttendanceDays) * 100 : 0;

        // Calculate quiz stats
        $totalQuizzes = $quizRecords->count();
        $averageQuizScore = $totalQuizzes > 0 ? $quizRecords->avg('score') : 0;
        $highestQuizScore = $totalQuizzes > 0 ? $quizRecords->max('score') : 0;

        // Calculate exam stats
        $totalExams = $examRecords->count();
        $averageExamScore = $totalExams > 0 ? $examRecords->avg('score') : 0;
        $highestExamScore = $totalExams > 0 ? $examRecords->max('score') : 0;

        // Calculate activity stats
        $totalActivities = $activityRecords->count();
        $averageActivityScore = $totalActivities > 0 ? $activityRecords->avg('score') : 0;
        $highestActivityScore = $totalActivities > 0 ? $activityRecords->max('score') : 0;
        $lowestActivityScore = $totalActivities > 0 ? $activityRecords->min('score') : 0;

        return view('Report.StudentReport', compact(
            'student',
            'attendanceRecords',
            'quizRecords',
            'examRecords',
            'totalAttendanceDays',
            'presentDays',
            'attendancePercentage',
            'totalQuizzes',
            'averageQuizScore',
            'highestQuizScore',
            'totalExams',
            'averageExamScore',
            'highestExamScore',
            'activityRecords',
            'totalActivities',
            'averageActivityScore',
            'highestActivityScore',
            'lowestActivityScore',
            'semester',
            'year',
            'start',
            'end'
        ));
    }
}

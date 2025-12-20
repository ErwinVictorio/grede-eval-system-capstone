<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Attendance;
use App\Models\EvalutionComment;
use App\Models\Quiz_exam_activity;
use App\Models\TeacherSetting;
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

        // Get project records within range
        $projectRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'project')
            ->whereBetween('date_taken', [$start->toDateString(), $end->toDateString()])
            ->orderBy('date_taken', 'desc')
            ->get();

        // Get recitation records within range
        $recitationRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'recitation')
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

        // Calculate project stats
        $totalProjects = $projectRecords->count();
        $averageProjectScore = $totalProjects > 0 ? $projectRecords->avg('score') : 0;
        $highestProjectScore = $totalProjects > 0 ? $projectRecords->max('score') : 0;
        $lowestProjectScore = $totalProjects > 0 ? $projectRecords->min('score') : 0;

        // Calculate recitation stats
        $totalRecitations = $recitationRecords->count();
        $averageRecitationScore = $totalRecitations > 0 ? $recitationRecords->avg('score') : 0;
        $highestRecitationScore = $totalRecitations > 0 ? $recitationRecords->max('score') : 0;
        $lowestRecitationScore = $totalRecitations > 0 ? $recitationRecords->min('score') : 0;

        // Fetch teacher weight settings
        $settings = TeacherSetting::where('user_id', Auth::id())->first();
        $defaultWeights = [
            'quiz_weight' => 25,
            'exam_weight' => 25,
            'activity_weight' => 25,
            'project_weight' => 15,
            'recitation_weight' => 10,
        ];

        $weights = $settings ? $settings->only(array_keys($defaultWeights)) : $defaultWeights;

        // Compute weighted overall: use only categories that have records
        $numerator = 0;
        $denominator = 0;

        if ($totalQuizzes > 0) {
            $numerator += ($averageQuizScore * ($weights['quiz_weight'] ?? $defaultWeights['quiz_weight']));
            $denominator += ($weights['quiz_weight'] ?? $defaultWeights['quiz_weight']);
        }
        if ($totalExams > 0) {
            $numerator += ($averageExamScore * ($weights['exam_weight'] ?? $defaultWeights['exam_weight']));
            $denominator += ($weights['exam_weight'] ?? $defaultWeights['exam_weight']);
        }
        if ($totalActivities > 0) {
            $numerator += ($averageActivityScore * ($weights['activity_weight'] ?? $defaultWeights['activity_weight']));
            $denominator += ($weights['activity_weight'] ?? $defaultWeights['activity_weight']);
        }
        if ($totalProjects > 0) {
            $numerator += ($averageProjectScore * ($weights['project_weight'] ?? $defaultWeights['project_weight']));
            $denominator += ($weights['project_weight'] ?? $defaultWeights['project_weight']);
        }
        if ($totalRecitations > 0) {
            $numerator += ($averageRecitationScore * ($weights['recitation_weight'] ?? $defaultWeights['recitation_weight']));
            $denominator += ($weights['recitation_weight'] ?? $defaultWeights['recitation_weight']);
        }

        $overallWeighted = $denominator > 0 ? ($numerator / $denominator) : 0;

        // pass the settings and computed overall
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
            'projectRecords',
            'totalProjects',
            'averageProjectScore',
            'highestProjectScore',
            'lowestProjectScore',
            'recitationRecords',
            'totalRecitations',
            'averageRecitationScore',
            'highestRecitationScore',
            'lowestRecitationScore',
            'settings',
            'overallWeighted',
            'semester',
            'year',
            'start',
            'end'
        ));
    }


    public function ShowFlagFormPage($id)
    {
        // Hanapin ang student o mag-error kung wala (404)
        $student = \App\Models\Student::findOrFail($id);

        // Security check: Siguraduhin na ang teacher ay may karapatan sa student na ito
        if ($student->teacher_id !== Auth::id()) {
            return \redirect()->route('login');
        }

        // Ipasa ang $student variable sa view
        return view('Report.FlagStudent', compact('student'));
    }

    /**
     * Logic para i-save ang referral sa database
     */
    public function storeFlag(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'category' => 'required',
            'status' => 'required',
            'comments' => 'required|min:5',
        ]);

        EvalutionComment::create([
            'student_id' => $request->student_id,
            'teacher_id' => Auth::id(),
            'status' => $request->status,
            'comments' => $request->comments,
            'category' => $request->category,
        ]);

        return redirect()->route('Dashboard.teacher')->with('success', 'Student has been flagged for counseling.');
    }
}

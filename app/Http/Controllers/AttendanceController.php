<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Show attendance form for a specific date
     */
    public function show(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        $teacher = Auth::user();

        // Get students taught by this teacher
        $students = Student::where('teacher_id', $teacher->id)->get();

        // Get existing attendance records for the date
        $attendanceRecords = Attendance::where('user_id', $teacher->id)
            ->where('date', $date)
            ->get()
            ->keyBy('full_name'); // Index by student name for quick lookup

        return view('Attendance.Show', compact('students', 'date', 'attendanceRecords', 'teacher'));
    }

    /**
     * Store attendance records for multiple students
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array', // attendance[full_name] = 1 or 0
        ]);

        $teacher = Auth::user();
        $date = $validated['date'];
        $attendanceData = $validated['attendance'];

        // Delete old records for this date and teacher (to avoid duplicates)
        Attendance::where('user_id', $teacher->id)
            ->where('date', $date)  // ← Only deletes records for THIS date
            ->delete();

        // Insert new attendance records
        foreach ($attendanceData as $fullName => $present) {
            $student = Student::where('full_name', $fullName)
                ->where('teacher_id', $teacher->id)
                ->first();

            if ($student) {
                Attendance::create([
                    'full_name' => $fullName,
                    'subject' => $student->subject,
                    'section' => $student->section,
                    'user_id' => $teacher->id,
                    'date' => $date,
                    'present' => (bool) $present,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Attendance recorded successfully for ' . $date);
    }

    /**
     * Get attendance report for a student
     */
    public function report($studentId)
    {
        $student = Student::findOrFail($studentId);
        $attendanceRecords = Attendance::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('present', true)->count();
        $attendancePercentage = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 0;

        return view('Attendance.Report', compact('student', 'attendanceRecords', 'attendancePercentage', 'presentDays', 'totalDays'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Quiz_exam_activity;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function show(Request $request)
    {
        $teacher = Auth::user();
        $students = Student::where('teacher_id', $teacher->id)->get();

        return view('Activity.Show', compact('students', 'teacher'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'activity_title' => 'required|string|max:255',
            'date_taken' => 'required|date',
            // support raw correct answers + total_items or percentage
            'score' => 'required|numeric|min:0',
            'total_items' => 'nullable|integer|min:1|max:1000',
        ]);

        $teacher = Auth::user();
        $student = Student::where('full_name', $validated['full_name'])
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        if (!empty($validated['total_items'])) {
            $rawScore = (float) $validated['score'];
            $totalItems = (int) $validated['total_items'];
            $percentage = $totalItems > 0 ? round(($rawScore / $totalItems) * 100, 1) : 0;
        } else {
            // assume percentage provided
            $percentage = min(100, max(0, (float) $validated['score']));
        }

        Quiz_exam_activity::create([
            'full_name' => $validated['full_name'],
            'subject' => $student->subject,
            'section' => $student->section,
            'user_id' => $teacher->id,
            'activity_type' => 'activity',
            'activity_title' => $validated['activity_title'],
            'date_taken' => $validated['date_taken'],
            'score' => $percentage,
        ]);

        return redirect()->back()->with('success', 'Activity recorded successfully for ' . $validated['full_name']);
    }

    public function edit($id)
    {
        $activity = Quiz_exam_activity::findOrFail($id);
        if ($activity->user_id !== Auth::id()) abort(403);
        return view('Activity.Edit', compact('activity'));
    }

    public function update(Request $request, $id)
    {
        $activity = Quiz_exam_activity::findOrFail($id);
        if ($activity->user_id !== Auth::id()) abort(403);

        if ($request->filled('total_items')) {
            $validated = $request->validate([
                'activity_title' => 'required|string|max:255',
                'date_taken' => 'required|date',
                'score' => 'required|numeric|min:0',
                'total_items' => 'required|integer|min:1|max:1000',
            ]);

            $rawScore = (float) $validated['score'];
            $totalItems = (int) $validated['total_items'];
            $percentage = $totalItems > 0 ? round(($rawScore / $totalItems) * 100, 1) : 0;

            $activity->update([
                'activity_title' => $validated['activity_title'],
                'date_taken' => $validated['date_taken'],
                'score' => $percentage,
            ]);
        } else {
            $validated = $request->validate([
                'activity_title' => 'required|string|max:255',
                'date_taken' => 'required|date',
                'score' => 'required|numeric|min:0|max:100',
            ]);
            $activity->update($validated);
        }

        return redirect()->route('Dashboard.teacher')->with('success', 'Activity updated successfully');
    }

    public function destroy($id)
    {
        $activity = Quiz_exam_activity::findOrFail($id);
        if ($activity->user_id !== Auth::id()) abort(403);
        $fullName = $activity->full_name;
        $activity->delete();
        return redirect()->back()->with('success', 'Activity deleted successfully for ' . $fullName);
    }

    public function report($studentId)
    {
        $student = Student::findOrFail($studentId);
        if ($student->teacher_id !== Auth::id()) abort(403);

        $activityRecords = Quiz_exam_activity::where('full_name', $student->full_name)
            ->where('user_id', Auth::id())
            ->where('activity_type', 'activity')
            ->orderBy('date_taken', 'desc')
            ->get();

        $totalActivities = $activityRecords->count();
        $averageActivityScore = $totalActivities > 0 ? $activityRecords->avg('score') : 0;
        $highestActivityScore = $totalActivities > 0 ? $activityRecords->max('score') : 0;

        return view('Activity.Report', compact('student', 'activityRecords', 'totalActivities', 'averageActivityScore', 'highestActivityScore'));
    }
}

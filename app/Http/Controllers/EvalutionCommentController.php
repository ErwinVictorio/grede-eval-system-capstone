<?php

namespace App\Http\Controllers;

use App\Models\EvalutionComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EvalutionCommentController extends Controller
{
    //

    public function index(Request $request)
    {
        // Build base query with eager loads
        $query = EvalutionComment::with(['student', 'teacher']);

        // Apply search term to comment text or student/teacher name
        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($qbuilder) use ($q) {
                $qbuilder->where('comments', 'like', "%{$q}%")
                    ->orWhereHas('student', function ($s) use ($q) {
                        $s->where('full_name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('teacher', function ($t) use ($q) {
                        $t->where('full_name', 'like', "%{$q}%");
                    });
            });
        }

        // Filter by teacher who referred
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        // Filter by category (if applicable)
        if ($request->filled('category')) {
            $query->where('category', $request->query('category'));
        }

        // Date range filters
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->query('to'));
        }

        // Latest first and paginate
        $evaluations = $query->latest()->paginate(15)->withQueryString();

        // Kunin ang mga Users na ang role ay 'teacher' (o 'instructor') 
        // at bilangin ang flagged students gamit ang relationship 'flagCreated'
        $instructors = User::where('role', 'teacher') // Siguraduhin na 'teacher' ang role name mo
            ->withCount('flagCreated')
            ->get();

        $stats = [
            'high_priority' => EvalutionComment::where('status', 'High')->count(),
            'ongoing' => EvalutionComment::where('status', 'ongoing')->count(),
            'resolved' => EvalutionComment::where('status', 'resolved')
                ->whereMonth('updated_at', now()->month)->count(),
        ];

        return view('Councilor.Dashboard', compact('evaluations', 'stats', 'instructors'));
    }

    /**
     * JSON search endpoint for live search or API usage
     */
    public function search(Request $request)
    {
        $query = EvalutionComment::with(['student', 'teacher']);

        if ($request->filled('q')) {
            $q = $request->query('q');
            $query->where(function ($qbuilder) use ($q) {
                $qbuilder->where('comments', 'like', "%{$q}%")
                    ->orWhereHas('student', function ($s) use ($q) {
                        $s->where('full_name', 'like', "%{$q}%");
                    })
                    ->orWhereHas('teacher', function ($t) use ($q) {
                        $t->where('full_name', 'like', "%{$q}%");
                    });
            });
        }

        $results = $query->latest()->limit(25)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'student' => $item->student?->full_name,
                'teacher' => $item->teacher?->full_name,
                'status' => $item->status,
                'category' => $item->category,
                'comments' => $item->comments,
                'scheduled_at' => optional($item->scheduled_at)->toDateTimeString(),
                'created_at' => $item->created_at->toDateTimeString(),
            ];
        });

        return response()->json(['data' => $results]);
    }

    public function setSchedule(Request $request, $id)
    {
        $request->validate([
            // Pansamantalang alisin ang 'after:now' para ma-test kung gagana
            'scheduled_at' => 'required|date',
        ]);

        $evaluation = EvalutionComment::findOrFail($id);

        // Siguraduhin na ang 'scheduled_at' ay nasa $fillable ng EvalutionComment Model
        $evaluation->update([
            'scheduled_at' => Carbon::parse($request->scheduled_at),
            'status' => 'ongoing'
        ]);

        return redirect()->back()->with('success', 'Schedule has been set successfully!');
    }


    // Magdagdag ng route method sa controller
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,ongoing,resolved',
        ]);

        $evaluation = EvalutionComment::findOrFail($id);
        $evaluation->update([
            'status' => $request->status
        ]);

        return redirect()->back()->with('success', 'Status updated successfully!');
    }
}

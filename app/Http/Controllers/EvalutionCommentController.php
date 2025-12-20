<?php

namespace App\Http\Controllers;

use App\Models\EvalutionComment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EvalutionCommentController extends Controller
{
    //

    public function index()
    {
        $evaluations = EvalutionComment::with(['student', 'teacher'])->latest()->get();

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

<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\TeacherSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TeacherController extends Controller
{
    //

    // teacher dashboard
    public function index()
    {
        // Kukunin natin ang mga estudyante kasama ang kanilang pinakabagong counseling referral
        $students = Student::where('teacher_id', Auth::id())
            ->with(['evaluations' => function ($query) {
                // Kunin lang ang mga pending o ongoing at may schedule na
                $query->whereNotNull('scheduled_at')
                    ->where('status', '!=', 'resolved')
                    ->orderBy('scheduled_at', 'asc');
            }])
            ->get();

        $percentage = TeacherSetting::where('user_id', Auth::id())->first();



        return view('Teacher.Dashboard', compact('students', 'percentage'));
    }






    public function addStudentForm()
    {
        //
        return view('Teacher.AddStudent');
    }

    //  create teacgeher dashboard
    public function store(Request $request)
    {
        // validate the request
        $validatedData = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'section' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'password' => 'required|string|min:8',
        ]);


        // create new teacher

        User::create([
            'full_name' => $validatedData['fullname'],
            'username' => $validatedData['username'],
            'section' => $validatedData['section'],
            'subject' => $validatedData['subject'],
            'role' => 'teacher',
            'password' => bcrypt($validatedData['password']),
        ]);

        return redirect()->route('Dashboard.teacher')->with('success', 'Teacher account created successfully.');
    }



    //  CREATE STUDENT FUNCTION
    public function storeStudent(Request $request)
    {
        // validate the request
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
        ]);

        //  get the id of the currently authenticated teacher
        $teacherId = Auth::user()->id;
        // create new student
        Student::create([
            'full_name' => $validatedData['full_name'],
            'section' => $validatedData['section'],
            'subject' => $validatedData['subject'],
            'teacher_id' => $teacherId,
        ]);

        return redirect()->route('Dashboard.teacher')->with('success', 'Student added successfully.');
    }



    //  Delete Student Function
    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        $student->delete();
        return redirect()->route('Dashboard.teacher')->with('success', 'Student deleted successfully.');
    }
}

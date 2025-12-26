<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //

        $teacher_list = User::where('role', 'teacher')->get();
        $countedTeacher = $teacher_list->count();

        return view('Admin.Dashboard', [
            'teacher_list' => $teacher_list,
            'countedTeacher' => $countedTeacher,
        ]);
    }

    /**


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

    }

    /**
     * Show the form for editing the specified teacher.
     */
    public function edit(string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        return view('Admin.EditTeacher', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $teacher->id,
            'section' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $teacher->full_name = $data['full_name'];
        $teacher->username = $data['username'];
        $teacher->section = $data['section'] ?? $teacher->section;
        $teacher->subject = $data['subject'] ?? $teacher->subject;

        if (!empty($data['password'])) {
            $teacher->password = Hash::make($data['password']);
        }

        $teacher->save();

        return redirect()->route('Dashboard.admin')->with('success', 'Teacher updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = User::where('role', 'teacher')->findOrFail($id);
        $teacher->delete();
        return redirect()->route('Dashboard.admin')->with('success', 'Teacher deleted successfully.');
    }
}

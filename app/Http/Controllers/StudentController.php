<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('school');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name_english', 'like', "%{$keyword}%")
                  ->orWhere('name_kana', 'like', "%{$keyword}%")
                  ->orWhere('nationality', 'like', "%{$keyword}%");
            });
        }

        $students = $query->paginate(12);
        $schools = School::all();

        return view('students.index', compact('students', 'schools'));
    }

    public function create()
    {
        $schools = School::all();
        return view('students.create', compact('schools'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_english' => 'required|string|max:255',
            'name_kana' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'gender' => 'required|string',
            'age' => 'required|integer|min:1|max:150',
            'email' => 'required|email|max:255',
            'jlpt_level' => 'nullable|string|max:10',
            'school_id' => 'nullable|exists:schools,id',
            'student_number' => 'nullable|string|max:255',
            'home_country_education' => 'nullable|string',
            'referrer' => 'nullable|string|max:255',
            'oc_attendance' => 'boolean',
            'oc_reservation_date' => 'nullable|date',
            'online' => 'boolean',
            'enrollment_year' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $validated['oc_attendance'] = $request->has('oc_attendance');
        $validated['online'] = $request->has('online');

        Student::create($validated);

        return redirect()->route('dashboard')->with('success', '生徒が正常に登録されました。');
    }

    public function edit(Student $student)
    {
        $schools = School::all();
        return view('students.edit', compact('student', 'schools'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name_english' => 'required|string|max:255',
            'name_kana' => 'required|string|max:255',
            'nationality' => 'required|string|max:255',
            'gender' => 'required|string',
            'age' => 'required|integer|min:1|max:150',
            'email' => 'required|email|max:255',
            'jlpt_level' => 'nullable|string|max:10',
            'school_id' => 'nullable|exists:schools,id',
            'student_number' => 'nullable|string|max:255',
            'home_country_education' => 'nullable|string',
            'referrer' => 'nullable|string|max:255',
            'oc_attendance' => 'boolean',
            'oc_reservation_date' => 'nullable|date',
            'online' => 'boolean',
            'enrollment_year' => 'nullable|integer',
            'status' => 'nullable|string',
        ]);

        $validated['oc_attendance'] = $request->has('oc_attendance');
        $validated['online'] = $request->has('online');

        $student->update($validated);

        return redirect()->route('dashboard')->with('success', '生徒情報が正常に更新されました。');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('dashboard')->with('success', '生徒が正常に削除されました。');
    }
}


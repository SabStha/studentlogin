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
            'oc_reservation_date' => 'nullable',
            'oc_reservation_time' => 'nullable',
            'oc_reservation_ampm' => 'nullable|in:AM,PM',
            'online' => 'boolean',
            'enrollment_year' => 'nullable|integer',
            'status' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['oc_attendance'] = $request->has('oc_attendance');
        $validated['online'] = $request->has('online');

        // Handle date and time combination
        if ($request->filled('oc_reservation_date') && $request->filled('oc_reservation_time') && $request->oc_reservation_date !== 'null' && $request->oc_reservation_time !== 'null') {
            $date = $request->oc_reservation_date;
            $time = $request->oc_reservation_time; // Already converted to 24-hour format by JavaScript
            $validated['oc_reservation_date'] = $date . ' ' . $time . ':00';
        } elseif ($request->filled('oc_reservation_date') && $request->oc_reservation_date !== 'null') {
            $validated['oc_reservation_date'] = $request->oc_reservation_date;
        } else {
            $validated['oc_reservation_date'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Ensure directory exists
            $uploadPath = public_path('storage/students');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $image->move($uploadPath, $imageName);
            $validated['image'] = 'storage/students/' . $imageName;
        }

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
            'oc_reservation_date' => 'nullable',
            'oc_reservation_time' => 'nullable',
            'oc_reservation_ampm' => 'nullable|in:AM,PM',
            'online' => 'boolean',
            'enrollment_year' => 'nullable|integer',
            'status' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['oc_attendance'] = $request->has('oc_attendance');
        $validated['online'] = $request->has('online');

        // Handle date and time combination
        if ($request->filled('oc_reservation_date') && $request->filled('oc_reservation_time') && $request->oc_reservation_date !== 'null' && $request->oc_reservation_time !== 'null') {
            $date = $request->oc_reservation_date;
            $time = $request->oc_reservation_time; // Already converted to 24-hour format by JavaScript
            $validated['oc_reservation_date'] = $date . ' ' . $time . ':00';
        } elseif ($request->filled('oc_reservation_date') && $request->oc_reservation_date !== 'null') {
            $validated['oc_reservation_date'] = $request->oc_reservation_date;
        } else {
            $validated['oc_reservation_date'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($student->image && file_exists(public_path($student->image))) {
                @unlink(public_path($student->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            
            // Ensure directory exists
            $uploadPath = public_path('storage/students');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            $image->move($uploadPath, $imageName);
            $validated['image'] = 'storage/students/' . $imageName;
        }

        $student->update($validated);

        return redirect()->route('dashboard')->with('success', '生徒情報が正常に更新されました。');
    }

    public function destroy(Student $student)
    {
        // Delete image if exists
        if ($student->image && file_exists(public_path($student->image))) {
            unlink(public_path($student->image));
        }
        
        $student->delete();
        return redirect()->route('dashboard')->with('success', '生徒が正常に削除されました。');
    }
}


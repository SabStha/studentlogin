<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('school');

        // Keyword search
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('name_english', 'like', "%{$keyword}%")
                  ->orWhere('name_kana', 'like', "%{$keyword}%")
                  ->orWhere('nationality', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // Enrollment year filter
        if ($request->filled('enrollment_year')) {
            $query->where('enrollment_year', $request->enrollment_year);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(12);

        // Statistics
        $appliedCount = Student::where('applied', true)->count();
        $participatedCount = Student::where('participated', true)->count();

        // Enrollment years for dropdown
        $enrollmentYears = Student::select('enrollment_year')
            ->distinct()
            ->whereNotNull('enrollment_year')
            ->orderBy('enrollment_year', 'desc')
            ->pluck('enrollment_year');
        
        // Always include 2026 if it doesn't exist
        if (!$enrollmentYears->contains(2026)) {
            $enrollmentYears = $enrollmentYears->push(2026)->sortDesc()->values();
        }

        return view('dashboard', compact('students', 'appliedCount', 'participatedCount', 'enrollmentYears'));
    }
}


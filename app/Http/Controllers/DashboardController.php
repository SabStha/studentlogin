<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        // Name search
        if ($request->filled('name')) {
            $query->where(function($q) use ($request) {
                $q->where('name_english', 'like', "%{$request->name}%")
                  ->orWhere('name_kana', 'like', "%{$request->name}%");
            });
        }

        // Enrollment year filter
        if ($request->filled('enrollment_year')) {
            $query->where('enrollment_year', $request->enrollment_year);
        }

        // Status filter (basic)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Detailed status filters (checkboxes)
        $statusFilters = [];
        if ($request->has('status_2year')) {
            $statusFilters[] = '2年合格';
        }
        if ($request->has('status_1year')) {
            $statusFilters[] = '1年合格';
        }
        if ($request->has('status_fail')) {
            $statusFilters[] = '不合格';
        }
        if ($request->has('status_waiting')) {
            $statusFilters[] = '試験待ち';
        }
        if (!empty($statusFilters)) {
            $query->whereIn('status', $statusFilters);
        }

        // Nationality filter
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        // Gender filter
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // JLPT level filter
        if ($request->filled('jlpt_level')) {
            $query->where('jlpt_level', $request->jlpt_level);
        }

        // School filter
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // OC Reservation Date filter
        if ($request->filled('oc_reservation_date')) {
            $query->whereDate('oc_reservation_date', $request->oc_reservation_date);
        }

        // Referrer filter
        if ($request->filled('referrer')) {
            $query->where('referrer', $request->referrer);
        }

        // Student number filter
        if ($request->filled('student_number')) {
            $query->where('student_number', 'like', "%{$request->student_number}%");
        }

        // Japanese evaluation filter (if field exists)
        if ($request->filled('japanese_evaluation')) {
            $query->where('japanese_evaluation', 'like', "%{$request->japanese_evaluation}%");
        }

        // Total score filter (if field exists)
        if ($request->filled('total_score')) {
            $query->where('total_score', $request->total_score);
        }

        // Nationality empty filter
        if ($request->has('nationality_empty')) {
            $query->where(function($q) {
                $q->whereNull('nationality')->orWhere('nationality', '');
            });
        }

        // School empty filter
        if ($request->has('school_empty')) {
            $query->whereNull('school_id');
        }

        // Absent only filter (assuming this means oc_attendance = false)
        if ($request->has('absent_only')) {
            $query->where('oc_attendance', false);
        }

        // Applicants only filter
        if ($request->has('applicants_only')) {
            $query->where('applied', true);
        }

        // Sort by name
        if ($request->has('sort_by_name')) {
            $query->orderBy('name_english', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $students = $query->paginate(12)->appends($request->query());

        // Statistics
        $appliedCount = Student::where('applied', true)->count();
        $participatedCount = Student::where('participated', true)->count();

        // Summary by Nationality
        $summaryByNationality = Student::select('nationality')
            ->selectRaw('COUNT(*) as applied_count')
            ->selectRaw('SUM(CASE WHEN participated = 1 THEN 1 ELSE 0 END) as participated_count')
            ->where('applied', true)
            ->groupBy('nationality')
            ->orderBy('nationality', 'asc')
            ->get();

        // Summary by School
        $summaryBySchool = Student::select('schools.name as school_name')
            ->selectRaw('COUNT(*) as applied_count')
            ->selectRaw('SUM(CASE WHEN students.participated = 1 THEN 1 ELSE 0 END) as participated_count')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->where('students.applied', true)
            ->groupBy('schools.name')
            ->orderBy('schools.name', 'asc')
            ->get();

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

        // Get unique values for dropdowns
        $nationalities = Student::select('nationality')
            ->distinct()
            ->whereNotNull('nationality')
            ->where('nationality', '!=', '')
            ->orderBy('nationality', 'asc')
            ->pluck('nationality');

        $referrers = Student::select('referrer')
            ->distinct()
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->orderBy('referrer', 'asc')
            ->pluck('referrer');

        $schools = School::orderBy('name', 'asc')->get();

        return view('dashboard', compact('students', 'appliedCount', 'participatedCount', 'enrollmentYears', 'nationalities', 'referrers', 'schools', 'summaryByNationality', 'summaryBySchool'));
    }

    public function downloadSummary()
    {
        // Get current enrollment year (default to 2026)
        $enrollmentYear = Student::select('enrollment_year')
            ->distinct()
            ->whereNotNull('enrollment_year')
            ->orderBy('enrollment_year', 'desc')
            ->first();
        
        $enrollmentYear = $enrollmentYear ? $enrollmentYear->enrollment_year : 2026;
        $nextYear = $enrollmentYear + 1;
        $currentDate = date('Y年n月j日', strtotime('now'));

        // Summary by Nationality
        $summaryByNationality = Student::select('nationality')
            ->selectRaw('COUNT(*) as applied_count')
            ->selectRaw('SUM(CASE WHEN participated = 1 THEN 1 ELSE 0 END) as participated_count')
            ->where('applied', true)
            ->groupBy('nationality')
            ->orderBy('nationality', 'asc')
            ->get();

        // Summary by School
        $summaryBySchool = Student::select('schools.name as school_name')
            ->selectRaw('COUNT(*) as applied_count')
            ->selectRaw('SUM(CASE WHEN students.participated = 1 THEN 1 ELSE 0 END) as participated_count')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->where('students.applied', true)
            ->groupBy('schools.name')
            ->orderBy('schools.name', 'asc')
            ->get();

        // Calculate totals
        $totalApplied = $summaryByNationality->sum('applied_count');
        $totalParticipated = $summaryByNationality->sum('participated_count');

        // Create new Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setCellValue('A1', 'オープンキャンパスサマリー');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Set subtitle for nationality
        $sheet->setCellValue('A2', '国籍別');
        $sheet->getStyle('A2')->getFont()->setBold(true);

        // Set date
        $sheet->setCellValue('A3', $nextYear . '年度入学 (' . $currentDate . '時点)');

        // Set headers for nationality table
        $sheet->setCellValue('A4', '国籍');
        $sheet->setCellValue('B4', '申込人数');
        $sheet->setCellValue('C4', '参加人数');
        
        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A4:C4')->applyFromArray($headerStyle);

        // Add nationality data
        $row = 5;
        foreach ($summaryByNationality as $item) {
            $sheet->setCellValue('A' . $row, $item->nationality);
            $sheet->setCellValue('B' . $row, $item->applied_count);
            $sheet->setCellValue('C' . $row, $item->participated_count);
            
            // Add borders
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            $row++;
        }

        // Add total row
        $sheet->setCellValue('A' . $row, '合計');
        $sheet->setCellValue('B' . $row, $totalApplied);
        $sheet->setCellValue('C' . $row, $totalParticipated);
        
        // Style total row
        $totalStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F0F0F0']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalStyle);

        // Add spacing
        $row += 2;

        // Set subtitle for school
        $sheet->setCellValue('A' . $row, '学校別');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        $row++;

        // Set headers for school table
        $sheet->setCellValue('A' . $row, '学校');
        $sheet->setCellValue('B' . $row, '申込人数');
        $sheet->setCellValue('C' . $row, '参加人数');
        
        // Style headers
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($headerStyle);
        $row++;

        // Add school data
        foreach ($summaryBySchool as $item) {
            $sheet->setCellValue('A' . $row, $item->school_name ?? '学校未設定');
            $sheet->setCellValue('B' . $row, $item->applied_count);
            $sheet->setCellValue('C' . $row, $item->participated_count);
            
            // Add borders
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ]);
            $row++;
        }

        // Add total row for school
        $totalAppliedSchool = $summaryBySchool->sum('applied_count');
        $totalParticipatedSchool = $summaryBySchool->sum('participated_count');
        $sheet->setCellValue('A' . $row, '合計');
        $sheet->setCellValue('B' . $row, $totalAppliedSchool);
        $sheet->setCellValue('C' . $row, $totalParticipatedSchool);
        $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalStyle);

        // Auto-size columns
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);

        // Set response headers
        $filename = 'オープンキャンパスサマリー_' . date('Ymd') . '.xlsx';
        
        return new StreamedResponse(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            200,
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'max-age=0',
            ]
        );
    }
}

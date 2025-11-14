<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;

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
        $totalAppliedSchool = $summaryBySchool->sum('applied_count');
        $totalParticipatedSchool = $summaryBySchool->sum('participated_count');

        // Get additional data for other sheets
        // Status sheet data (学校の片思い, 学生の片思い, 両想い)
        $statusData = Student::select('schools.name as school_name')
            ->selectRaw('SUM(CASE WHEN students.applied = 1 AND students.participated = 0 THEN 1 ELSE 0 END) as school_unrequited')
            ->selectRaw('SUM(CASE WHEN students.applied = 0 AND students.participated = 1 THEN 1 ELSE 0 END) as student_unrequited')
            ->selectRaw('SUM(CASE WHEN students.applied = 1 AND students.participated = 1 THEN 1 ELSE 0 END) as mutual')
            ->leftJoin('schools', 'students.school_id', '=', 'schools.id')
            ->groupBy('schools.name')
            ->orderBy('schools.name', 'asc')
            ->get();

        // Successful applicants (合格者)
        $successfulApplicants = Student::whereIn('status', ['1年合格', '2年合格'])
            ->select('student_number', 'name_english')
            ->orderBy('name_english', 'asc')
            ->get();

        // Unsuccessful applicants (不合格者)
        $unsuccessfulApplicants = Student::where('status', '不合格')
            ->select('student_number', 'name_english')
            ->orderBy('name_english', 'asc')
            ->get();

        // Check if PhpSpreadsheet is available
        if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            // Use Excel export with multiple sheets
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            
            // Sheet 1: 国籍別 (By Nationality)
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('国籍別');

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
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
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
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
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
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F0F0F0']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            $sheet->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalStyle);

            // Auto-size columns
            $sheet->getColumnDimension('A')->setAutoSize(true);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);

            // Sheet 2: 学校別 (By School)
            $sheet2 = $spreadsheet->createSheet();
            $sheet2->setTitle('学校別');
            
            // Set title
            $sheet2->setCellValue('A1', 'オープンキャンパスサマリー');
            $sheet2->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            // Set subtitle
            $sheet2->setCellValue('A2', '学校別');
            $sheet2->getStyle('A2')->getFont()->setBold(true);
            
            // Set date
            $sheet2->setCellValue('A3', $nextYear . '年度入学 (' . $currentDate . '時点)');
            
            // Set headers
            $sheet2->setCellValue('A4', '学校名');
            $sheet2->setCellValue('B4', '申込人数');
            $sheet2->setCellValue('C4', '参加人数');
            $sheet2->getStyle('A4:C4')->applyFromArray($headerStyle);
            
            // Add school data
            $row = 5;
            foreach ($summaryBySchool as $item) {
                $sheet2->setCellValue('A' . $row, $item->school_name ?? '学校未設定');
                $sheet2->setCellValue('B' . $row, $item->applied_count);
                $sheet2->setCellValue('C' . $row, $item->participated_count);
                $sheet2->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $row++;
            }
            
            // Add total row
            $sheet2->setCellValue('A' . $row, '合計');
            $sheet2->setCellValue('B' . $row, $totalAppliedSchool);
            $sheet2->setCellValue('C' . $row, $totalParticipatedSchool);
            $sheet2->getStyle('A' . $row . ':C' . $row)->applyFromArray($totalStyle);
            $sheet2->getColumnDimension('A')->setAutoSize(true);
            $sheet2->getColumnDimension('B')->setAutoSize(true);
            $sheet2->getColumnDimension('C')->setAutoSize(true);

            // Sheet 3: 状況 (Status)
            $sheet3 = $spreadsheet->createSheet();
            $sheet3->setTitle('状況');
            
            // Set title
            $sheet3->setCellValue('A1', 'オープンキャンパスサマリー');
            $sheet3->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            // Set subtitle
            $sheet3->setCellValue('A2', '状況');
            $sheet3->getStyle('A2')->getFont()->setBold(true);
            
            // Set date
            $sheet3->setCellValue('A3', $nextYear . '年度入学(' . $currentDate . '時点)');
            
            // Set headers
            $sheet3->setCellValue('A4', '学校名');
            $sheet3->setCellValue('B4', '学校の片思い');
            $sheet3->setCellValue('C4', '学生の片思い');
            $sheet3->setCellValue('D4', '両想い');
            $sheet3->getStyle('A4:D4')->applyFromArray($headerStyle);
            
            // Add status data
            $row = 5;
            foreach ($statusData as $item) {
                $sheet3->setCellValue('A' . $row, $item->school_name ?? '学校未設定');
                $sheet3->setCellValue('B' . $row, $item->school_unrequited);
                $sheet3->setCellValue('C' . $row, $item->student_unrequited);
                $sheet3->setCellValue('D' . $row, $item->mutual);
                $sheet3->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $row++;
            }
            
            // Add total row
            $totalSchoolUnrequited = $statusData->sum('school_unrequited');
            $totalStudentUnrequited = $statusData->sum('student_unrequited');
            $totalMutual = $statusData->sum('mutual');
            $sheet3->setCellValue('A' . $row, '合計');
            $sheet3->setCellValue('B' . $row, $totalSchoolUnrequited);
            $sheet3->setCellValue('C' . $row, $totalStudentUnrequited);
            $sheet3->setCellValue('D' . $row, $totalMutual);
            $sheet3->getStyle('A' . $row . ':D' . $row)->applyFromArray($totalStyle);
            $sheet3->getColumnDimension('A')->setAutoSize(true);
            $sheet3->getColumnDimension('B')->setAutoSize(true);
            $sheet3->getColumnDimension('C')->setAutoSize(true);
            $sheet3->getColumnDimension('D')->setAutoSize(true);

            // Sheet 4: 合格者 (Successful Applicants)
            $sheet4 = $spreadsheet->createSheet();
            $sheet4->setTitle('合格者');
            
            // Set title
            $sheet4->setCellValue('A1', '入試サマリー');
            $sheet4->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            // Set subtitle
            $sheet4->setCellValue('A2', '合格者一覧');
            $sheet4->getStyle('A2')->getFont()->setBold(true)->setSize(14);
            
            // Set date
            $sheet4->setCellValue('A3', $nextYear . '年度入学(' . $currentDate . '時点)');
            
            // Set headers
            $sheet4->setCellValue('A4', '受験番号');
            $sheet4->setCellValue('B4', '名前');
            $sheet4->setCellValue('C4', '総合点数');
            $sheet4->getStyle('A4:C4')->applyFromArray($headerStyle);
            
            // Add successful applicants data
            $row = 5;
            foreach ($successfulApplicants as $student) {
                $sheet4->setCellValue('A' . $row, $student->student_number ?? '');
                $sheet4->setCellValue('B' . $row, $student->name_english);
                $sheet4->setCellValue('C' . $row, ''); // total_score field doesn't exist yet
                $sheet4->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $row++;
            }
            $sheet4->getColumnDimension('A')->setAutoSize(true);
            $sheet4->getColumnDimension('B')->setAutoSize(true);
            $sheet4->getColumnDimension('C')->setAutoSize(true);

            // Sheet 5: 不合格者 (Unsuccessful Applicants)
            $sheet5 = $spreadsheet->createSheet();
            $sheet5->setTitle('不合格者');
            
            // Set title
            $sheet5->setCellValue('A1', '入試サマリー');
            $sheet5->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            
            // Set subtitle
            $sheet5->setCellValue('A2', '不合格者一覧');
            $sheet5->getStyle('A2')->getFont()->setBold(true)->setSize(14);
            
            // Set date
            $sheet5->setCellValue('A3', $nextYear . '年度入学(' . $currentDate . '時点)');
            
            // Set headers
            $sheet5->setCellValue('A4', '受験番号');
            $sheet5->setCellValue('B4', '名前');
            $sheet5->setCellValue('C4', '総合点数');
            $sheet5->getStyle('A4:C4')->applyFromArray($headerStyle);
            
            // Add unsuccessful applicants data
            $row = 5;
            foreach ($unsuccessfulApplicants as $student) {
                $sheet5->setCellValue('A' . $row, $student->student_number ?? '');
                $sheet5->setCellValue('B' . $row, $student->name_english);
                $sheet5->setCellValue('C' . $row, ''); // total_score field doesn't exist yet
                $sheet5->getStyle('A' . $row . ':C' . $row)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
                $row++;
            }
            $sheet5->getColumnDimension('A')->setAutoSize(true);
            $sheet5->getColumnDimension('B')->setAutoSize(true);
            $sheet5->getColumnDimension('C')->setAutoSize(true);

            // Set first sheet as active
            $spreadsheet->setActiveSheetIndex(0);

            // Set response headers
            $filename = 'オープンキャンパスサマリー_' . date('Ymd') . '.xlsx';
            
            return new StreamedResponse(
                function () use ($spreadsheet) {
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );
        } else {
            // Fallback to CSV export (no external dependencies)
            // Note: CSV doesn't support multiple sheets, so we'll create a combined CSV
            $filename = 'オープンキャンパスサマリー_' . date('Ymd') . '.csv';
            
            return new StreamedResponse(
                function () use ($summaryByNationality, $summaryBySchool, $statusData, $successfulApplicants, $unsuccessfulApplicants, $totalApplied, $totalParticipated, $totalAppliedSchool, $totalParticipatedSchool, $nextYear, $currentDate) {
                    $handle = fopen('php://output', 'w');
                    
                    // Add BOM for Excel UTF-8 support
                    fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                    
                    // Sheet 1: 国籍別
                    fputcsv($handle, ['オープンキャンパスサマリー'], ',');
                    fputcsv($handle, ['国籍別'], ',');
                    fputcsv($handle, [$nextYear . '年度入学 (' . $currentDate . '時点)'], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, ['国籍', '申込人数', '参加人数'], ',');
                    
                    foreach ($summaryByNationality as $item) {
                        fputcsv($handle, [$item->nationality, $item->applied_count, $item->participated_count], ',');
                    }
                    
                    fputcsv($handle, ['合計', $totalApplied, $totalParticipated], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, [], ',');
                    
                    // Sheet 2: 学校別
                    fputcsv($handle, ['オープンキャンパスサマリー'], ',');
                    fputcsv($handle, ['学校別'], ',');
                    fputcsv($handle, [$nextYear . '年度入学 (' . $currentDate . '時点)'], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, ['学校名', '申込人数', '参加人数'], ',');
                    
                    foreach ($summaryBySchool as $item) {
                        fputcsv($handle, [$item->school_name ?? '学校未設定', $item->applied_count, $item->participated_count], ',');
                    }
                    
                    fputcsv($handle, ['合計', $totalAppliedSchool, $totalParticipatedSchool], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, [], ',');
                    
                    // Sheet 3: 状況
                    fputcsv($handle, ['オープンキャンパスサマリー'], ',');
                    fputcsv($handle, ['状況'], ',');
                    fputcsv($handle, [$nextYear . '年度入学(' . $currentDate . '時点)'], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, ['学校名', '学校の片思い', '学生の片思い', '両想い'], ',');
                    
                    foreach ($statusData as $item) {
                        fputcsv($handle, [$item->school_name ?? '学校未設定', $item->school_unrequited, $item->student_unrequited, $item->mutual], ',');
                    }
                    
                    $totalSchoolUnrequited = $statusData->sum('school_unrequited');
                    $totalStudentUnrequited = $statusData->sum('student_unrequited');
                    $totalMutual = $statusData->sum('mutual');
                    fputcsv($handle, ['合計', $totalSchoolUnrequited, $totalStudentUnrequited, $totalMutual], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, [], ',');
                    
                    // Sheet 4: 合格者
                    fputcsv($handle, ['入試サマリー'], ',');
                    fputcsv($handle, ['合格者一覧'], ',');
                    fputcsv($handle, [$nextYear . '年度入学(' . $currentDate . '時点)'], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, ['受験番号', '名前', '総合点数'], ',');
                    
                    foreach ($successfulApplicants as $student) {
                        fputcsv($handle, [$student->student_number ?? '', $student->name_english, ''], ',');
                    }
                    fputcsv($handle, [], ',');
                    fputcsv($handle, [], ',');
                    
                    // Sheet 5: 不合格者
                    fputcsv($handle, ['入試サマリー'], ',');
                    fputcsv($handle, ['不合格者一覧'], ',');
                    fputcsv($handle, [$nextYear . '年度入学(' . $currentDate . '時点)'], ',');
                    fputcsv($handle, [], ',');
                    fputcsv($handle, ['受験番号', '名前', '総合点数'], ',');
                    
                    foreach ($unsuccessfulApplicants as $student) {
                        fputcsv($handle, [$student->student_number ?? '', $student->name_english, ''], ',');
                    }
                    
                    fclose($handle);
                },
                200,
                [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );
        }
    }
}

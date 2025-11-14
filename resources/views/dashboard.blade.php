@extends('layouts.app')

@section('title', 'ダッシュボード - AIWA FARM')

@section('content')
<h1 style="font-size: 28px; margin-bottom: 20px;">生徒検索</h1>

<!-- Search Form (Always Visible) -->
<div class="search-form" style="background-color: #fff; padding: 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ddd;">
    <form method="GET" action="{{ route('dashboard') }}" id="search-form">
        <!-- Basic Search Fields (Always Visible) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div class="form-group">
                <label for="keyword">キーワード</label>
                <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="キーワード" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
            </div>

            <div class="form-group">
                <label for="enrollment_year">入学年度</label>
                <select name="enrollment_year" id="enrollment_year" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">選択してください</option>
                    @foreach($enrollmentYears as $year)
                        <option value="{{ $year }}" {{ request('enrollment_year') == $year ? 'selected' : '' }}>{{ $year }}年</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">ステータス</label>
                <select name="status" id="status" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                    <option value="">すべて</option>
                    <option value="2年合格" {{ request('status') == '2年合格' ? 'selected' : '' }}>2年合格</option>
                    <option value="1年合格" {{ request('status') == '1年合格' ? 'selected' : '' }}>1年合格</option>
                    <option value="不合格" {{ request('status') == '不合格' ? 'selected' : '' }}>不合格</option>
                    <option value="試験待ち" {{ request('status') == '試験待ち' ? 'selected' : '' }}>試験待ち</option>
                </select>
            </div>
        </div>

        <!-- Detailed Search Header (Clickable) -->
        <div style="text-align: center; margin-bottom: 15px;">
            <h2 id="toggle-detailed-search" style="color: #0066cc; text-decoration: underline; cursor: pointer; font-size: 18px; margin: 0; display: inline-block;">詳細検索</h2>
        </div>

        <!-- Detailed Search Section (Initially Hidden) -->
        <div id="detailed-search" style="display: none; margin-bottom: 20px;">
            <!-- Checkbox Filters -->
            <div style="margin-bottom: 20px;">
                <div style="display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="sort_by_name" value="1" {{ request('sort_by_name') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>名前で並び替え</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="nationality_empty" value="1" {{ request('nationality_empty') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>国籍未入力のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="school_empty" value="1" {{ request('school_empty') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>学校未入力のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="absent_only" value="1" {{ request('absent_only') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>欠席のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="applicants_only" value="1" {{ request('applicants_only') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>出願者のみ</span>
                    </label>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 15px;">
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="status_2year" value="1" {{ request('status_2year') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>2年合格者のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="status_1year" value="1" {{ request('status_1year') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>1年合格者のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="status_fail" value="1" {{ request('status_fail') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>不合格者のみ</span>
                    </label>
                    <label style="display: flex; align-items: center; cursor: pointer;">
                        <input type="checkbox" name="status_waiting" value="1" {{ request('status_waiting') ? 'checked' : '' }} style="margin-right: 5px;">
                        <span>試験待ちのみ</span>
                    </label>
                </div>
            </div>

            <!-- Input Fields -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div class="form-group">
                    <label for="oc_reservation_date">OC予約日時</label>
                    <input type="date" name="oc_reservation_date" id="oc_reservation_date" value="{{ request('oc_reservation_date') }}" placeholder="dd/mm/yyyy" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="name">名前</label>
                    <input type="text" name="name" id="name" value="{{ request('name') }}" placeholder="名前" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="nationality">国籍</label>
                    <select name="nationality" id="nationality" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                        @foreach($nationalities as $nat)
                            <option value="{{ $nat }}" {{ request('nationality') == $nat ? 'selected' : '' }}>{{ $nat }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="gender">性別</label>
                    <select name="gender" id="gender" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                        <option value="男" {{ request('gender') == '男' ? 'selected' : '' }}>男</option>
                        <option value="女" {{ request('gender') == '女' ? 'selected' : '' }}>女</option>
                        <option value="その他" {{ request('gender') == 'その他' ? 'selected' : '' }}>その他</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jlpt_level">JLPT</label>
                    <select name="jlpt_level" id="jlpt_level" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                        <option value="N1" {{ request('jlpt_level') == 'N1' ? 'selected' : '' }}>N1</option>
                        <option value="N2" {{ request('jlpt_level') == 'N2' ? 'selected' : '' }}>N2</option>
                        <option value="N3" {{ request('jlpt_level') == 'N3' ? 'selected' : '' }}>N3</option>
                        <option value="N4" {{ request('jlpt_level') == 'N4' ? 'selected' : '' }}>N4</option>
                        <option value="N5" {{ request('jlpt_level') == 'N5' ? 'selected' : '' }}>N5</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="school_id">今の学校</label>
                    <select name="school_id" id="school_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="japanese_evaluation">日本語・人物評価</label>
                    <select name="japanese_evaluation" id="japanese_evaluation" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="total_score">総合点数</label>
                    <input type="number" name="total_score" id="total_score" value="{{ request('total_score') }}" placeholder="総合点数" min="0" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>

                <div class="form-group">
                    <label for="referrer">紹介者</label>
                    <select name="referrer" id="referrer" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">選択してください</option>
                        @foreach($referrers as $ref)
                            <option value="{{ $ref }}" {{ request('referrer') == $ref ? 'selected' : '' }}>{{ $ref }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="student_number">受験番号 B-</label>
                    <input type="text" name="student_number" id="student_number" value="{{ request('student_number') }}" placeholder="受験番号" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                </div>
            </div>

            <!-- Search Clear Link -->
            <div style="text-align: right; margin-top: 15px;">
                <a href="{{ route('dashboard') }}" style="color: #0066cc; text-decoration: underline; cursor: pointer;">検索クリア</a>
            </div>
        </div>

        <!-- Main Search Button -->
        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" style="background-color: #0066cc; color: white; border: none; padding: 12px 40px; border-radius: 4px; cursor: pointer; font-size: 16px;">検索</button>
        </div>
    </form>
</div>

<!-- Summary Section Header (Clickable) -->
<div style="text-align: center; margin-bottom: 15px;">
    <h2 id="toggle-summary" style="color: #0066cc; text-decoration: underline; cursor: pointer; font-size: 18px; margin: 0; display: inline-block;">サマリー</h2>
</div>

<!-- Summary Section (Initially Hidden) -->
<div id="summary-section" style="display: none; background-color: #fff; padding: 20px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #ddd;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 24px; margin: 0;">サマリー</h2>
        <button type="button" id="download-summary" class="btn" style="background-color: #ff69b4; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">サマリーダウンロード</button>
    </div>

    <!-- Summary by Nationality -->
    <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; margin-bottom: 10px;">国籍別</h3>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">国籍</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">申込</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">参加</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaryByNationality as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->nationality }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item->applied_count }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item->participated_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: center; color: #999;">データがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Summary by School -->
    <div>
        <h3 style="font-size: 18px; margin-bottom: 10px;">学校別</h3>
        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: left;">学校</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">申込</th>
                    <th style="padding: 10px; border: 1px solid #ddd; text-align: center;">参加</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summaryBySchool as $item)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $item->school_name ?? '学校未設定' }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item->applied_count }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $item->participated_count }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: center; color: #999;">データがありません</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Status Legend and Statistics -->
<div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
    <div class="legend" style="display: flex; gap: 20px; flex-wrap: wrap;">
        <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
            <span class="status-badge status-2year" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: #0066cc; color: white; text-align: center; line-height: 20px; font-size: 12px;">✓</span>
            <span>2年合格</span>
        </div>
        <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
            <span class="status-badge status-1year" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: #28a745; color: white; text-align: center; line-height: 20px; font-size: 12px;">✓</span>
            <span>1年合格</span>
        </div>
        <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
            <span class="status-badge status-fail" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: #dc3545; color: white; text-align: center; line-height: 20px; font-size: 12px;">✗</span>
            <span>不合格</span>
        </div>
        <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
            <span class="status-badge status-waiting" style="display: inline-block; width: 20px; height: 20px; border-radius: 50%; background-color: #ffc107; color: white; text-align: center; line-height: 20px; font-size: 12px;">✗</span>
            <span>試験待ち</span>
        </div>
    </div>

    <div class="statistics">
        <div class="stat-item" style="font-size: 14px;">申込：{{ $appliedCount }}人 参加：{{ $participatedCount }}人</div>
    </div>
</div>

<!-- Student Cards Grid -->
<div class="grid">
    @forelse($students as $student)
        <div class="student-card">
            <div style="width: 100%; height: 200px; border-radius: 4px; margin-bottom: 10px; overflow: hidden; background-color: #e0e0e0; display: flex; align-items: center; justify-content: center;">
                @if($student->image)
                    <img src="{{ asset($student->image) }}" alt="{{ $student->name_english }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="color: #999;">画像</span>
                @endif
            </div>
            <div class="student-info">
                <div style="font-weight: bold; font-size: 16px; margin-bottom: 8px;">
                    {{ $student->name_english }} ({{ $student->age }})
                </div>
                <div style="margin-bottom: 5px;">{{ $student->nationality }}</div>
                <div style="margin-bottom: 5px;">JLPT: {{ $student->jlpt_level ?? 'N/A' }}</div>
                <div style="margin-bottom: 5px;">{{ $student->school->name ?? '学校未設定' }}</div>
                <div style="margin-top: 10px;">
                    <span class="status-badge status-{{ $student->status == '2年合格' ? '2year' : ($student->status == '1年合格' ? '1year' : ($student->status == '不合格' ? 'fail' : 'waiting')) }}">
                        {{ $student->status }}
                    </span>
                </div>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
            生徒が見つかりませんでした。
        </div>
    @endforelse
</div>

<div style="margin-top: 30px;">
    {{ $students->links() }}
</div>

<script>
// Toggle detailed search
document.getElementById('toggle-detailed-search').addEventListener('click', function() {
    const detailedSearch = document.getElementById('detailed-search');
    if (detailedSearch.style.display === 'none') {
        detailedSearch.style.display = 'block';
    } else {
        detailedSearch.style.display = 'none';
    }
});

// Toggle summary section
document.getElementById('toggle-summary').addEventListener('click', function() {
    const summarySection = document.getElementById('summary-section');
    if (summarySection.style.display === 'none') {
        summarySection.style.display = 'block';
    } else {
        summarySection.style.display = 'none';
    }
});

// Show detailed search if any detailed search field has a value
@if(request()->hasAny(['oc_reservation_date', 'name', 'nationality', 'gender', 'jlpt_level', 'school_id', 'japanese_evaluation', 'total_score', 'referrer', 'student_number', 'sort_by_name', 'nationality_empty', 'school_empty', 'absent_only', 'applicants_only', 'status_2year', 'status_1year', 'status_fail', 'status_waiting']))
    document.getElementById('detailed-search').style.display = 'block';
@endif

// Summary download
document.getElementById('download-summary').addEventListener('click', function() {
    window.location.href = '{{ route("dashboard.download-summary") }}';
});
</script>
@endsection

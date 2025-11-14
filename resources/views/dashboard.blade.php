@extends('layouts.app')

@section('title', 'ダッシュボード - AIWA FARM')

@section('content')
<h1 style="font-size: 28px; margin-bottom: 20px;">生徒検索</h1>

<div class="search-form">
    <form method="GET" action="{{ route('dashboard') }}">
        <div class="form-group">
            <label for="keyword">キーワード</label>
            <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="キーワード">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="enrollment_year">入学年度</label>
                <select name="enrollment_year" id="enrollment_year">
                    <option value="">選択してください</option>
                    @foreach($enrollmentYears as $year)
                        <option value="{{ $year }}" {{ request('enrollment_year') == $year ? 'selected' : '' }}>{{ $year }}年</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="status">ステータス</label>
                <select name="status" id="status">
                    <option value="">すべて</option>
                    <option value="2年合格" {{ request('status') == '2年合格' ? 'selected' : '' }}>2年合格</option>
                    <option value="1年合格" {{ request('status') == '1年合格' ? 'selected' : '' }}>1年合格</option>
                    <option value="不合格" {{ request('status') == '不合格' ? 'selected' : '' }}>不合格</option>
                    <option value="試験待ち" {{ request('status') == '試験待ち' ? 'selected' : '' }}>試験待ち</option>
                </select>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary" style="width: 100%;">検索</button>
            </div>
        </div>
    </form>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
    <div class="legend">
        <div class="legend-item">
            <span class="status-badge status-2year">✓</span>
            <span>2年合格</span>
        </div>
        <div class="legend-item">
            <span class="status-badge status-1year">✓</span>
            <span>1年合格</span>
        </div>
        <div class="legend-item">
            <span class="status-badge status-fail">✗</span>
            <span>不合格</span>
        </div>
        <div class="legend-item">
            <span class="status-badge status-waiting">✗</span>
            <span>試験待ち</span>
        </div>
    </div>

    <div class="statistics">
        <div class="stat-item">申込: {{ $appliedCount }}人 参加: {{ $participatedCount }}人</div>
    </div>
</div>

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
@endsection


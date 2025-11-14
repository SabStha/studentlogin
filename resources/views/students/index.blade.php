@extends('layouts.app')

@section('title', '生徒検索 - AIWA FARM')

@section('content')
<h1 style="font-size: 28px; margin-bottom: 20px;">生徒検索</h1>

<div class="search-form">
    <form method="GET" action="{{ route('students.index') }}">
        <div class="form-group">
            <label for="keyword">キーワード</label>
            <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}" placeholder="キーワード">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">検索</button>
        </div>
    </form>
</div>

<div class="grid">
    @forelse($students as $student)
        <div class="student-card">
            <div style="width: 100%; height: 200px; background-color: #e0e0e0; border-radius: 4px; margin-bottom: 10px; display: flex; align-items: center; justify-content: center; color: #999;">
                画像
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
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <a href="{{ route('students.edit', $student) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">編集</a>
                <form method="POST" action="{{ route('students.destroy', $student) }}" style="display: inline;" onsubmit="return confirm('削除してもよろしいですか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">削除</button>
                </form>
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


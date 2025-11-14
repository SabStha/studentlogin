@extends('layouts.app')

@section('title', '学校検索 - AIWA FARM')

@section('content')
<h1 style="font-size: 28px; margin-bottom: 20px;">学校検索</h1>

<div class="search-form">
    <form method="GET" action="{{ route('schools.index') }}">
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
    @forelse($schools as $school)
        <div class="student-card">
            <div style="font-weight: bold; font-size: 18px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #ddd;">
                {{ $school->name }}
            </div>
            <div class="student-info">
                @if($school->contact_person)
                    <div style="margin-bottom: 5px;">{{ $school->contact_person }}</div>
                @endif
                @if($school->position)
                    <div style="margin-bottom: 5px;">{{ $school->position }}</div>
                @endif
                @if($school->phone)
                    <div style="margin-bottom: 5px;">{{ $school->phone }}</div>
                @endif
                @if($school->address)
                    <div style="margin-bottom: 5px; font-size: 12px; color: #666;">{{ $school->address }}</div>
                @endif
            </div>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <a href="{{ route('schools.edit', $school) }}" class="btn btn-secondary" style="padding: 5px 10px; font-size: 12px;">編集</a>
                <form method="POST" action="{{ route('schools.destroy', $school) }}" style="display: inline;" onsubmit="return confirm('削除してもよろしいですか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding: 5px 10px; font-size: 12px;">削除</button>
                </form>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
            学校が見つかりませんでした。
        </div>
    @endforelse
</div>

<div style="margin-top: 30px;">
    {{ $schools->links() }}
</div>
@endsection


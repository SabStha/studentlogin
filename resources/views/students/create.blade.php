@extends('layouts.app')

@section('title', '生徒登録 - AIWA FARM')

@section('content')
<div class="card" style="background-color: #f9f9f9;">
    <h1 style="font-size: 24px; margin-bottom: 25px; font-weight: bold;">生徒登録</h1>
    
    <form method="POST" action="{{ route('students.store') }}">
        @csrf

        <div class="form-group checkbox-group">
            <input type="checkbox" name="oc_attendance" id="oc_attendance" value="1" {{ old('oc_attendance') ? 'checked' : '' }}>
            <label for="oc_attendance">OC出席</label>
        </div>

        <div class="form-group">
            <label for="oc_reservation_date">OC予約日時</label>
            <input type="date" name="oc_reservation_date" id="oc_reservation_date" value="{{ old('oc_reservation_date') }}" placeholder="dd/mm/yyyy">
        </div>

        <div class="form-group checkbox-group">
            <input type="checkbox" name="online" id="online" value="1" {{ old('online') ? 'checked' : '' }}>
            <label for="online">オンライン</label>
        </div>

        <div class="form-group">
            <label for="name_english">名前 英語</label>
            <input type="text" name="name_english" id="name_english" value="{{ old('name_english') }}" required>
        </div>

        <div class="form-group">
            <label for="name_kana">名前 カナ</label>
            <input type="text" name="name_kana" id="name_kana" value="{{ old('name_kana') }}" required>
        </div>

        <div class="form-group">
            <label for="nationality">国籍</label>
            <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" required>
        </div>

        <div class="form-group">
            <label for="gender">性別</label>
            <select name="gender" id="gender" required>
                <option value="">選択してください</option>
                <option value="男" {{ old('gender') == '男' ? 'selected' : '' }}>男</option>
                <option value="女" {{ old('gender') == '女' ? 'selected' : '' }}>女</option>
                <option value="その他" {{ old('gender') == 'その他' ? 'selected' : '' }}>その他</option>
            </select>
        </div>

        <div class="form-group">
            <label for="age">年齢</label>
            <input type="number" name="age" id="age" value="{{ old('age') }}" required min="1" max="150">
        </div>

        <div class="form-group">
            <label for="email">Eメール</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="jlpt_level">JLPT</label>
            <select name="jlpt_level" id="jlpt_level">
                <option value="">選択してください</option>
                <option value="N1" {{ old('jlpt_level') == 'N1' ? 'selected' : '' }}>N1</option>
                <option value="N2" {{ old('jlpt_level') == 'N2' ? 'selected' : '' }}>N2</option>
                <option value="N3" {{ old('jlpt_level') == 'N3' ? 'selected' : '' }}>N3</option>
                <option value="N4" {{ old('jlpt_level') == 'N4' ? 'selected' : '' }}>N4</option>
                <option value="N5" {{ old('jlpt_level') == 'N5' ? 'selected' : '' }}>N5</option>
            </select>
        </div>

        <div class="form-group">
            <label for="school_id">今の学校 ※ない場合は学校登録から登録後、画面リフレッシュ（保存してないデータはクリアされる）</label>
            <select name="school_id" id="school_id">
                <option value="">選択してください</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="student_number">学籍番号</label>
            <input type="text" name="student_number" id="student_number" value="{{ old('student_number') }}">
        </div>

        <div class="form-group">
            <label for="home_country_education">母国の最終学歴(大学の場合学校名まで入力)</label>
            <textarea name="home_country_education" id="home_country_education" rows="3">{{ old('home_country_education') }}</textarea>
        </div>

        <div class="form-group">
            <label for="referrer">紹介者</label>
            <input type="text" name="referrer" id="referrer" value="{{ old('referrer') }}">
        </div>

        <div class="form-group">
            <label for="enrollment_year">入学年度</label>
            <input type="number" name="enrollment_year" id="enrollment_year" value="{{ old('enrollment_year', date('Y')) }}" min="2000" max="2100">
        </div>

        <div class="form-group">
            <label for="status">ステータス</label>
            <select name="status" id="status">
                <option value="試験待ち" {{ old('status', '試験待ち') == '試験待ち' ? 'selected' : '' }}>試験待ち</option>
                <option value="1年合格" {{ old('status') == '1年合格' ? 'selected' : '' }}>1年合格</option>
                <option value="2年合格" {{ old('status') == '2年合格' ? 'selected' : '' }}>2年合格</option>
                <option value="不合格" {{ old('status') == '不合格' ? 'selected' : '' }}>不合格</option>
            </select>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">キャンセル</a>
        </div>
    </form>
</div>
@endsection


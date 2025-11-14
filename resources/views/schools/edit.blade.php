@extends('layouts.app')

@section('title', '学校編集 - AIWA FARM')

@section('content')
<div class="card" style="background-color: #f9f9f9;">
    <h1 style="font-size: 24px; margin-bottom: 25px; font-weight: bold;">学校編集</h1>
    
    <form method="POST" action="{{ route('schools.update', $school) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">学校名</label>
            <input type="text" name="name" id="name" value="{{ old('name', $school->name) }}" required>
        </div>

        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $school->postal_code) }}" placeholder="123-4567">
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $school->address) }}">
        </div>

        <div class="form-group">
            <label for="contact_person">担当者</label>
            <input type="text" name="contact_person" id="contact_person" value="{{ old('contact_person', $school->contact_person) }}">
        </div>

        <div class="form-group">
            <label for="position">役職</label>
            <input type="text" name="position" id="position" value="{{ old('position', $school->position) }}">
        </div>

        <div class="form-group">
            <label for="phone">電話番号</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $school->phone) }}" placeholder="090-1234-5678">
        </div>

        <div class="form-group">
            <label for="fax">FAX番号</label>
            <input type="text" name="fax" id="fax" value="{{ old('fax', $school->fax) }}" placeholder="090-1234-5678">
        </div>

        <div class="form-group">
            <label for="email">Eメール</label>
            <input type="email" name="email" id="email" value="{{ old('email', $school->email) }}">
        </div>

        <div class="form-group">
            <label for="website">Webサイト</label>
            <input type="url" name="website" id="website" value="{{ old('website', $school->website) }}" placeholder="https://example.com">
        </div>

        <div class="form-group">
            <label for="memo">メモ</label>
            <textarea name="memo" id="memo" rows="4">{{ old('memo', $school->memo) }}</textarea>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">更新</button>
            <a href="{{ route('schools.index') }}" class="btn btn-secondary">キャンセル</a>
        </div>
    </form>
</div>
@endsection


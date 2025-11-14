@extends('layouts.app')

@section('title', '生徒登録 - AIWA FARM')

@section('content')
<div class="card" style="background-color: #f9f9f9;">
    <h1 style="font-size: 24px; margin-bottom: 25px; font-weight: bold;">生徒登録</h1>
    
    <form method="POST" action="{{ route('students.store') }}" enctype="multipart/form-data">
        @csrf

        <div style="display: flex; gap: 20px; align-items: flex-start; margin-bottom: 20px;">
            <div style="flex: 0 0 200px;">
                <div id="image-preview" style="width: 200px; height: 250px; border: 2px solid #ddd; border-radius: 4px; background-color: #f5f5f5; display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; overflow: hidden;">
                    <div id="no-image-text" style="text-align: center; color: #999;">
                        <div style="color: #dc3545; font-size: 12px; margin-bottom: 5px;">not appear</div>
                        <div style="font-size: 16px;">No Image</div>
                    </div>
                    <img id="preview-img" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <label for="image" class="btn btn-primary" style="flex: 1; text-align: center; cursor: pointer; margin: 0; padding: 8px;">画像選択</label>
                    <button type="button" id="remove-image" class="btn btn-danger" style="flex: 1; display: none; padding: 8px;">画像削除</button>
                </div>
                <input type="file" name="image" id="image" accept="image/*" style="display: none;" onchange="previewImage(this)">
            </div>
            <div style="flex: 1;">
                <div class="form-group">
                    <label for="oc_attendance" style="display: block; margin-bottom: 10px;">OC出席</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="oc_attendance" id="oc_attendance" value="1" {{ old('oc_attendance') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="oc_reservation_date">OC予約日時</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <input type="date" name="oc_reservation_date" id="oc_reservation_date" value="{{ old('oc_reservation_date') }}" placeholder="dd/mm/yyyy" style="flex: 1;">
                        <input type="time" name="oc_reservation_time" id="oc_reservation_time" value="{{ old('oc_reservation_time') }}" style="flex: 1;">
                        <select name="oc_reservation_ampm" id="oc_reservation_ampm" style="flex: 0 0 80px;">
                            <option value="AM" {{ old('oc_reservation_ampm', 'AM') == 'AM' ? 'selected' : '' }}>AM</option>
                            <option value="PM" {{ old('oc_reservation_ampm') == 'PM' ? 'selected' : '' }}>PM</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="online" style="display: block; margin-bottom: 10px;">オンライン</label>
                    <label class="toggle-switch">
                        <input type="checkbox" name="online" id="online" value="1" {{ old('online') ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
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
                    <label for="school_id">今の学校</label>
                    <select name="school_id" id="school_id">
                        <option value="">選択してください</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                    <div style="font-size: 11px; color: #dc3545; margin-top: 5px;">※ない場合は学校登録から登録後、画面リフレッシュ（保存してないデータはクリアされる）</div>
                </div>

                <div class="form-group">
                    <label for="student_number">学籍番号</label>
                    <input type="text" name="student_number" id="student_number" value="{{ old('student_number') }}">
                </div>

                <div class="form-group">
                    <label for="home_country_education">母国の最終学歴(大学の場合学校名まで入力)</label>
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <button type="button" class="education-btn" data-education="高" style="padding: 8px 15px; background-color: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">高</button>
                        <button type="button" class="education-btn" data-education="専" style="padding: 8px 15px; background-color: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">専</button>
                        <button type="button" class="education-btn" data-education="大" style="padding: 8px 15px; background-color: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">大</button>
                        <button type="button" class="education-btn" data-education="院" style="padding: 8px 15px; background-color: #0066cc; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">院</button>
                    </div>
                    <textarea name="home_country_education" id="home_country_education" rows="3" placeholder="学校名を入力してください">{{ old('home_country_education') }}</textarea>
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
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">登録</button>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">キャンセル</a>
        </div>
    </form>
</div>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 26px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
    border: 1px solid #ddd;
}

.toggle-switch input:checked + .toggle-slider {
    background-color: #0066cc;
}

.toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(24px);
}

.toggle-switch input:focus + .toggle-slider {
    box-shadow: 0 0 1px #0066cc;
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview-img');
    const noImageText = document.getElementById('no-image-text');
    const removeBtn = document.getElementById('remove-image');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            noImageText.style.display = 'none';
            removeBtn.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

document.getElementById('remove-image').addEventListener('click', function() {
    document.getElementById('image').value = '';
    document.getElementById('preview-img').src = '';
    document.getElementById('preview-img').style.display = 'none';
    document.getElementById('no-image-text').style.display = 'block';
    this.style.display = 'none';
});

// Convert 24-hour time to 12-hour with AM/PM
document.getElementById('oc_reservation_time').addEventListener('change', function() {
    const timeInput = this;
    const ampmSelect = document.getElementById('oc_reservation_ampm');
    
    if (timeInput.value) {
        const [hours, minutes] = timeInput.value.split(':');
        const hour24 = parseInt(hours);
        
        if (hour24 >= 12) {
            ampmSelect.value = 'PM';
            if (hour24 > 12) {
                timeInput.value = String(hour24 - 12).padStart(2, '0') + ':' + minutes;
            }
        } else {
            ampmSelect.value = 'AM';
            if (hour24 === 0) {
                timeInput.value = '12:' + minutes;
            }
        }
    }
});

// Convert 12-hour time back to 24-hour format before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const timeInput = document.getElementById('oc_reservation_time');
    const ampmSelect = document.getElementById('oc_reservation_ampm');
    
    if (timeInput.value) {
        const [hours, minutes] = timeInput.value.split(':');
        let hour24 = parseInt(hours);
        
        if (ampmSelect.value === 'PM' && hour24 !== 12) {
            hour24 += 12;
        } else if (ampmSelect.value === 'AM' && hour24 === 12) {
            hour24 = 0;
        }
        
        timeInput.value = String(hour24).padStart(2, '0') + ':' + minutes;
    }
});

// Education level buttons
document.querySelectorAll('.education-btn').forEach(button => {
    button.addEventListener('click', function() {
        // Remove active class from all buttons
        document.querySelectorAll('.education-btn').forEach(btn => {
            btn.style.backgroundColor = '#0066cc';
            btn.style.opacity = '1';
        });
        
        // Add active state to clicked button
        this.style.backgroundColor = '#0052a3';
        this.style.opacity = '0.9';
        
        // Get the education level
        const educationLevel = this.getAttribute('data-education');
        const textarea = document.getElementById('home_country_education');
        
        // Map to full names
        const educationMap = {
            '高': '高校',
            '専': '専門学校',
            '大': '大学',
            '院': '大学院'
        };
        
        // If textarea is empty or just contains the education level, set it
        const currentValue = textarea.value.trim();
        if (!currentValue || currentValue === educationMap[educationLevel]) {
            textarea.value = educationMap[educationLevel];
        } else if (!currentValue.startsWith(educationMap[educationLevel])) {
            // If it doesn't start with the education level, prepend it
            textarea.value = educationMap[educationLevel] + ' ' + currentValue;
        }
        
        // Focus on textarea for school name input
        textarea.focus();
    });
});
</script>
@endsection

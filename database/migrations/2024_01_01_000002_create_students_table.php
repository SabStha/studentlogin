<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name_english');
            $table->string('name_kana');
            $table->string('nationality');
            $table->string('gender');
            $table->integer('age');
            $table->string('email');
            $table->string('jlpt_level')->nullable();
            $table->foreignId('school_id')->nullable()->constrained()->onDelete('set null');
            $table->string('student_number')->nullable();
            $table->text('home_country_education')->nullable();
            $table->string('referrer')->nullable();
            $table->boolean('oc_attendance')->default(false);
            $table->date('oc_reservation_date')->nullable();
            $table->boolean('online')->default(false);
            $table->integer('enrollment_year')->nullable();
            $table->string('status')->default('試験待ち'); // 試験待ち, 1年合格, 2年合格, 不合格
            $table->boolean('applied')->default(false);
            $table->boolean('participated')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};


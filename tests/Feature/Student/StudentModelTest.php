<?php

use App\Models\Student;
use Illuminate\Support\Facades\DB;

test('cpr is encrypted in database and decrypted on access', function () {
    $student = Student::factory()->create(['cpr' => '010190-1234']);

    $raw = DB::table('students')->where('id', $student->id)->value('cpr');
    expect($raw)->not->toBe('010190-1234');

    $student->refresh();
    expect($student->cpr)->toBe('010190-1234');
});

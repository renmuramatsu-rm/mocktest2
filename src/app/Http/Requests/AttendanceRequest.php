<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'requested_clockIn' => ['date_format:H:i', 'before:requested_clockOut'],
            'requested_clockOut' => ['date_format:H:i'],
            'remark' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'requested_clockIn.before' => '出勤時間もしくは退勤時間が不適切な値です',
            'remark.required' => '備考を記入してください',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAttendanceRequest extends FormRequest
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
            'clockIn'    => ['required','date_format:H:i', 'before:clockOut'],
            'clockOut'   => ['required','date_format:H:i'],
            'remark'     => ['required'],
            'restIn.*'   => ['nullable','date_format:H:i', 'before:clockOut', 'after:clockIn'],
            'restOut.*'  => ['nullable','date_format:H:i', 'before:clockOut', 'after:clockIn'],
        ];
    }

    public function messages()
    {
        return [
            'clockIn.before'   => '出勤時間もしくは退勤時間が不適切な値です',
            'remark.required'  => '備考を記入してください',
            'restIn.*.before'  => '休憩時間が勤務時間外です',
            'restIn.*.after'   => '休憩時間が勤務時間外です',
            'restOut.*.before' => '休憩時間が勤務時間外です',
            'restOut.*.after'  => '休憩時間が勤務時間外です',
        ];
    }
}

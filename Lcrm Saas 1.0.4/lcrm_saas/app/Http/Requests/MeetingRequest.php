<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;


class MeetingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'meeting_subject' => 'required',
            'starting_date' => 'required',
            'ending_date' => 'required',
            'responsible_id' => "required",
            'location' => "required",
            'company_attendees' => "required",
        ];
    }

    /**
     * Get the validator instance for the request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
}

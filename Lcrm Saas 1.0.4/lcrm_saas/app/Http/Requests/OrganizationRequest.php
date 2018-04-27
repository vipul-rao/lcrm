<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Repositories\OrganizationRepository;

class OrganizationRequest extends FormRequest
{
    private $organizationRepository;

    public function __construct(
            OrganizationRepository $organizationRepository)
    {
        parent::__construct();
        $this->organizationRepository = $organizationRepository;
    }

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
    public function rules(Request $request)
    {
        $org = $this->route('organization');
        $organization = 'PUT' === $request->method() ? $this->organizationRepository->find($org) : null;

        return [
                'name' => 'required|min:3|max:50|alpha_dash',
                'phone_number' => 'required|regex:/^\d{5,15}?$/',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('organizations')->ignore($org),
                ],

            'owner_first_name' => 'required|min:3|max:50|alpha_dash',
            'owner_last_name' => 'required|min:3|max:50|alpha_dash',
            'owner_phone_number' => 'required|regex:/^\d{5,15}?$/',
            'owner_email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore('PUT' === $request->method() ? $organization->user_id : null),
            ],
            'owner_password' => 'PUT' === $request->method() ? 'nullable|min:6|confirmed' : 'required|min:6|confirmed',
            'owner_password_confirmation' => 'PUT' === $request->method() ? 'nullable|same:owner_password' : 'required|same:owner_password',
            'plan_id' => 'PUT' === $request->method() ? 'nullable' : 'required',
            'duration' => 'PUT' === $request->method() ? 'nullable' : 'required',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'Phone number can be only numbers',
            'owner_phone_number.regex' => 'Phone number can be only numbers',
        ];
    }
}

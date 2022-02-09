<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Hierarchies;
use App\Rules\canBeHeadRule;
use App\Rules\SalaryRangeRule;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:2|max:256',
            'email' => 'required|email|unique:App\Models\Employee,email',
            'phone' => 'required|unique:App\Models\Employee,phone_number',
            'image' => 'required|mimes:png,jpg|dimensions:min_width=300,min_height=300|max:5120',
            'position_id'=>'required|exists:App\Models\Position,position_name',
            'salary' => ['required', new SalaryRangeRule],
            'head' => 'sometimes|nullable|exists:App\Models\Employee,name',
            'date' => 'required|date_format:"d.m.y"'
        ];
    }



    public function attributes()
    {
        return [

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Enter the employee\'s name.',
            'email.required' => 'Enter the employee\'s email.',
            'email.email' => 'You have entered invalid email address.',
            'phone.required' => 'Enter the employee\'s phone number',
            'phone.min' => 'Invalid phone number',
            'image.required' => 'Photo is required.',
            'image.mimes' => 'Selected photo is in the wrong format',
            'image.dimensions' => 'The dimensions of the photo are wrong.',
            'image.max' => 'Invalid file. File size is too big.',
            'position_id.required' => 'Enter the position',
            'position_id.exists' => 'Position doesn\'t exist in database',
            'salary.required' => 'Enter the employee\'s salary',
            'salary.min' => 'Enter the employee\'s salary',
            'head.exists' => 'Employee with this name is not in database',
            'date.required' => 'Enter the employee\'s employment date',
            'date.date' => 'The value must be date',
            'email.unique' => 'Employee with this email already exist.',
            'phone.unique' => 'Employee with this phone number already exist.',
        ];
    }
}

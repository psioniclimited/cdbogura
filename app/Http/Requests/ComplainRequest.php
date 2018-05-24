<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ComplainRequest extends Request {

    /**
     * Determine if the customer is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }


    public function messages()
    {
        return [
            'customer_id.required' => 'Select a Customer',
            'complain_status_id.required' => 'Select Complain Status'
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'description' => 'required',
            'date' => 'required',
            'customer_id' => 'required',
            'complain_status_id' => 'required',
        ];
    }

}

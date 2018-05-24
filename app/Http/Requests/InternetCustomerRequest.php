<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class InternetCustomerRequest extends Request {

    /**
     * Determine if the customer is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required',
            'monthly_bill' => 'required',
            'subscription_details_id' => 'required',
            'sectors_id' => 'required',
            'roads_id' => 'required',
            'houses_id' => 'required',
            'last_paid' => 'required',
        ];
    }

}

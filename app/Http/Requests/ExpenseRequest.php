<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ExpenseRequest extends Request {

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
            'transaction_date' => 'required',
            'note' => 'required',
            'paid_with' => 'required',
            'expense_category' => 'required',
            'ref_number' => 'required',
            'amount' => 'required',
        ];
    }

}
<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class MemberWebRequest extends Request
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
            'fullname' => 'required|min:10',
            'addrs' => 'required',
            'mob_num' => 'required',
            'off_num' => 'required',
            'email' => 'required',
            'member_type' => 'required',
            'password' => 'required|confirmed',

        ];
    }
}

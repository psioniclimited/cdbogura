<?php

namespace App\Modules\User\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

    
    use Authenticatable,
        CanResetPassword;
        
    use SoftDeletes;

    use EntrustUserTrait {
        EntrustUserTrait::restore insteadof SoftDeletes;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','territory_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function role_user() {
        return $this->hasOne('App\Modules\User\Models\RoleUser');
    }

    /**
     * The sectors that belong to the user.
     */
    public function sectors()
    {
        return $this->belongsToMany('App\Modules\CableManagement\Models\Sector', 'users_has_sectors', 'users_id', 'sectors_id');
    }

    /**
     * Get the customer details for the user.
     */
    public function customer_details()
    {
        return $this->hasMany('App\Modules\CableManagement\Models\CustomerDetails', 'users_id');
    }

}

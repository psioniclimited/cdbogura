<?php

namespace App\Http\Middleware;

use App\Modules\CableManagement\Models\Customer;
use Closure;
use Entrust;
class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Entrust::user();
        $customer = Customer::find($request->id);

        if(Entrust::hasRole('admin'))
            return $next($request);
        if(Entrust::hasRole('manager') && $user->territory_id == $customer->territory_id)
            return $next($request);
        abort(403);
    }
}

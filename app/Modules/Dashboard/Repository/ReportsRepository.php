<?php
namespace App\Modules\Dashboard\Repository;
use App\Modules\User\Models\RoleUser;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\User\Models\User;
use Carbon\Carbon;
use DB;
class ReportsRepository{
	/**
	 * Get count of bill collectors
	 * @return collectorCount
	 */
	public function totalDue(){
		// Due Internet Bill Calculation
		$next_month = Carbon::now()->addMonth()->format('Y-m-01');
        
        $internet_due_bill = DB::table('customers')
                ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
                ->where('customer_status_id', '1')
                ->where('subscription_types_id', '3')
                ->where('last_paid', '<', $next_month)
                ->whereNull('deleted_at');
        // $internet_due_bill = $internet_due_bill->first();
        // $internet_due_bill = $internet_due_bill->total;
        // Due Dish Bill Calculation
        $dish_due_bill = DB::table('customers')
                ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
                ->where('customer_status_id', '1')
                ->where('subscription_types_id', '!=', '3')
                ->where('last_paid', '<', $next_month)
                ->whereNull('deleted_at');
        // $dish_due_bill = $dish_due_bill->first();
        // $dish_due_bill = $dish_due_bill->total;   


        return $internet_due_bill->first()->total + $dish_due_bill->first()->total;
	}
	/**
	 * Get customer count
	 * @return customerCount
	 */
	public function customerCount(){
		$customerCount = Customer::count();
		return $customerCount;
	}

	public function userCount(){
		$userCount = User::count();
		return $userCount;
	}
	/**
	 * Get daily collection count
	 * @return dailyCallectionCount
	 */
	public function dailyCollectionCount($start, $end){
        // Daily collection count is retrieved from the db using whereBetween
        $dailyCollectionCount = CustomerDetails::whereBetween('timestamp', [$start, $end])->count();
        return $dailyCollectionCount;
	}
	/**
	 * Daily collection sum
	 * @return dailyCollectionSum
	 */
	public function dailyCollectionSum($start, $end){
		$dailyCollectionSum = CustomerDetails::whereBetween('timestamp', [$start, $end])->sum('total');
		if($dailyCollectionSum != null){
			return $dailyCollectionSum;
		}
		else{
			return 0;
		}
	}

	public function totalCollectionSum(){
		$totalCollectionSum = CustomerDetails::sum('total');
		return $totalCollectionSum;
	}

	/**
	 * [analogDigitalBillCollectionSum - sum of analog & digital bill]
	 * @return [int] [bill amount]
	 */
	public function analogDigitalBillCollectionSum(){
		$totalCollectionSum = CustomerDetails::whereHas('customers', function($query){
            $query->where('subscription_types_id', '!=', 3);
        })
        ->sum('total');
		return $totalCollectionSum;
	}

	/**
	 * [internetBillCollectionSum - sum of internet bill]
	 * @return [type] [description]
	 */
	public function internetBillCollectionSum(){
		$totalCollectionSum = CustomerDetails::whereHas('customers', function($query){
            $query->where('subscription_types_id', '=', 3);
        })
        ->sum('total');
		return $totalCollectionSum;
	}

	public function totalDueCount(){
		$totalDueCount = CustomerDetails::where('due', '=', '1')->count();
		return $totalDueCount;
	}

	public function daily_collection_query($start_of_the_30_days, $end_of_the_30_days) {
		// dd($start_of_the_30_days);
		// dd($end_of_the_30_days);
		return DB::select("SELECT 
                SUM(total) as total
            FROM
                customer_details
            WHERE
                timestamp BETWEEN ? AND ?
                GROUP BY CAST(timestamp AS DATE)", [$start_of_the_30_days, $end_of_the_30_days]);
	}

	public function area_wise_collection_query($first_day_of_this_month_with_timestamp, $last_day_of_this_month_with_timestamp) {
		return DB::select("SELECT 
                    territory.name, SUM(total) as total
                FROM
                    customer_details
                    JOIN customers ON customers.customers_id = customer_details.customers_id
                    JOIN territory ON territory.id = customers.territory_id
                WHERE
                    timestamp BETWEEN ? AND ?
                    GROUP BY customers.territory_id", [$first_day_of_this_month_with_timestamp, $last_day_of_this_month_with_timestamp]);
	}

	public function collector_ranking_query($first_day_of_this_month_with_timestamp, $last_day_of_this_month_with_timestamp) {
		return DB::select("SELECT 
                            users.name, SUM(total) AS total
                        FROM
                            customer_details
                                JOIN
                            users ON users.id = customer_details.users_id
                        WHERE
                            timestamp BETWEEN ? AND ? GROUP BY customer_details.users_id
                        ORDER BY total desc", [$first_day_of_this_month_with_timestamp, $last_day_of_this_month_with_timestamp]);
	}


	public function monthly_bill_collection($start_of_the_month_with_timestamp, $end_of_the_month_with_timestamp) {
		return DB::select("SELECT SUM(total) as total FROM customer_details
                                WHERE timestamp BETWEEN ? AND ?"
                                , [$start_of_the_month_with_timestamp, $end_of_the_month_with_timestamp]);
	}

	public function collection_of_this_month() {
		$collection_of_this_month =  DB::select("SELECT SUM(total) as total, SUM(discount) as discount FROM customer_details
                                WHERE (timestamp BETWEEN ? AND ?) AND (deleted_at=NULL)"
                                ,[Carbon::now()->startOfMonth()->toDateString(), Carbon::now()->endOfMonth()->toDateString()]);
//		 dd($collection_of_this_month);
        return $collection_of_this_month[0]->total - $collection_of_this_month[0]->discount;
	}


	 public function totalDishDue() {
         $next_month = Carbon::now()->addMonth()->format('Y-m-01');
         // due for prepaid customers
         $dish_prepaid_due_bill = DB::table('customers')
             ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
             ->where('customer_status_id', '1')
             ->where('subscription_types_id', '!=', '3')
             ->where('last_paid', '<', $next_month)
             ->where('is_postpaid', 0)
             ->whereNull('deleted_at');

         $next_month = Carbon::now()->format('Y-m-01');
         // due for post paid users
         $dish_postpaid_due_bill = DB::table('customers')
             ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
             ->where('customer_status_id', '1')
             ->where('subscription_types_id', '!=', '3')
             ->where('last_paid', '<', $next_month)
             ->where('is_postpaid', 1)
             ->whereNull('deleted_at');

         if($dish_prepaid_due_bill->first() == null){
             return $dish_postpaid_due_bill->first()->total;
         }
         elseif ($dish_postpaid_due_bill->first() == null){
             return $dish_prepaid_due_bill->first()->total;
         }
         else{
             return $dish_postpaid_due_bill->first()->total + $dish_prepaid_due_bill->first()->total;
         }
     }

    public function totalInternetDue() {
        $next_month = Carbon::now()->addMonth()->format('Y-m-01');

        // due for prepaid customers
        $internet_prepaid_due_bill = DB::table('customers')
            ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
            ->where('customer_status_id', '1')
            ->where('subscription_types_id', '3')
            ->where('last_paid', '<', $next_month)
            ->where('is_postpaid', 0)
            ->whereNull('deleted_at');

        $next_month = Carbon::now()->format('Y-m-01');
        // due for post paid users
        $internet_postpaid_due_bill = DB::table('customers')
            ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
            ->where('customer_status_id', '1')
            ->where('subscription_types_id', '3')
            ->where('last_paid', '<', $next_month)
            ->where('is_postpaid', 1)
            ->whereNull('deleted_at');

        if($internet_prepaid_due_bill->first() == null){
            return $internet_postpaid_due_bill->first()->total;
        }
        elseif ($internet_postpaid_due_bill->first() == null){
            return $internet_prepaid_due_bill->first()->total;
        }
        else{
            return $internet_postpaid_due_bill->first()->total + $internet_prepaid_due_bill->first()->total;
//            return response()->json($internet_postpaid_due_bill->first()->total + $internet_prepaid_due_bill->first()->total);
        }
    }

	

}
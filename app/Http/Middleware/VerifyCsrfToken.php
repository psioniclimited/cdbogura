<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'authmob',
        'create_customer_process',
        'create_territory_process',	
        'create_sector_process',
        'create_road_process',
        'create_house_process',
        'chart_data',
        'customers/*/delete',
        'internetcustomers/*/delete',
        'users/*/delete',
        'billcollectors/*/delete',
        'sync/*',
        'edit_customers_process/*',
        'refund_bill_process',
        'discount_internet_bill_process',
        'refund_internet_bill_process',
        'discount_bill_process',
        'collect_bill_process',
        'create_expense_process',
        'chart_of_accounts_update_expense',
        'chart_of_accounts_add_expense',
        'edit_complain_status',
        'create_internet_customer_process',
        'edit_internet_customer_process',
        'create_partner_process',
        'update_partner_process',
        'partner/*/delete',
    ];
}

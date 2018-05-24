<?php

/*
|--------------------------------------------------------------------------
| Company Routes
|--------------------------------------------------------------------------
|
| All the routes for Company module has been written here
|
|
*/
Route::group(['middleware' => ['web']], function () {   
    
    // Expense
    Route::get('create_expense', 'App\Modules\Accounting\Controllers\ExpensesController@createExpense');
    Route::post('create_expense_process', 'App\Modules\Accounting\Controllers\ExpensesController@createExpenseProcess');
    Route::get('edit_expense/{posting_id}', 'App\Modules\Accounting\Controllers\ExpensesController@editExpense');
    Route::post('edit_expense_process', 'App\Modules\Accounting\Controllers\ExpensesController@editExpenseProcess');
    Route::get('get_expense_category', 'App\Modules\Accounting\Controllers\ExpensesController@getExpenseCategory');
    Route::get('expense_list', 'App\Modules\Accounting\Controllers\ExpensesController@viewExpenseList');
    Route::get('expense_sum', 'App\Modules\Accounting\Controllers\ExpensesController@expenseSum');

    // Expense Category
    Route::get('chart_of_accounts_expense', 'App\Modules\Accounting\Controllers\ExpensesController@chartOfAccountsExpense');
    Route::get('chart_of_accounts_list', 'App\Modules\Accounting\Controllers\ExpensesController@chartOfAccountsList');
    Route::get('chart_of_accounts_expense/{chart_of_accounts_id}/edit', 'App\Modules\Accounting\Controllers\ExpensesController@chartOfAccountsExpenseEdit');
    Route::post('chart_of_accounts_update_expense', 'App\Modules\Accounting\Controllers\ExpensesController@chartOfAccountsExpenseUpdate');
    Route::post('chart_of_accounts_add_expense', 'App\Modules\Accounting\Controllers\ExpensesController@chartOfAccountsAddExpense');

    // Chart of Accounts
    Route::get('create_chart_of_account', 'App\Modules\Accounting\Controllers\ChartOfAccountsController@createChartOfAccount');
    Route::post('create_chart_of_account_process', 'App\Modules\Accounting\Controllers\ChartOfAccountsController@createChartOfAccountProcess');
    Route::post('chart_of_account/{chart_of_account}/delete', 'App\Modules\Accounting\Controllers\ChartOfAccountsController@deleteChartOfAccountProcess');
    Route::get('chart_of_accounts/{chart_of_accounts_id}/edit', 'App\Modules\Accounting\Controllers\ChartOfAccountsController@chartOfAccountsEdit');
    Route::post('chart_of_accounts_update/{chart_of_accounts_id}', 'App\Modules\Accounting\Controllers\ChartOfAccountsController@chartOfAccountsUpdate');


});


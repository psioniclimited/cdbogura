<ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>
    <li class="active">
        <a href="{{url('dashboard')}}">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>            
        </a>        
    </li>
    <li class="disabled-li" {!! Request::is('customers') || Request::is('create_customer') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Dish Customers</span>
            <span class="pull-right-container">
                <i class="fa fa-lock pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('customers.read'))
                <li {!! Request::is('customers') ? ' class="active"' : null !!}><a href="{{url('customers')}}"><i class="fa fa-circle-o"></i> All Dish Customers</a></li>
            @endif
            @if(Entrust::can('customers.create'))
                <li {!! Request::is('create_customer') ? ' class="active"' : null !!}><a href="{{url('create_customer')}}"><i class="fa fa-circle-o"></i> New Dish Customer</a></li>
            @endif            
        </ul>
    </li>
    <li {!! Request::is('internetcustomers') || Request::is('create_internet_customer') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Internet Customers</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('internetcustomers.read'))
                <li {!! Request::is('internetcustomers') ? ' class="active"' : null !!}><a href="{{url('internetcustomers')}}"><i class="fa fa-circle-o"></i> All Internet Customers</a></li>
            @endif
            @if(Entrust::can('internetcustomers.create'))
                <li {!! Request::is('create_internet_customer') ? ' class="active"' : null !!}><a href="{{url('create_internet_customer')}}"><i class="fa fa-circle-o"></i> New Internet Customer</a></li>
            @endif
        </ul>
    </li>
    <li {!! Request::is('duelist') || Request::is('internetcustomersduelist') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Due List</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('duelist.read'))
                <li class="disabled-li" {!! Request::is('duelist') ? ' class="active"' : null !!}><a href="{{url('duelist')}}"><i class="fa fa-lock"></i> Dish Customer Due List</a></li>
            @endif
            @if(Entrust::can('internetcustomerduelist.read'))
                <li {!! Request::is('internetcustomersduelist') ? ' class="active"' : null !!}><a href="{{url('internetcustomersduelist')}}"><i class="fa fa-circle-o"></i> Internet Customer Due List</a></li>
            @endif
        </ul>
    </li>
    <li {!! Request::is('collect_bill') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Bill Collection</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('collectbill.access'))
                <li {!! Request::is('collect_bill') ? ' class="active"' : null !!}><a href="{{url('collect_bill')}}"><i class="fa fa-circle-o"></i> Collect Bill</a></li>
            @endif
        </ul>
    </li>
    <li {!! Request::is('create_expense') || Request::is('edit_expense') || Request::is('expense_list') || Request::is('chart_of_accounts') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Expenses</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('create_expense') ? ' class="active"' : null !!}><a href="{{url('create_expense')}}"><i class="fa fa-circle-o"></i> New Expense</a></li>
            <li {!! Request::is('expense_list') ? ' class="active"' : null !!}><a href="{{url('expense_list')}}"><i class="fa fa-circle-o"></i> Expense List</a></li>
            <li {!! Request::is('chart_of_accounts') ? ' class="active"' : null !!}><a href="{{url('chart_of_accounts')}}"><i class="fa fa-circle-o"></i> Expense Category</a></li>
        </ul>
    </li>
    <li {!! Request::is('create_complain') || Request::is('complain_list') || Request::is('*edit_complain/*') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Complain</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li {!! Request::is('create_complain') ? ' class="active"' : null !!}><a href="{{url('create_complain')}}"><i class="fa fa-circle-o"></i> New Complain</a></li>
            <li {!! Request::is('complain_list') ? ' class="active"' : null !!}><a href="{{url('complain_list')}}"><i class="fa fa-circle-o"></i> Complain List</a></li>
        </ul>
    </li>
    <li {!! Request::is('billpendinglist') || Request::is('billcollectionlist') || Request::is('refundhistory') || Request::is('internetbillpendinglist') || Request::is('internetbillcollectionlist') || Request::is('internetrefundhistory') || Request::is('mapreport') || Request::is('chartreport') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Collection Summary</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            {{-- @if(Entrust::can('billpendinglist.read')) 
                <li {!! Request::is('billpendinglist') ? ' class="active"' : null !!}><a href="{{url('billpendinglist')}}"><i class="fa fa-circle-o"></i>Bill Pending</a></li>
            @endif --}}
            @if(Entrust::can('billcollectionlist.read'))
                <li class="disabled-li" {!! Request::is('billcollectionlist') ? ' class="active"' : null !!}><a href="{{url('billcollectionlist')}}"><i class="fa fa-lock"></i>Dish Bill Collection</a></li>
            @endif
            @if(Entrust::can('refund.access'))
                <li class="disabled-li" {!! Request::is('refundhistory') ? ' class="active"' : null !!}><a href="{{url('refundhistory')}}"><i class="fa fa-lock"></i>Dish Refund History</a></li>
            @endif
            {{-- @if(Entrust::can('internetbillpendinglist.read'))
                <li {!! Request::is('internetbillpendinglist') ? ' class="active"' : null !!}><a href="{{url('internetbillpendinglist')}}"><i class="fa fa-circle-o"></i>Internet Bill Pending</a></li>
            @endif --}}
            @if(Entrust::can('internetbillcollectionlist.read'))
                <li {!! Request::is('internetbillcollectionlist') ? ' class="active"' : null !!}><a href="{{url('internetbillcollectionlist')}}"><i class="fa fa-circle-o"></i>Internet Bill Collection</a></li>
            @endif
            @if(Entrust::can('refund.access'))
                <li {!! Request::is('internetrefundhistory') ? ' class="active"' : null !!}><a href="{{url('internetrefundhistory')}}"><i class="fa fa-circle-o"></i>Internet Refund History</a></li>
            @endif
            @if(Entrust::can('mapreport.read'))
                <li {!! Request::is('mapreport') ? ' class="active"' : null !!}><a href="{{url('mapreport')}}"><i class="fa fa-circle-o"></i>Map Report</a></li>
            @endif
                {{-- <li {!! Request::is('chartreport') ? ' class="active"' : null !!}><a href="{{url('chartreport')}}"><i class="fa fa-circle-o"></i>Chart Report</a></li>    --}}
        </ul>
    </li>
    <li {!! Request::is('partner_list') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Partnership</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('partner.access'))
                <li {!! Request::is('partner_list') ? ' class="active"' : null !!}><a href="{{url('partner_list')}}"><i class="fa fa-circle-o"></i> Partner List</a></li>
            @endif
        </ul>
    </li>
    <li {!! Request::is('allbillcollectors') || Request::is('create_bill_collector') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Bill Collectors</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('billcollectors.read'))
                <li {!! Request::is('allbillcollectors') ? ' class="active"' : null !!}><a href="{{url('allbillcollectors')}}"><i class="fa fa-circle-o"></i> All Bill Collectors</a></li>
            @endif
            @if(Entrust::can('billcollectors.create'))
                <li {!! Request::is('create_bill_collector') ? ' class="active"' : null !!}><a href="{{url('create_bill_collector')}}"><i class="fa fa-circle-o"></i> New Bill Collector</a></li>
            @endif
        </ul>
    </li>
    <li {!! Request::is('allusers') || Request::is('create_users') ? ' class="treeview active"' : ' class="treeview"' !!}>
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Users</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            @if(Entrust::can('users.read'))
                <li {!! Request::is('allusers') ? ' class="active"' : null !!}><a href="{{url('allusers')}}"><i class="fa fa-circle-o"></i> All User</a></li>
            @endif
            @if(Entrust::can('users.create'))
                <li {!! Request::is('create_users') ? ' class="active"' : null !!}><a href="{{url('create_users')}}"><i class="fa fa-circle-o"></i> New User</a></li> 
            @endif           
        </ul>
    </li>
    @if(Entrust::hasRole('admin'))
        <li {!! Request::is('roles') || Request::is('permissions') ? ' class="treeview active"' : ' class="treeview"' !!}>
            <a href="#">
                <i class="fa fa-gears"></i>
                <span>Settings</span>
                <span class="pull-right-container">
                    <i class="fa fa-angle-left pull-right"></i>
                </span>
            </a>
            <ul class="treeview-menu">
                <li {!! Request::is('*o*s') ? ' class="treeview active"' : ' class="treeview"' !!}>
                    <a href="#"><i class="fa fa-circle-o"></i> Permissions
                        <span class="pull-right-container">
                            <i class="fa fa-angle-left pull-right"></i>
                        </span>
                    </a>
                    <ul class="treeview-menu">
                        @if(Entrust::can('roles.access'))
                            <li {!! Request::is('roles') ? ' class="active"' : null !!}><a href="{{url('roles')}}"><i class="fa fa-circle-o"></i> Roles</a></li>
                        @endif 
                        @if(Entrust::can('permissions.access'))
                            <li {!! Request::is('permissions') ? ' class="active"' : null !!}><a href="{{url('permissions')}}"><i class="fa fa-circle-o"></i> Permission</a></li>
                        @endif 
                    </ul>
                </li>
                
               
            </ul>
        </li>
    @endif
</ul>
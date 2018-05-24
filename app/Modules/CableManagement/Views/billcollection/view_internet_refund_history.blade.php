@extends('master')

@section('css')

<!-- DataTable -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">  

@endsection

@section('scripts')

{{-- Datatable --}}
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>
{{-- {!! $dataTable->scripts() !!} --}}
<script type="text/javascript">
    $(document).ready(function () {
        var table = $("#dataTableBuilder").DataTable({
            "serverSide": true,
            "processing": true,
            "stateSave": true,
            "order": [[10, 'desc']],
            "ajax": {
                url: ''
            },
            "columns": [{
                "name": "customers.customer_code",
                "data": "customers.customer_code",
                "title": "Customer Code",
                "orderable": true,
                "searchable": true
            }, {
                "name": "customers.name",
                "data": "customers.name",
                "title": "Name",
                "orderable": true,
                "searchable": true
            }, {
                "name": "customers.phone",
                "data": "customers.phone",
                "title": "Phone",
                "orderable": true,
                "searchable": true
            }, {
                "name": "customers.house.road.sector.sector",
                "data": "customers.house.road.sector.sector",
                "orderable": false,
                "searchable": false,
                "title": "Sector/Moholla/Village"
            }, {
                "name": "customers.house.road.sector.territory.name",
                "data": "customers.house.road.sector.territory.name",
                "orderable": false,
                "searchable": false,
                "title": "Territory"
            }, {
                "name": "customers.subscription_detail.shared",
                "data": "customers.subscription_detail.shared",
                "title": "Shared\/Dedicated",
                "orderable": false,
                "searchable": false
            }, {
                "name": "customers.subscription_detail.bandwidth",
                "data": "customers.subscription_detail.bandwidth",
                "title": "Bandwidth",
                "orderable": false,
                "searchable": false
            }, {
                "name": "total",
                "data": "total",
                "title": "Total",
                "orderable": true,
                "searchable": true
            }, {
                "name": "discount",
                "data": "discount",
                "title": "Discount",
                "orderable": true,
                "searchable": true
            }, {
                "name": "timestamp",
                "data": "timestamp",
                "title": "Timestamp",
                "orderable": true,
                "searchable": true
            }, {
                "name": "users.name",
                "data": "users.name",
                "title": "Collected By",
                "orderable": true,
                "searchable": true
            }, {
                "name": "deleted_at",
                "data": "deleted_at",
                "title": "Refund Time",
                "orderable": true,
                "searchable": true
            }, {
                "name": "location",
                "data": "location",
                "orderable": false,
                "searchable": false,
                "title": "Location"
            }],
            "dom": "Bfrtip",
            "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
        });

    });
</script>

@endsection

@section('side_menu')

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    
</section>
<!-- Main content -->
<section class="content">
<div class="row">
    <div class="col-xs-12">            
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Internet Refund History</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <br>
                {!! $dataTable->table() !!}
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
    <!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
@endsection


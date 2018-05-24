@extends('master')

@section('css')

<!-- DataTable -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">  
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('scripts')
{{-- Datatable --}}
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>
{{-- Select2 --}}
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
{{-- Utils --}}
<script src="{{asset('custom/js/utils.js')}}"></script>
{{-- {!! $dataTable->scripts() !!} --}}
<script type="text/javascript">
    $(document).ready(function () {
        var table = $("#dataTableBuilder").DataTable({
            "serverSide": true,
            "processing": true,
            "stateSave": true,
            "ajax": {
                url: '',
                data: function (data) {
                    data.bill_collector = $('#bill_collector').val();
                    data.territory = $('#territory').val();
                    data.sector = $('#sector').val();
                }
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
                "name": "customers.number_of_connections",
                "data": "customers.number_of_connections",
                "title": "Number of Connections",
                "orderable": true,
                "searchable": true
            }, {
                "name": "total",
                "data": "total",
                "title": "Total",
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
                "name": "location",
                "data": "location",
                "orderable": false,
                "searchable": false,
                "title": "Location"
            }],
            "dom": "Bfrtip",
            "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
        });

        // Select2 fields
        var bill_collector = $('#bill_collector'),
            territory = $('#territory'),
            sector = $('#sector');

        // Bill collector initialized
        bill_collector.select2({
            placeholder: "Select a bill collector",
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/allbillcollectors",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            } 
        });

        // Territory initialized
        territory.select2({
            placeholder: "Enter Territory",
            allowClear: true,
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/allterritory",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        page: params.page
                    }
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            } 
        });

        // Set the parameters as an object
        var parameters = {
            placeholder: "Sector",
            url: '{{URL::to('/')}}/auto/sector',
            selector_id: sector,
            value_id: territory
        }
        
        // Pass it as a parameter to init_select
        // Initialize select2 on sector field
        init_select(parameters);

        // on bill collector change disable 
        // territory and sector select2 fields
        bill_collector.change(function(){
            territory.prop("disabled", true);
            sector.prop("disabled", true);
        });

        //on territory change reinitialize sector
        territory.change(function(){
            // disable bill collector select2 field
            bill_collector.prop("disabled", true);
            //clear selected value of road
            sector.val(null).trigger("change");
            // Set the parameters as an object
            var parameters = {
                placeholder: "Sector",
                url: '{{URL::to('/')}}/auto/sector',
                selector_id: sector,
                value_id: territory
            }
            
            // Pass it as a parameter to init_select
            // Initialize select2 on sector
            init_select(parameters);
        });

        // Show button 
        $('#show').on('click', function(e){
            e.preventDefault();
            // Reload datatable
            table.ajax.reload();
        });

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            bill_collector.val(null).trigger("change");
            territory.val(null).trigger("change");
            sector.val(null).trigger("change");
            // Enable the select2 fields
            bill_collector.prop("disabled", false);
            territory.prop("disabled", false);
            sector.prop("disabled", false);
            // Reload datatable
            table.ajax.reload();
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
                <h3 class="box-title">Bill Pending List of Analog &amp; Digital Customers</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Bill Collector</label>
                            <select id="bill_collector" name="bill_collector" class="form-control select2" >
                                
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Territory</label>
                            <select id="territory" name="territory" class="form-control select2" >
                              
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sector</label>
                            <select id="sector" name="sector" class="form-control select2" >
                                   
                            </select>
                        </div>
                    </div>
                </div><!-- /.row -->
                <div class="row">
                    <div class="col-md-2">
                        <button id="show" class="btn btn-info">Show</button>
                        <button id="clear_filter" class="btn btn-warning">Clear</button>
                        <br>
                    </div>
                </div><!-- row -->
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


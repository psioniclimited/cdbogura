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
                    data.territory = $('#territory').val();
                    data.sector = $('#sector').val();
                    data.road = $('#road').val();
                }
            },
            "columns": [{
                "name": "customer_code",
                "data": "customer_code",
                "title": "Customer Code",
                "orderable": true,
                "searchable": true
            }, {
                "name": "name",
                "data": "name",
                "title": "Name",
                "orderable": true,
                "searchable": true
            }, {
                "name": "phone",
                "data": "phone",
                "title": "Phone",
                "orderable": true,
                "searchable": true
            }, {
                "name": "sector.sector",
                "data": "sector.sector",
                "orderable": false,
                "searchable": false,
                "title": "Sector/Moholla/Village"
            }, {
                "name": "road.road",
                "data": "road.road",
                "title": "Road/Residential area",
                "orderable": true,
                "searchable": true
            }, {
                "name": "house.house",
                "data": "house.house",
                "title": "House",
                "orderable": true,
                "searchable": true
            }, {
                "name": "flat",
                "data": "flat",
                "title": "Flat",
                "orderable": true,
                "searchable": true
            }, {
                "name": "territory.name",
                "data": "territory.name",
                "orderable": false,
                "searchable": false,
                "title": "Territory"
            }, {
                "name": "subscription_detail.shared",
                "data": "subscription_detail.shared",
                "title": "Shared\/Dedicated",
                "orderable": true,
                "searchable": true
            }, {
                "name": "ppoeorip",
                "data": "ppoeorip",
                "title": "PPoE Username/IP",
                "orderable": true,
                "searchable": true
            }, {
                "name": "subscription_detail.bandwidth",
                "data": "subscription_detail.bandwidth",
                "title": "Bandwidth",
                "orderable": true,
                "searchable": true
            }, {
                "name": "monthly_bill",
                "data": "monthly_bill",
                "title": "Monthly Bill",
                "orderable": true,
                "searchable": true
            }, {
                "name": "last_paid",
                "data": "last_paid",
                "title": "Due on",
                "orderable": true,
                "searchable": true
            }, {
                "name": "total_due",
                "data": "total_due",
                "orderable": false,
                "searchable": false,
                "title": "Total Due"
            }, {
                "name": "customer_status.description",
                "data": "customer_status.description",
                "title": "Status",
                "searchable": false,
                "orderable": true
            }],
            "dom": "Bfrtip",
            "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
        });

        // Select2 fields
        var territory = $('#territory'),
            sector = $('#sector'),
            road = $('#road');

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

        //on territory change reinitialize sector
        territory.change(function(){
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

        //on sector change initialize road
        sector.change(function(){
            //clear selected value of road
            road.val(null).trigger("change");
            // Set the parameters as an object
            var parameters = {
                placeholder: "Road",
                url: '{{URL::to('/')}}/auto/road',
                selector_id: road,
                value_id: sector
            }
            // Pass it as a parameter to init_select
            // Initialize select2 on road
            init_select(parameters);
        });

        // Show button 
        $('#show').on('click', function(e){
            e.preventDefault();
            // Reload datatable
            table.ajax.reload();
            // Internet Due Bill
            $.ajax({
                url: "{{URL::to('/internetduebill')}}",
                data:{
                    "territory": $('#territory').val(), 
                    "sector": $('#sector').val(),
                    "road": $('#road').val()
                }
            })
            .done(function( data ) {
                // Set bill
                $('#due_bill').html(data.total);
                console.log(data);
            });
        });

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            territory.val(null).trigger("change");
            sector.val(null).trigger("change");
            road.val(null).trigger("change");
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
                <h3 class="box-title">Due List of Internet Customers</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Due Bill</span>
                                <span class="info-box-number banner-font" id="due_bill"></span>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Territory</label>
                            <select id="territory" name="territory" class="form-control select2" >
                              
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Sector/Moholla/Village*</label>
                            <select id="sector" name="sector" class="form-control select2" >
                                   
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Road/Residential area*</label>
                            <select id="road" name="road" class="form-control select2" >
                                   
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


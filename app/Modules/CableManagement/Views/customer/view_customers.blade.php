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
            "order": [[0, 'asc']],
            "ajax": {
                url: '',
                data: function (data) {
                    data.subscription_type = $('#subscription_type').val();
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
                "name": "number_of_connections",
                "data": "number_of_connections",
                "title": "Number Of Connections",
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
                "name": "customer_status.description",
                "data": "customer_status.description",
                "title": "Status",
                "orderable": true,
                "searchable": true
            }, {
                "name": "action",
                "data": "action",
                "orderable": false,
                "searchable": false,
                "title": "Action"
            }],
            "dom": "Bfrtip",
            "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
        });

        // Select2 fields
        var subscription_type = $('#subscription_type'), 
            territory = $('#territory'),
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
            // Target Bill
            $.ajax({
                url: "{{URL::to('/targetbill')}}",
                data:{
                    "subscription_type": $('#subscription_type').val(), 
                    "territory": $('#territory').val(), 
                    "sector": $('#sector').val(),
                    "road": $('#road').val()
                }
            })
            .done(function( data ) {
                // Set bill
                $('#target_bill').html(data);
            });
        });

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            subscription_type.prop('selectedIndex',0);
            territory.val(null).trigger("change");
            sector.val(null).trigger("change");
            road.val(null).trigger("change");
            // Reload datatable
            table.ajax.reload();
            // Set target bill to 0
            $('#target_bill').html(0);
        });

        // Delete Customer
        $('#confirm_delete').on('show.bs.modal', function(e) {
            var $modal = $(this),
                customer_id = e.relatedTarget.id;

            $('#cancel_delete_button').click(function(e){
                customer_id = null;
            });

            $('#delete_customer').click(function(e){    
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'customers/' + customer_id + '/delete',
                    data: customer_id,
                    success: function(data){
                        table.ajax.reload(null, false);
                        $('#confirm_delete').modal('toggle');
                    }
                });
            });
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
                <h3 class="box-title">Dish Customer list</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Target Bill</span>
                                <span class="info-box-number banner-font" id="target_bill"></span>
                            </div>
                        </div>
                    </div>  
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Subscription Type (Analog/Digital)</label>
                            <select class="form-control" name="subscription_type" id="subscription_type">
                                <option selected disabled>Select Type</option>
                                @foreach($subscription_types as $subscription_type)
                                    <option value="{{$subscription_type->id}}">{{$subscription_type->name}}</option>
                                @endforeach
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

<!-- Delete Customer Modal -->
<div class="modal fade" id="confirm_delete" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Remove Parmanently</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure about this ?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="delete_customer">Delete</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_delete_button">Cancel</button>
            </div>
        </div>
        <!-- /. Modal content ends here -->
    </div>
</div>
<!--  Delete Customer Modal ends here -->
</section>
<!-- /.content -->

@endsection


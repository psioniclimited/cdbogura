@extends('master')

@section('css')

<!-- DataTable -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.0.3/css/buttons.dataTables.min.css">  
<!-- daterange picker -->
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('scripts')
{{-- Datatable --}}
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>
{{-- Date Range Picker --}}
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
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
            "order": [[8, 'desc']],
            "ajax": {
                url: '',
                data: function (data) {
                    data.bill_collector = $('#bill_collector').val();
                    data.territory = $('#territory').val();
                    data.sector = $('#sector').val();
                    data.road = $('#road').val();
                    data.daterange = $('#daterange').val();
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
                "name": "customers.house.road.sector.sector",
                "data": "customers.house.road.sector.sector",
                "orderable": false,
                "searchable": false,
                "title": "Sector/Moholla/Village"
            }, {
                "name": "customers.house.road.road",
                "data": "customers.house.road.road",
                "orderable": false,
                "searchable": false,
                "title": "Road/Residential Area"
            }, {
                "name": "customers.house.house",
                "data": "customers.house.house",
                "orderable": false,
                "searchable": false,
                "title": "House"
            }, {
                "name": "customers.flat",
                "data": "customers.flat",
                "title": "Flat",
                "orderable": true,
                "searchable": true
            }, {
                "name": "customers.house.road.sector.territory.name",
                "data": "customers.house.road.sector.territory.name",
                "orderable": false,
                "searchable": false,
                "title": "Territory"
            }, {
                "name": "bill_month",
                "data": "bill_month",
                "title": "Bill Months",
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
                "name": "discount_button",
                "data": "discount_button",
                "title": "Discount",
                "orderable": false,
                "searchable": false
            }, {
                "name": "refund",
                "data": "refund",
                "orderable": false,
                "searchable": false,
                "title": "Refund"
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
            sector = $('#sector'),
            road = $('#road');

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

        // on bill collector change disable 
        // territory and sector select2 fields
        bill_collector.change(function(){
            territory.prop("disabled", true);
            sector.prop("disabled", true);
            road.prop("disabled", true);
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
            // Collection Count
            $.ajax({
                url: "{{URL::to('/collectionamount')}}",
                data:{
                    "bill_collector": $('#bill_collector').val(), 
                    "territory": $('#territory').val(), 
                    "sector": $('#sector').val(), 
                    "road": $('#road').val(), 
                    "daterange": $('#daterange').val(), 
                }
            })
            .done(function( data ) {
                // Set amount
                $('#collection_sum').html(data);
            });
        });

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            bill_collector.val(null).trigger("change");
            territory.val(null).trigger("change");
            sector.val(null).trigger("change");
            road.val(null).trigger("change");
            $('#daterange').val(null).trigger("change");
            // Enable the select2 fields
            bill_collector.prop("disabled", false);
            territory.prop("disabled", false);
            sector.prop("disabled", false);
            road.prop("disabled", false);
            // Reload datatable
            table.ajax.reload();
            // Set individual collection amount to 0
            $('#collection_sum').html(0);
        });

        // Refund bill
        $('#confirm_refund').on('shown.bs.modal', function(e) {
            var $modal = $(this),
                bill_id = e.relatedTarget.id;  

            $('#cancel_refund_button').click(function(e){
                bill_id = null;
            }); 

            $('#refund_modal_form').submit(function(e){
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: '{{URL::to('/refund_bill_process')}}',
                    data: {
                        bill_id
                    },
                    success: function(data){
                        //close the modal
                        $('#confirm_refund').modal('toggle');
                        // Reload Cards datatable
                        // table.ajax.reload();
                        location.reload();
                    }
                });
            });
        });

        // Add discount
        $('#confirm_discount').on('shown.bs.modal', function(e) {
            var discount_bill_id = e.relatedTarget.id;  

            $('#cancel_discount_button').click(function(e){
                discount_bill_id = null;
            }); 

            $('#discount_modal_form').submit(function(e){
                var form_data = $("#discount_modal_form").serializeArray();
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: '{{URL::to('/discount_bill_process')}}',
                    data: {
                        form_data,
                        discount_bill_id
                    },
                    success: function(data){
                        //close the modal
                        $('#confirm_discount').modal('toggle');
                        // Reload Cards datatable
                        // table.ajax.reload();
                        location.reload();
                    }
                });
            });
        });

        //Date range picker
        $('#daterange').daterangepicker({
            autoUpdateInput: false,
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            locale: {
                format: 'DD/MM/YYYY',
                cancelLabel: 'Clear'
            }
        });

        // Required for Input Initially Empty, starts here
        $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
          $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        });

        $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
        });
        // Required for Input Initially Empty, ends here

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
                <h3 class="box-title">Bill Collection List of Analog &amp; Digital Customers</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive">
                <div class="row">
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bill Amount</span>
                                <span class="info-box-number banner-font" id="collection_sum"></span>
                            </div>
                        </div>
                    </div>  
                </div>
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date range:</label>
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="daterange" name="daterange" value="">
                            </div>
                        </div>
                    </div>
                </div>
                <br>
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

<!-- Refund Modal -->
<!-- Form for Refund Modal  -->
{!! Form::open(array('url' => 'refund_bill_process', 'id' => 'refund_modal_form')) !!}
<div class="modal fade" id="confirm_refund" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Refund Bill</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure about this ?</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-danger" id="refund_bill">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_refund_button">Cancel</button>
            </div>
        </div>
        <!-- /. Modal content ends here -->
    </div>
</div>
<!-- Refund Modal ends here -->
{!! Form::close() !!}
<!-- /.  Form for Refund Modal ends here -->

<!-- Discount Modal -->
<!-- Form for Discount Modal  -->
{!! Form::open(array('url' => 'discount_bill_process', 'id' => 'discount_modal_form')) !!}
<div class="modal fade" id="confirm_discount" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Discount</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Enter discount amount</label>
                    <input type="number" class="form-control" name="discount" id="discount" placeholder="Enter discount amount" min="0">
                    <p>***Enter an amount which is less than specific monthly bill</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-warning" id="add_discount">Submit</button>
                <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_discount_button">Cancel</button>
            </div>
        </div>
        <!-- /. Modal content ends here -->
    </div>
</div>
<!-- Discount Modal ends here -->
{!! Form::close() !!}
<!-- /.  Form for Discount Modal ends here -->

</section>
<!-- /.content -->

@endsection


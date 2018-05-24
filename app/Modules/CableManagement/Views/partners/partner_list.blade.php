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
                "paging": false,
                "order": [[1, 'desc']],
                "ajax": {
                    url: '',
                    data: function (data) {
                        data.daterange = $('#daterange').val();
                    }
                },
                "columns": [{
                    "name": "name",
                    "data": "name",
                    "title": "Partner name",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "percentage",
                    "data": "percentage",
                    "title": "Percentage %",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "total",
                    "data": "total",
                    "title": "Total",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "action",
                    "data": "action",
                    "title": "Action",
                    "orderable": true,
                    "searchable": true
                }],
                "dom": "Bfrtip",
                "buttons": ["csv", "excel", "pdf", "print", "reset", "reload"]
            });

            getBillCollectionForPartners();

            // Show button
            $('#show').on('click', function(e){
                getBillCollectionForPartners()
                e.preventDefault();
                // Reload datatable
                table.ajax.reload();
                // Collection Count
            });

            // Clear button
            $('#clear_filter').on('click', function(e) {
                e.preventDefault();
                $('#daterange').val(null).trigger("change");
                // Reload datatable
                table.ajax.reload();
                // Set individual collection amount to 0
                $('#collection_sum').html(0);
            });

            function getBillCollectionForPartners() {
                $.ajax({
                    url: "{{URL::to('/collection_sum_for_partners')}}",
                    data:{
                        "daterange": $('#daterange').val(),
                    }
                })
                    .done(function( data ) {
                        console.log(data);
                        $('#total_bill_amount').html(data.total_bill_amount);
                        $('#total_expense').html(data.total_expense);
                        $('#final_amount').html(data.final_amount);
                    });
            }

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

            // Modal Operation
            var $modal, partner_id;
            // Edit Partner
            $('#confirm_edit').on('show.bs.modal', function(e) {
                $modal = $(this);
                partner_id = e.relatedTarget.id;
                $.ajax({
                    cache: false,
                    type: 'GET',
                    url: 'partner/' + partner_id,
                    data: partner_id,
                    success: function(data){
                        console.log(data);
                        $('input#edit_partner_id').val(data.id);
                        $('input#edit_name').val(data.name);
                        $('input#edit_percentage').val(data.percentage);
                        $('input#edit_partner_previous_percentage').val(data.percentage);
                        $('#confirm_delete').modal('toggle');
                    }
                });
            });

            $('#cancel_edit_button').click(function(e){
                partner_id = null;
            });

            // Delete Partner
            $('#confirm_delete').on('show.bs.modal', function(e) {
                $modal = $(this);
                partner_id = e.relatedTarget.id;
            });
            $('#delete_customer').click(function(e){
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'partner/' + partner_id + '/delete',
                    data: partner_id,
                    success: function(data){
                        console.log(data);
                        table.ajax.reload(null, false);
                        $('#confirm_delete').modal('toggle');
                    }
                });
            });
            $('#cancel_delete_button').click(function(e){
                partner_id = null;
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
                    <h3 class="box-title">Bill Collection of Customers</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Bill Amount</span>
                                    <span class="info-box-number banner-font" id="total_bill_amount"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Expense</span>
                                    <span class="info-box-number banner-font" id="total_expense"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Final Amount</span>
                                    <span class="info-box-number banner-font" id="final_amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        {{-- Form starts here --}}
                        {!! Form::open(array('url' => 'create_partner_process', 'id' => 'add_partner_form', )) !!}
                        <div class="col-md-3">
                            <div class="form-group @if ($errors->has('name')) has-error @endif">
                                <label>Partner Name*</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter partner name" tabindex=1 value="{{old('name')}}">
                                @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group @if ($errors->has('percentage')) has-error @endif">
                                <label>Percentage %</label>
                                <input type="number" min="1" max="100" class="form-control" name="percentage" id="percentage" placeholder="Enter percentage" tabindex=2 value="{{old('percentage')}}">
                                @if ($errors->has('percentage')) <p class="help-block">{{ $errors->first('percentage') }}</p> @endif
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>Show</label>
                                <button type="submit" class="form-control btn btn-primary" tabindex=3>Submit</button>
                            </div>
                        </div>
                        {!! Form::close() !!}
                        {{-- Form ends here --}}
                    </div>

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

    {{--All Modals Start--}}

    <!-- Edit Partner Modal -->
    <div class="modal fade" id="confirm_edit" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remove Parmanently</h4>
                </div>
                {{-- Form starts here --}}
                {!! Form::open(array('url' => 'update_partner_process', 'id' => 'update_partner_form', )) !!}
                <div class="modal-body">
                    <div class="form-group @if ($errors->has('name')) has-error @endif">
                        <label>Partner Name*</label>
                        <input type="text" class="form-control" name="name" id="edit_name" placeholder="Enter partner name" value="{{old('name')}}">
                        @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('percentage')) has-error @endif">
                        <label>Percentage %</label>
                        <input type="number" min="1" max="100" class="form-control" name="percentage" id="edit_percentage" placeholder="Enter percentage" value="{{old('percentage')}}">
                        @if ($errors->has('percentage')) <p class="help-block">{{ $errors->first('percentage') }}</p> @endif
                    </div>
                    <input type="hidden" class="form-control" id="edit_partner_id" name="id" >
                    <input type="hidden" class="form-control" id="edit_partner_previous_percentage" name="previous_percentage" >
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="edit_partner">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_edit_button">Cancel</button>
                </div>
                {!! Form::close() !!}
                {{-- Form ends here --}}
            </div>
            <!-- /. Modal content ends here -->
        </div>
    </div>
    <!--  Edit Partner Modal ends here -->

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

    {{--All Modals End--}}

</section>
<!-- /.content -->

@endsection


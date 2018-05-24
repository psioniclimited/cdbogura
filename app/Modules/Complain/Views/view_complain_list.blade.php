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

        // $("edit_complain_status").click(function (e) {
        // $('.edit_complain_status').on('click', function(e) {
        //         console.log("Button Clicked");
        //         console.log(e);
        // });

        $(document).on('click', '.edit_complain_status', function(e) {
            const complain_id = e.currentTarget.id;
            $.ajax({
                cache: false,
                type: 'POST',
                url: '/edit_complain_status/',
                data: {
                    complain_id: complain_id
                },
                success: function(data) {
                    console.log(data);
                    table.ajax.reload();
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            $('#daterange').val(null).trigger("change");
            // Enable the select2 fields
            // Reload datatable
            table.ajax.reload();
        });

        // Show button
        $('#show').on('click', function(e){
            e.preventDefault();
            // Reload datatable
            table.ajax.reload();
            // Collection Count
            $.ajax({
                url: "{{URL::to('/expense_sum')}}",
                data:{
                    "daterange": $('#daterange').val(),
                }
            })
            .done(function( data ) {

            });
        });

        var table = $("#dataTableBuilder").DataTable({
            "serverSide": true,
            "processing": true,
            "stateSave": true,
            "ajax": {
                url: '',
                data: function (data) {
                    data.daterange = $('#daterange').val();
                    data.expense_category = $('#expense_category').val();
                }
            },
            "columns": [
                {
                    "name": "date",
                    "data": "date",
                    "title": "Complain Date",
                    "orderable": true,
                    "searchable": false
                }, {
                    "name": "description",
                    "data": "description",
                    "title": "Complain",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "complain_status.status",
                    "data": "complain_status.status",
                    "title": "Complain Status",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "customer.customer_code",
                    "data": "customer.customer_code",
                    "title": "Customer Code",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "customer.name",
                    "data": "customer.name",
                    "title": "Customer Name",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "customer.phone",
                    "data": "customer.phone",
                    "title": "Customer Phone No.",
                    "orderable": true,
                    "searchable": true
                }, {
                    "name": "Resolve",
                    "data": "Resolve",
                    "orderable": false,
                    "searchable": false
                }, {
                    "name": "action",
                    "data": "action",
                    "orderable": false,
                    "searchable": false
                }],
            search: {
                "regex": true
            },
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
                        <h3 class="box-title">Complain List</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <div class="row">
                            <div class="col-md-4">
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
    </section>
    <!-- /.content -->

@endsection


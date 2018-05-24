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

        var expense_category_select2 = $('#expense_category').select2({
            allowClear: true,
            placeholder: 'Select Expense Category',
            ajax: {
                url: "/get_expense_category",
                dataType: 'json',
                delay: 250,
                tags: true,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;
                    // console.log(data);
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

        // Clear button
        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            // Clear the select2 fields
            $('#daterange').val(null).trigger("change");
            expense_category_select2.val(null).trigger("change");
            // Enable the select2 fields
            // Reload datatable
            table.ajax.reload();
            // Set individual collection amount to 0
            $('#expense_sum').html('');
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
                    "expense_category": $('#expense_category').val(),
                    "daterange": $('#daterange').val(),
                }
            })
            .done(function( data ) {
                // Set amount
                console.log(data);
                $('#expense_sum').html(data);
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
                "name": "posting_transaction_date",
                "data": "posting_transaction_date",
                "title": "Transaction Date",
                "orderable": true,
                "searchable": false
            }, {
                "name": "note",
                "data": "note",
                "title": "Description",
                "orderable": true,
                "searchable": true
            }, {
                "name": "chart_of_accounts.name",
                "data": "name",
                "title": "Expense Category",
                "orderable": true,
                "searchable": true
            }, {
                "name": "postings.debit",
                "data": "debit",
                "title": "Amount",
                "orderable": true,
                "searchable": true
            }, {
                "name": "action",
                "data": "action",
                "title": "Action",
                "orderable": true,
                "searchable": true
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
                    <h3 class="box-title">Expense List</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="info-box bg-green">
                                <span class="info-box-icon"><i class="ion ion-cash"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Expense</span>
                                    <span class="info-box-number" id="expense_sum"></span>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expense Category*</label>
                                <select class="form-control select2" name="expense_category" id="expense_category"></select>
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


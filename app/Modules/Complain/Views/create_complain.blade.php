@extends('master')

@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    {{-- Tooltispter --}}
    <link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
    <!-- Bootstrap datepicker -->
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

@endsection

@section('scripts')
    {{-- Select2 --}}
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    {{-- Validation --}}
    <script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
    {{-- Tooltispter --}}
    <script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
    <!-- Bootstrap datepicker -->
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>

    <script>
        $(document).ready(function () {
            // // initialize tooltipster on form input elements
            // $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
            //     trigger: 'custom', // default is 'hover' which is no good here
            //     onlyOne: false, // allow multiple tips to be open at a time
            //     position: 'right'  // display the tips to the right of the element
            // });
            //
            // // initialize validate plugin on the form
            // $('#create_expense_category_process').validate({
            //     errorPlacement: function (error, element) {
            //
            //         var lastError = $(element).data('lastError'),
            //             newError = $(error).text();
            //
            //         $(element).data('lastError', newError);
            //
            //         if (newError !== '' && newError !== lastError) {
            //             $(element).tooltipster('content', newError);
            //             $(element).tooltipster('show');
            //         }
            //     },
            //     success: function (label, element) {
            //         $(element).tooltipster('hide');
            //     },
            //     rules: {
            //         transaction_date: {required: true},
            //         note: {required: true},
            //         paid_with: {required: true},
            //         expense_category: {required: true},
            //         ref_number: {required: true},
            //         amount: {required: true},
            //     },
            //     messages: {
            //         transaction_date: {required: "Please enter Transaction Date"},
            //         note: {required: "Please Enter Description"},
            //         paid_with: {required: "Please Enter Paid With"},
            //         expense_category: {required: "Please Enter Expense Category"},
            //         ref_number: {required: "Please Enter Reference nnumber"},
            //         amount: {required: "Please Enter Amount"},
            //     }
            // });


            //Last paid date
            $('#date').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true
            });

            $('#customer_id').select2({
                allowClear: true,
                placeholder: 'Select Customer',
                ajax: {
                    url: "/get_customers",
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

        });

    </script>

@endsection

@section('side_menu')

@endsection

@section('content')

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Expenses
            <small>expense creation form</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- <div class="col-md-6"> -->
        <!-- Horizontal Form -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Create Expense</h3>
            </div>

            <!-- /.box-header -->
            <!-- form starts here -->
            {!! Form::open(array('url' => 'create_complain_process', 'id' => 'create_complain_process', 'class' => 'form-horizontal')) !!}
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('date')) has-error @endif">
                        <label>Date*</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="date" name="date" value="{{old('date')}}">
                        </div>
                        @if ($errors->has('date')) <p class="help-block">{{ $errors->first('date') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('description')) has-error @endif">
                        <label>Description*</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Enter Description" value="{{old('description')}}">
                        @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('complain_status')) has-error @endif">
                        <label>Complain Status*</label>
                        <select class="form-control" name="complain_status_id" id="complain_status_id">
                            @foreach($complain_status as $status)
                                <option value="{{$status->id}}">{{$status->status}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('complain_status_id')) <p class="help-block">{{ $errors->first('complain_status_id') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('customer_id')) has-error @endif">
                        <label>Customer*</label>
                        <select class="form-control select2" name="customer_id" id="customer_id" value="{{old('customer_id')}}"></select>
                        @if ($errors->has('customer_id')) <p class="help-block">{{ $errors->first('customer_id') }}</p> @endif
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">Submit</button>
            </div>
            <!-- /.box-footer -->
        {!! Form::close() !!}
        <!-- /.form ends here -->
        </div>
        <!-- /.box -->
        <!-- </div> -->
    </section>
    <!-- /.content -->

@endsection


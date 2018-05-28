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
        // initialize tooltipster on form input elements
        $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
            trigger: 'custom', // default is 'hover' which is no good here
            onlyOne: false, // allow multiple tips to be open at a time
            position: 'right'  // display the tips to the right of the element
        });

        // initialize validate plugin on the form
        $('#create_expense_process').validate({
            errorPlacement: function (error, element) {

                var lastError = $(element).data('lastError'),
                    newError = $(error).text();

                $(element).data('lastError', newError);

                if (newError !== '' && newError !== lastError) {
                    $(element).tooltipster('content', newError);
                    $(element).tooltipster('show');
                }
            },
            success: function (label, element) {
                $(element).tooltipster('hide');
            },
            rules: {
                transaction_date: {required: true},
                note: {required: true},
                paid_with: {required: true},
                expense_category: {required: true},
                ref_number: {required: true},
                amount: {required: true},
            },
            messages: {
                transaction_date: {required: "Please enter Transaction Date"},
                note: {required: "Please Enter Description"},
                paid_with: {required: "Please Enter Paid With"},
                expense_category: {required: "Please Enter Expense Category"},
                ref_number: {required: "Please Enter Reference nnumber"},
                amount: {required: "Please Enter Amount"},
            }
        });


        //Last paid date
        $('#transaction_date').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });

        $('#from_chart_of_accounts').select2({
            allowClear: true,
            placeholder: 'Select Chart of Account',
            ajax: {
                url: "/get_coa_for_money_transfer",
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

        $('#to_chart_of_accounts').select2({
            allowClear: true,
            placeholder: 'Select Chart of Account',
            ajax: {
                url: "/get_coa_for_money_transfer",
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
            Money Transfer
            <small>Form</small>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- <div class="col-md-6"> -->
        <!-- Horizontal Form -->
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Money Transfer</h3>
            </div>

            <!-- /.box-header -->
            <!-- form starts here -->
            {!! Form::open(array('url' => 'money_transfer_process', 'id' => 'money_transfer_process', 'class' => 'form-horizontal')) !!}
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('from_chart_of_accounts')) has-error @endif">
                        <label>From*</label>
                        <select class="form-control select2" name="from_chart_of_accounts" id="from_chart_of_accounts" value="{{old('from_chart_of_accounts')}}"></select>
                        @if ($errors->has('from_chart_of_accounts')) <p class="help-block">{{ $errors->first('from_chart_of_accounts') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('note')) has-error @endif">
                        <label>Description*</label>
                        <input type="text" class="form-control" id="amount" name="note" placeholder="Enter note" value="{{old('note')}}">
                        @if ($errors->has('note')) <p class="help-block">{{ $errors->first('note') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('amount')) has-error @endif">
                        <label>Amount*</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="Enter Amount" value="{{old('amount')}}">
                        @if ($errors->has('amount')) <p class="help-block">{{ $errors->first('amount') }}</p> @endif
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('to_chart_of_accounts')) has-error @endif">
                        <label>To*</label>
                        <select class="form-control select2" name="to_chart_of_accounts" id="to_chart_of_accounts" value="{{old('to_chart_of_accounts')}}"></select>
                        @if ($errors->has('to_chart_of_accounts')) <p class="help-block">{{ $errors->first('to_chart_of_accounts') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('ref_number')) has-error @endif">
                        <label>Ref number*</label>
                        <input type="text" class="form-control" id="ref_number" name="ref_number" placeholder="Enter ref number" value="{{old('ref_number')}}">
                        @if ($errors->has('ref_number')) <p class="help-block">{{ $errors->first('ref_number') }}</p> @endif
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


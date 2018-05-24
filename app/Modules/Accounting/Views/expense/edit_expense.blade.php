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

            $('#expense_category').select2({
                allowClear: true,
                data: [{
                    id: '{{ $posting->chart_of_account->id  }}',
                    text: '{{ $posting->chart_of_account->name }}'
                }],
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
            {!! Form::open(array('url' => 'edit_expense_process', 'id' => 'edit_expense_category_process', 'class' => 'form-horizontal')) !!}
            <input type="hidden" name="id" value="{{ $posting->id }}">
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('transaction_date')) has-error @endif">
                        <label>Date*</label>
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="transaction_date" name="transaction_date" value="{{$posting->transaction_date}}">
                        </div>
                        @if ($errors->has('transaction_date')) <p class="help-block">{{ $errors->first('transaction_date') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('note')) has-error @endif">
                        <label>Description*</label>
                        <input type="text" class="form-control" id="note" name="note" value="{{$posting->journal->note}}">
                        @if ($errors->has('note')) <p class="help-block">{{ $errors->first('note') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('paid_with')) has-error @endif">
                        <label>Paid With*</label>
                        <select class="form-control" name="paid_with" id="paid_with">
                            @foreach($paid_with_options as $paid_with_option)
                                <option value="{{$paid_with_option->id}}">{{$paid_with_option->name}}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('paid_with')) <p class="help-block">{{ $errors->first('paid_with') }}</p> @endif
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-1"></div>
                <div class="col-md-4">
                    <div class="form-group @if ($errors->has('expense_category')) has-error @endif">
                        <label>Expense Category*</label>
                        <select class="form-control select2" name="expense_category" id="expense_category" value="{{old('expense_category')}}"></select>
                        @if ($errors->has('expense_category')) <p class="help-block">{{ $errors->first('expense_category') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('ref_number')) has-error @endif">
                        <label>Reference no*</label>
                        <input type="text" class="form-control" id="ref_number" name="ref_number" placeholder="Enter Reference no" value="{{$posting->journal->ref_number}}">
                        @if ($errors->has('ref_number')) <p class="help-block">{{ $errors->first('ref_number') }}</p> @endif
                    </div>
                    <div class="form-group @if ($errors->has('amount')) has-error @endif">
                        <label>Amount*</label>
                        <input type="number" min="0" class="form-control" id="amount" name="amount" placeholder="Enter Amount" value="{{$posting->debit}}">
                        @if ($errors->has('amount')) <p class="help-block">{{ $errors->first('amount') }}</p> @endif
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


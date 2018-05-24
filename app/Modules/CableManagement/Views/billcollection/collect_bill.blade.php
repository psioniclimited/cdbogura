@extends('master')

@section('css')

{{-- Select2 --}}
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
{{-- Tooltipster --}}
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">

@endsection

@section('scripts')

{{-- Select2 --}}
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
{{-- Validation --}}
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
{{-- Page script --}}
<script>
$(document).ready(function () {
    // initialize tooltipster on form input elements
    $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false, // allow multiple tips to be open at a time
        position: 'right'  // display the tips to the right of the element
    });

    // initialize validate plugin on the form
    $('#collect_bill_form').validate({
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
            customer_code: {required: true},
            last_paid_date_num: {required: true}
        },
        messages: {
            customer_code: {required: "Please select a customer code"},
            last_paid_date_num: {required: "Please enter number of months"}
        }
    });

    // Customer codes
    var customer_code = $('#customer_code');
    customer_code.select2({
        placeholder: "Select a customer code",
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/auto/customercodes",
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

    customer_code.change(function(){
        $(this).valid(); // trigger validation on this element
    });

    //on customer_code change show customer details
    customer_code.change(function(){
        // clear values
        $('p').empty();
        $('input').val('');

        $.ajax({
          method: "GET",
          url: "{{URL::to('/')}}/auto/customerdetails",
          data: { "customer_id": customer_code.val() }
        })
        .done(function( data ) {
            $("#customer_name").text(data.name);
            $("#customer_address").text(data.address);
            $("#customer_phone").text(data.phone);
            $("#customer_connection_start_date").text(data.connection_start_date);
            $("#customer_name").text(data.name);
            $("#customer_due_month").text(data.last_paid);
            $("#customer_monthly_bill").text(data.monthly_bill);
        });
    });


// Customer codes
    var select_customer_name = $('#select_customer_name');
    select_customer_name.select2({
        placeholder: "Select a customer name",
        allowClear: true,
        ajax: {
            dataType: 'json',
            url: "{{URL::to('/')}}/auto/customernames",
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

    //on customer_code change show customer details
    select_customer_name.change(function(){
        // clear values
        $('p').empty();
        $('input').val('');

        $.ajax({
            method: "GET",
            url: "{{URL::to('/')}}/auto/customerdetails",
            data: { "customer_id": select_customer_name.val() }
        })
        .done(function( data ) {
            $("#customer_name").text(data.name);
            $("#customer_address").text(data.address);
            $("#customer_phone").text(data.phone);
            $("#customer_connection_start_date").text(data.connection_start_date);
            $("#customer_name").text(data.name);
            $("#customer_due_month").text(data.last_paid);
            $("#customer_monthly_bill").text(data.monthly_bill);
        });
    });




    // on number of months change show total bill amount
    $("#last_paid_date_num").keyup(function(){
        var displayed_monthly_bill = $("#customer_monthly_bill").html();
        console.log(displayed_monthly_bill);
        var number_of_months = $("#last_paid_date_num").val();
        console.log(number_of_months);
        var total = displayed_monthly_bill * number_of_months  
        $("#customer_total_bill").text(total);
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
        <div class="col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title">Collect Bill</h3>
                        </div>
                        <!-- /.box-header -->
                         {{--Form starts here--}}
                        {!! Form::open(array('url' => 'collect_bill_process', 'id' => 'collect_bill_form', 'class' => 'form-horizontal')) !!}
                        <div class="box-body">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Customer Code*</label>
                                    <select class="form-control select2" name="customer_code" id="customer_code"></select>
                                </div>
                                <div class="form-group">
                                    <label>Customer Name*</label>
                                    <select class="form-control select2" name="name" id="select_customer_name"></select>
                                </div>
                                <div class="form-group">
                                    <label>Number of months*</label>
                                    <input type="number" class="form-control" name="last_paid_date_num" id="last_paid_date_num" placeholder="Enter number of months">
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-2"></div>
                            <div class="col-md-4"></div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">
                            <center><button type="submit" class="btn btn-primary pull-right">Submit</button></center>
                        </div>
                        <!-- /.box-footer -->
                        {!! Form::close() !!}
                         {{--Form ends here--}}
                    </div><!-- /.box -->
                </div><!-- col-xs-12 -->
            </div><!-- row -->
            <div class="row">
                <div class="col-xs-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Collect Bill</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <div class="info-box bg-red">
                                    <label><span class="label-custom">Total Bill:</span></label>
                                    <div class="info-box-content">
                                        <span class="info-box-number banner-font" id="customer_total_bill"></span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </div><!-- row -->
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-solid box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Customer Details</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            {{--<div class="row">--}}
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Customer Name:</strong>
                                    <p class="text-muted" id="customer_name"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Address:</strong>
                                    <p class="text-muted" id="customer_address"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Phone:</strong>
                                    <p class="text-muted" id="customer_phone"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Connection Start Date:</strong>
                                    <p class="text-muted" id="customer_connection_start_date"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Due Month:</strong>
                                    <p class="text-muted" id="customer_due_month"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Monthly Bill:</strong>
                                    <p class="text-muted" id="customer_monthly_bill"></p>
                                </div>
                            </div>
                            {{--</div><!-- row -->--}}

                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!-- /.col-xs-12 -->
            </div><!-- /.row -->
        </div>
    </div>






    {{--<div class="row">--}}
        {{--<div class="col-xs-12">--}}
            {{--<div class="box box-solid box-info">--}}
                {{--<div class="box-header">--}}
                  {{--<h3 class="box-title">Customer Details</h3>--}}
                {{--</div><!-- /.box-header -->--}}
                {{--<div class="box-body">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Customer Name:</strong>--}}
                            {{--<p class="text-muted" id="customer_name"></p>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Address:</strong>--}}
                            {{--<p class="text-muted" id="customer_address"></p>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Phone:</strong>--}}
                            {{--<p class="text-muted" id="customer_phone"></p>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Connection Start Date:</strong>--}}
                            {{--<p class="text-muted" id="customer_connection_start_date"></p>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Due Month:</strong>--}}
                            {{--<p class="text-muted" id="customer_due_month"></p>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<strong>Monthly Bill:</strong>--}}
                            {{--<p class="text-muted" id="customer_monthly_bill"></p>--}}
                        {{--</div>--}}
                    {{--</div><!-- row -->--}}
                    {{--<div class="row">--}}
                        {{--<br><br>--}}
                        {{--<div class="col-sm-2">--}}
                            {{--<div class="form-group">--}}
                                {{--<div class="info-box bg-red">--}}
                                    {{--<label><span class="label-custom">Total Bill:</span></label>--}}
                                    {{--<div class="info-box-content">--}}
                                      {{--<span class="info-box-number banner-font" id="customer_total_bill"></span>--}}
                                    {{--</div>--}}
                                    {{--<!-- /.info-box-content -->--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div><!-- row -->--}}
                {{--</div><!-- /.box-body -->--}}
            {{--</div><!-- /.box -->--}}
        {{--</div><!-- /.col-xs-12 -->--}}
    {{--</div><!-- /.row -->--}}

	{{--<div class="row">--}}
        {{--<div class="col-xs-12">--}}
            {{--<div class="box box-info">--}}
        		{{--<div class="box-header with-border">--}}
        			{{--<h3 class="box-title">Collect Bill</h3>--}}
        		{{--</div>--}}
        		{{--<!-- /.box-header -->--}}
        		 {{--Form starts here--}}
        		{{--{!! Form::open(array('url' => 'collect_bill_process', 'id' => 'collect_bill_form', 'class' => 'form-horizontal')) !!}--}}
        		{{--<div class="box-body">--}}
        			{{--<div class="col-md-4">--}}
        				{{--<div class="form-group">--}}
                            {{--<label>Customer Code*</label>--}}
                            {{--<select class="form-control select2" name="customer_code" id="customer_code"></select>--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
        	                {{--<label>Number of months*</label>--}}
        	                {{--<input type="number" class="form-control" name="last_paid_date_num" id="last_paid_date_num" placeholder="Enter number of months">--}}
        	            {{--</div>--}}
        			{{--</div>--}}
        			{{--<!-- /.col -->--}}
        			{{--<div class="col-md-2"></div>--}}
        			{{--<div class="col-md-4"></div>--}}
        		{{--</div>--}}
        		{{--<!-- /.box-body -->--}}
        		{{--<div class="box-footer">--}}
        	       	{{--<center><button type="submit" class="btn btn-primary pull-right">Submit</button></center>--}}
              	{{--</div>--}}
        		{{--<!-- /.box-footer -->--}}
        		{{--{!! Form::close() !!}--}}
        		 {{--Form ends here--}}
        	{{--</div><!-- /.box -->--}}
        {{--</div><!-- col-xs-12 -->--}}
    {{--</div><!-- row -->--}}

</section>
@endsection



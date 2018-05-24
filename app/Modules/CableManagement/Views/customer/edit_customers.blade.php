@extends('master')

@section('css')

{{-- Validation --}}
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- Bootstrap datepicker -->
<link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">

@endsection

@section('scripts')

{{-- Validation --}}
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
{{-- Select2 --}}
<script src="{{asset('plugins/select2/select2.full.js')}}"></script>
{{-- Utils --}}
{{-- <script src="{{asset('custom/js/utils.js')}}"></script> --}}
<script>
    var init_select = function(parameters){
    console.log("alla");
    var placeholder_text = 'Enter ';
    parameters.selector_id.select2({
        allowClear: true,
        placeholder: placeholder_text + parameters.placeholder,
        ajax: {
            dataType: 'json',
            url: parameters.url,
            delay: 250,
            data: function(params) {
                return {
                    term: params.term,
                    value_term: parameters.value_id.val(),
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
}  

</script>
<!-- Bootstrap datepicker -->
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
{{-- Page script --}}
<script>
$(document).ready(function () {
    //Last paid date
    $('#last_paid').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    //Connection start date
    $('#connection_start_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    // initialize tooltipster on form input elements
    $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false, // allow multiple tips to be open at a time
        position: 'right'  // display the tips to the right of the element
    });

    // initialize validate plugin on the form
    $('#edit_customer_form').validate({
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
            name: {required: true},
            // customer_code: {required: true},
            monthly_bill: {required: true},
            customer_status_id: {required: true},
            subscription_types_id: {required: true},
            territory_id: {required: true},
            sectors_id: {required: true},
            roads_id: {required: true},
            houses_id: {required: true},
            last_paid: {required: true}
        },
        messages: {
            name: {required: "Please enter customer name"},
            // customer_code: {required: "Please enter customer code"},
            monthly_bill: {required: "Please enter monthly bill"},
            customer_status_id: {required: "Please select status"},
            subscription_types_id: {required: "Please select subscription type"},
            territory_id: {required: "Please select territory"},
            sectors_id: {required: "Please select Sector/Moholla/Village"},
            roads_id: {required: "Please select Road/Residential area"},
            houses_id: {required: "Please select House"},
            last_paid: {required: "Please select last paid date"}
        }
    });

    var territory = $('#territory_id'), 
        sector = $('#sectors_id'),
        road = $('#roads_id'),
        house = $('#houses_id');

    $.get( "{{URL::to('/auto/customerdetails')}}", { customer_id: $('#customer_id').val() } ,function( data ) {
        
        sector.select2({
            allowClear: true,    
            data: [
                { id: data.house.road.sector.id, text:  data.house.road.sector.sector }
            ], 
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/sector",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        value_term: territory.val(),
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
      
        road.select2({
            allowClear: true,                
            data: [
                { id: data.house.road.id, text:  data.house.road.road }
            ],
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/road",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        value_term: sector.val(),
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
      
        house.select2({
            allowClear: true,
            data: [
                { id: data.house.id, text:  data.house.house }
            ],
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/house",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        value_term: road.val(),
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
    });

    //on sector change initialize road
    sector.change(function(){
        //clear selected value of road
        road.val(null).trigger("change");
        $(this).valid(); // trigger validation on this element
      
    });

    //on road change initialize house
    road.change(function(){
        //clear selected house value
        house.val(null).trigger("change");
        $(this).valid(); // trigger validation on this element
       
    });

    //on territory change initialize sector
    territory.change(function(){
        //clear selected value of road
        sector.val(null).trigger("change");
        $(this).valid(); // trigger validation on this element
       
    });

    // ..... Modal scripts start here ..... //

    // Add new territory
    $('#submit_territory').click(function(event){    
        event.preventDefault();
        $.post(
            "{{URL::to('/create_territory_process')}}",
            $( "#territory_modal_form" ).serialize(),
            function(data){
                if(data.status == 'success'){
                    //append
                    $('#territory_id')
                    .append($("<option></option>")
                        .attr("value", data.id)
                        .text(data.text));

                    //close the modal
                    $('#territory_modal').modal('toggle');
                        // clear input fields
                    $('input').val('');
                     
                }
            } 
        );
    });

    // Load territory data on sector modal
    $('#sector_modal').on('show.bs.modal', function(){
        $.get( "{{URL::to('/auto/allterritory')}}", function( data ) {
            $('#sector_modal_territory').empty();
            $.each(data, function(index, element){
                $('#sector_modal_territory').append($("<option></option>")
                    .attr("value", element.id)
                    .text(element.text));
            });
        });

    });

    // Add new sector
    $('#submit_sector').click(function(event){
        event.preventDefault();
        $.post(
            "{{URL::to('/create_sector_process')}}",
            $( "#sector_modal_form" ).serialize(),
            function(data){
                if(data.status == 'success'){
                    // clear input fields
                    $('input').val('');
                    //close the modal
                    $('#sector_modal').modal('toggle');
                     
                }
            } 
        );
    });

    // Load territory data
    // Load sector data based on territory data using select2
    $('#road_modal').on('show.bs.modal', function(){
        $.get( "{{URL::to('/auto/allterritory')}}", function( data ) {
            $('#road_modal_territory').empty();
            $.each(data, function(index, element){
                $('#road_modal_territory').append($("<option></option>")
                    .attr("value", element.id)
                    .text(element.text));
            });
        });

        var road_modal_territory = $('#road_modal_territory'),
            road_modal_sector = $('#road_modal_sector');

        // Initialize sector select2 field when modal is opened
        // Set the parameters as an object
        var parameters = {
            placeholder: "Sector/Moholla/Village",
            url: '{{URL::to('/')}}/auto/sector',
            selector_id: road_modal_sector,
            value_id: road_modal_territory
        }
        
        // Pass it as a parameter to init_select
        // Initialize select2 on sector
        init_select(parameters);

        //on territory change initialize sector
        road_modal_territory.change(function(){
            //clear selected value of sector
            road_modal_sector.val(null).trigger("change");
            // Set the parameters as an object
            var parameters = {
                placeholder: "Sector/Moholla/Village",
                url: '{{URL::to('/')}}/auto/sector',
                selector_id: road_modal_sector,
                value_id: road_modal_territory
            }
            
            // Pass it as a parameter to init_select
            // Initialize select2 on sector
            init_select(parameters);
        });
    });

    // Add new road 
    $('#submit_road').click(function(event){
        event.preventDefault();
        $.post(
            "{{URL::to('/create_road_process')}}",
            $( "#road_modal_form" ).serialize(),
            function(data){
                if(data.status == 'success'){
                    //close the modal
                    $('#road_modal').modal('toggle');
                    // clear input fields
                    $('input').val('');
                    // clear select2 field
                    road_modal_sector.val(null).trigger("change");
                     
                }
            } 
        );
    });

    // Load territory data
    // Load sector data based on territory data using select2
    // Load road data based on sector data using select2
    $('#house_modal').on('show.bs.modal', function(){
        $.get( "{{URL::to('/auto/allterritory')}}", function( data ) {
            $('#house_modal_territory').empty();
            $.each(data, function(index, element){
                $('#house_modal_territory')
                    .append($("<option></option>")
                        .attr("value", element.id)
                        .text(element.text));
            });
        });

        var house_modal_territory = $('#house_modal_territory'),
            house_modal_sector = $('#house_modal_sector'),
            house_modal_road = $('#house_modal_road');

        // Initialize sector select2 field when modal is opened
        // Set the parameters as an object
        var parameters = {
            placeholder: "Sector/Moholla/Village",
            url: '{{URL::to('/')}}/auto/sector',
            selector_id: house_modal_sector,
            value_id: house_modal_territory
        }
            
        // Pass it as a parameter to init_select
        // Initialize select2 on sector
        init_select(parameters);

        //on territory change initialize sector
        house_modal_territory.change(function(){
            //clear selected value of road
            house_modal_sector.val(null).trigger("change");
            // Set the parameters as an object
            var parameters = {
                placeholder: "Sector/Moholla/Village",
                url: '{{URL::to('/')}}/auto/sector',
                selector_id: house_modal_sector,
                value_id: house_modal_territory
            }
            
            // Pass it as a parameter to init_select
            // Initialize select2 on sector
            init_select(parameters);
        });

        //on sector change initialize road
        house_modal_sector.change(function(){
            //clear selected value of road
            house_modal_road.val(null).trigger("change");
            // Set the parameters as an object
            var parameters = {
                placeholder: "Road/Residential Area",
                url: '{{URL::to('/')}}/auto/road',
                selector_id: house_modal_road,
                value_id: house_modal_sector
            }
            
            // Pass it as a parameter to init_select
            // Initialize select2 on road
            init_select(parameters);
        });

    });

    // Add new house 
    $('#submit_house').click(function(event){
        event.preventDefault();
        $.post(
            "{{URL::to('/create_house_process')}}",
            $( "#house_modal_form" ).serialize(),
            function(data){
                if(data.status == 'success'){
                    //close the modal
                    $('#house_modal').modal('toggle');
                    // clear input fields
                    $('input').val('');
                    // clear select2 fields
                    house_modal_sector.val(null).trigger("change");
                    house_modal_road.val(null).trigger("change");
                     
                }
            } 
        );
    });
    // ..... Modal scripts end here ..... //

    // ..... On Focus Select2 Dropdown operation Starts ..... //
    var select2_open;
    // open select2 dropdown on focus
    $(document).on('focus', '.select2-selection--single', function(e) {
        select2_open = $(this).parent().parent().siblings('select');
        select2_open.select2('open');
    });
    // ..... On Focus Select2 Dropdown operation Ends ..... //
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
    <!-- <div class="col-md-6"> -->
    <!-- Horizontal Form -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Edit Dish Customer</h3>
        </div>
        <!-- /.box-header -->
        <!-- form starts here -->
        {!! Form::open(array('url' => array('edit_customers_process', $customer->customers_id), 'id' => 'edit_customer_form', 'class' => 'form-horizontal', 'method'=>'PUT')) !!}
        <div class="box-body">
            <div class="col-md-4">
                <div class="form-group @if ($errors->has('name')) has-error @endif">
                    <label>Customer Name*</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter customer Name" value="{{$customer->name}}" tabindex=1>
                    @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                </div>
                {{-- <div class="form-group">
                    <label>Customer Code*</label>
                    <input type="text" class="form-control" name="customer_code" id="customer_code" placeholder="Enter customer code" value="{{$customer->customer_code}}">
                </div> --}}
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter phone" value="{{$customer->phone}}" tabindex=2>
                </div>
                <div class="form-group">
                    <label>Number of Connections</label>
                    <input type="number" class="form-control" name="number_of_connections"  id="number_of_connections" placeholder="Enter number of connections" value="{{$customer->number_of_connections}}" tabindex=3>
                </div>
                <div class="form-group @if ($errors->has('monthly_bill')) has-error @endif">
                    <label>Monthly Bill*</label>
                    <input type="number" class="form-control" name="monthly_bill" id="monthly_bill" placeholder="Enter monthly bill" value="{{$customer->monthly_bill}}" tabindex=4>
                    @if ($errors->has('monthly_bill')) <p class="help-block">{{ $errors->first('monthly_bill') }}</p> @endif
                </div>
                <div class="form-group">
                    <label>Customer Status*</label>
                    <select class="form-control" name="customer_status_id" id="customer_status_id" tabindex=5>
                        @foreach($customer_status as $status)
                            <option value="{{$status->id}}" @if($status->id == $customer->customer_status_id) selected @endif>{{$status->description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Subscription Type*</label>
                    <select class="form-control" name="subscription_types_id" id="subscription_types_id" tabindex=6>
                        @foreach($subscription_types as $subscription_type)
                            <option value="{{$subscription_type->id}}" @if($subscription_type->id == $customer->subscription_types_id) selected @endif>{{$subscription_type->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Territory*</label>
                    <div class="input-group">
                        <select class="form-control" name="territory_id" id="territory_id" tabindex=7>
                            @foreach($territory as $terr)
                                <option value="{{$terr->id}}"@if($terr->id == $customer->territory_id) selected @endif>{{$terr->name}}</option>
                            @endforeach
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-block btn-info btn-flat" data-toggle="modal" data-target="#territory_modal">...</button>
                        </span>
                    </div>
                </div>
                <div class="form-group @if ($errors->has('sectors_id')) has-error @endif">
                    <label>Sector/Moholla/Village*</label>
                    <div class="input-group">
                        <select class="form-control select2" name="sectors_id" id="sectors_id" tabindex=8>
                            
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-block btn-info btn-flat" data-toggle="modal" data-target="#sector_modal">...</button>
                        </span>
                    </div>
                    @if ($errors->has('sectors_id')) <p class="help-block">{{ $errors->first('sectors_id') }}</p> @endif
                </div>
                <div class="form-group @if ($errors->has('roads_id')) has-error @endif">
                    <label>Road/Residential Area*</label>
                    <div class="input-group">
                        <select class="form-control select2" name="roads_id" id="roads_id" tabindex=9>
                            
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-block btn-info btn-flat" data-toggle="modal" data-target="#road_modal">...</button>
                        </span>
                    </div>
                    @if ($errors->has('roads_id')) <p class="help-block">{{ $errors->first('roads_id') }}</p> @endif
                </div>
                <div class="form-group @if ($errors->has('houses_id')) has-error @endif">
                    <label>House*</label>
                    <div class="input-group">
                        <select class="form-control select2" name="houses_id" id="houses_id" tabindex=10>
                            
                        </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn btn-block btn-info btn-flat" data-toggle="modal" data-target="#house_modal">...</button>
                        </span>
                    </div>
                    @if ($errors->has('houses_id')) <p class="help-block">{{ $errors->first('houses_id') }}</p> @endif
                </div>
                <div class="form-group">
                    <label>Flat</label>
                    <input type="text" class="form-control" name="flat" id="flat" placeholder="Enter flat" value="{{$customer->flat}}" tabindex=11>
                </div>
                <div class="form-group @if ($errors->has('last_paid')) has-error @endif">
                    <label>Due on*</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" id="last_paid" name="last_paid" value="{{$last_paid}}" tabindex=12>
                    </div>
                    @if ($errors->has('last_paid')) <p class="help-block">{{ $errors->first('last_paid') }}</p> @endif
                </div>
                <div class="form-group">
                    <label>Connection Start Date</label>
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control pull-right" id="connection_start_date" name="connection_start_date" value="{{$connection_start_date}}" tabindex=13>
                    </div>
                </div>
            </div>
            <input type="hidden" id="customer_id" name="customer_id" value="{{$customer->customers_id}}" >
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right" tabindex=14>Submit</button>
        </div>
        <!-- /.box-footer -->
        {!! Form::close() !!}
        <!-- /.form ends here -->
    </div>
    <!-- /.box -->

    <!-- All Modals -->
    <!-- Form for Add New Territory Modal  -->
    {!! Form::open(array('url' => 'create_territory_process', 'id' => 'territory_modal_form')) !!}
        <!-- Add New Territory Modal -->
        <div class="modal fade" id="territory_modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add New Territory</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="territory_modal">Territory Name</label>
                            <input type="text" name="territory_modal" class="form-control" id="territory_modal" placeholder="Territory Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_territory" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /. Modal content ends here -->
            </div>
        </div>
        <!--  Add New Territory Modal ends here -->
    {!! Form::close() !!}
    <!-- /.  Form for Add New Territory Modal ends here -->

    <!-- Form for Add New Sector Modal -->
    {!! Form::open(array('url' => 'create_sector_process', 'id' => 'sector_modal_form')) !!}
        <!-- Add New Sector Modal -->
        <div class="modal fade" id="sector_modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add New Sector/Moholla/Village</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Territory</label>
                            <select name="sector_modal_territory" id="sector_modal_territory" class="form-control">
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="sector_modal">Sector/Moholla/Village Name</label>
                            <input type="text" name="sector_modal" class="form-control" id="sector_modal" placeholder="Sector/Moholla/Village Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_sector" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /. Modal content ends here -->
            </div>
        </div>
        <!--  Add New Sector Modal ends here -->
    {!! Form::close() !!}
    <!-- /.  Form for Add New Sector Modal ends here -->

    <!-- Form for Add New Road Modal  -->
    {!! Form::open(array('url' => 'create_road_process', 'id' => 'road_modal_form')) !!}
        <!-- Add New Road Modal  -->
        <div class="modal fade" id="road_modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add New Road/Residential Area</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Territory</label>
                            <select name="road_modal_territory" id="road_modal_territory" class="form-control">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sector/Moholla/Village</label>
                            <select name="road_modal_sector" class="form-control select2" id="road_modal_sector">
                              
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="road_modal">Road/Residential Area Name</label>
                            <input type="text" name="road_modal" class="form-control" id="road_modal" placeholder="Road/Residential Area Name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_road" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /. Modal content ends here -->
            </div>
        </div>
        <!--  Add New Road Modal ends here -->
    {!! Form::close() !!}
    <!-- /.  Form for Add New Road Modal ends here -->
    <!-- Form for Add New House Modal  -->
    {!! Form::open(array('url' => 'create_house_process', 'id' => 'house_modal_form')) !!}
        <!-- Add New House Modal -->
        <div class="modal fade" id="house_modal" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add New House</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Territory</label>
                            <select name="house_modal_territory" id="house_modal_territory" class="form-control">
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sector/Moholla/Village</label>
                            <select name="house_modal_sector" class="form-control select2" id="house_modal_sector">
                              
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Road/Residential Area</label>
                            <select name="house_modal_road" class="form-control select2" id="house_modal_road">
                              
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="house_modal">House Name</label>
                            <input type="text" name="house_modal" class="form-control" id="house_modal" placeholder="House Name">
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit_house" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /. Modal content ends here -->
            </div>
        </div>
        <!--  Add New House Modal ends here -->
    {!! Form::close() !!}
    <!-- /.  Form for Add New House Modal ends here -->
    <!-- All Modals end here -->
</section>
<!-- /.content -->

@endsection


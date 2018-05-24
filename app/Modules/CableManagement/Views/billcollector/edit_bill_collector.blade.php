@extends('master')

@section('css')
{{-- Select2 --}}
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
{{-- Tooltispter --}}
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
@endsection

@section('scripts')
{{-- Select2 --}}
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
{{-- Validation --}}
<script src="{{asset('plugins/validation/dist/jquery.validate.js')}}"></script>
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
<script>

$(document).ready(function () {

    // initialize tooltipster on form input elements
    $('form input, select').tooltipster({// <-  USE THE PROPER SELECTOR FOR YOUR INPUTs
        trigger: 'custom', // default is 'hover' which is no good here
        onlyOne: false, // allow multiple tips to be open at a time
        position: 'right'  // display the tips to the right of the element
    });

    // initialize validate plugin on the form
    $('#edit_bill_collector_form').validate({
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
            name: {required: true, minlength: 4},
            email: {required: true, email: true},
            territory: {required: true},
            'sector[]': {required: true},
            upassword: {required: false, minlength: 6},
            upassword_re: {required: false, equalTo: "#upassword"}
        },
        messages: {
            name: {required: "Please enter fullname"},
            email: {required: "Please enter email address"},
            territory: {required: "Please select territory"},
            'sector[]': {required: "Please select sector"},
            upassword: {required: "Six digit password"},
            upassword_re: {required: "Re-enter same password"}
        }
    });

    var sector = $('#sector'),
        territory = $('#territory');

    $.get( "{{URL::to('/auto/billcollectordetails')}}", { bill_collector_id: $('#bill_collector_id').val() }, function( data ) {
        var sector_data = [];
        var sector_data_id = [];
        if(data.sectors.length > 0) {
            for( var i = 0; i < data.sectors.length; i++) {
                item = { };
                item['id'] = data.sectors[i].id;
                item['text'] = data.sectors[i].sector;
                sector_data.push(item);
                // To display multiple values on select2 field
                sector_data_id.push(data.sectors[i].id);
            }
            console.log(sector_data);
        }
        sector.select2({
            multiple: true,
            allowClear: true,    
            data: sector_data, 
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

        sector.val(sector_data_id);
        sector.trigger('change.select2');
    });

    //on territory change initialize sector
    territory.change(function(){
        //clear selected value of road
        sector.val(null).trigger("change");
    });

    //on sector change trigger validation
    sector.change(function(){
        $(this).valid(); // trigger validation on this element
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
        Bill Collectors
        <small>bill collector edit form</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">User</a></li>
        <li class="active">Edit Users</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <!-- <div class="col-md-6"> -->
    <!-- Horizontal Form -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Bill Collector Edit Page</h3>
        </div>
        <!-- /.box-header -->
        <!-- form starts here -->
        {!! Form::open(array('url' => array('edit_bill_collector_process', $bill_collector->id), 'id' => 'edit_bill_collector_form', 'class' => 'form-horizontal', 'method'=>'PUT')) !!}
        <div class="box-body">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Fullname*</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter fullname" value="{{$bill_collector->name}}">
                </div>
                <div class="form-group">
                    <label>Email*</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{$bill_collector->email}}">
                </div>
                <div class="form-group">
                    <label>Territory*</label>
                    <select name="territory" id="territory" class="form-control">
                        @foreach($territory as $terr)
                            <option value="{{$terr->id}}" @if($terr->id == $bill_collector->territory_id) selected @endif>{{$terr->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- /.col -->
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Sector*</label>
                    <select name="sector[]" id="sector" class="form-control select2">
                        
                    </select>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="upassword" name="upassword" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" class="form-control" id="upassword_re" name="upassword_re" placeholder="Enter password again">
                </div>
            </div>
            <input type="hidden" name="bill_collector_id" id="bill_collector_id" value="{{$bill_collector->id}}">
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-default">Cancel</button>
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


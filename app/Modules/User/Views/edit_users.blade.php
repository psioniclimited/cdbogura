@extends('master')

@section('css')
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
@endsection

@section('scripts')
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
    $('#edit_user_form').validate({
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
            fullname: {required: true, minlength: 4},
            uemail: {required: true, email: true},
            upassword: {required: false, minlength: 6},
            upassword_re: {required: false, equalTo: "#upassword"},
            uroles: {required: true}
        },
        messages: {
            fullname: {required: "Please give fullname"},
            uemail: {required: "Insert email address"},
            upassword: {required: "Six digit password"},
            upassword_re: {required: "Re-enter same password"},
            uroles: {required: "Please select a role"}
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
        User Module
        <small>it all starts here</small>
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
            <h3 class="box-title">User Edit Page</h3>
        </div>
        <!-- /.box-header -->
        <!-- form starts here -->
        @if(isset($editUser))
        {!! Form::open(array('url' => 'edit_users_process', 'id' => 'edit_user_form', 'class' => 'form-horizontal')) !!}
        @foreach($editUser as $eusr)
        <div class="box-body">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fullname" class="control-label">Fullname*</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter fullname" value="{{$eusr->name}}">
                </div>
                <div class="form-group">
                    <label for="uemail" class="control-label">Email*</label>
                    <input type="email" class="form-control" id="uemail" name="uemail" placeholder="Enter email" value="{{$eusr->email}}">
                </div>
                <div class="form-group">
                    <label for="uroles" class="control-label">Role*</label>
                    <select class="form-control" name="uroles">
                        <option value="">Select Role</option>
                        @foreach($getRoles as $grole)
                            <option value="{{$grole->id}}" @if($grole->id == $eusr->roles[0]->id) selected @endif>{{$grole->display_name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <!-- /.col -->
            <div class="col-md-2"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Territory</label>
                    <select name="territory" id="territory" class="form-control">
                        @foreach($territory as $terr)
                            <option value="{{$terr->id}}" @if($terr->id == $eusr->territory_id) selected @endif>{{$terr->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="upassword" class="control-label">Password*</label>
                    <input type="password" class="form-control" id="upassword" name="upassword" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label for="upassword_re" class="control-label">Confirm Password*</label>
                    <input type="password" class="form-control" id="upassword_re" name="upassword_re" placeholder="Enter password again">
                </div>
            </div>
            <input type="hidden" name="uid" value="{{$eusr->id}}">
        </div>
        @endforeach
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">Submit</button>
        </div>
        <!-- /.box-footer -->
        {!! Form::close() !!}
        @endif
        <!-- /.form ends here -->
    </div>
    <!-- /.box -->
    <!-- </div> -->
</section>
<!-- /.content -->

@endsection


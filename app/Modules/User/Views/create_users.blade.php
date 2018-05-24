@extends('master')

@section('css')
{{-- Tooltipster --}}
<link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
@endsection

@section('scripts')
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
    $('#add_user_form').validate({
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
            upassword: {required: true, minlength: 6},
            upassword_re: {required: true, equalTo: "#upassword"},
            uroles: {required: true}
        },
        messages: {
            fullname: {required: "Please enter fullname"},
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
        Users
        <small>user creation form</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <!-- <div class="col-md-6"> -->
    <!-- Horizontal Form -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Create User</h3>
        </div>
        <!-- /.box-header -->
        <!-- form starts here -->
        {!! Form::open(array('url' => 'create_users_process', 'id' => 'add_user_form', 'class' => 'form-horizontal')) !!}

        <div class="box-body">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="fullname">Fullname*</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter fullname">
                </div>
                <div class="form-group">
                    <label for="uemail">Email*</label>
                    <input type="email" class="form-control" id="uemail" name="uemail" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="uroles">Role*</label>
                    <select class="form-control" name="uroles" >
                        <option value="">Select Role</option>
                        @foreach($getRoles as $grole)
                        <option value="{{$grole->id}}">{{$grole->display_name}}</option>
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
                            <option value="{{$terr->id}}">{{$terr->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="upassword">Password*</label>
                    <input type="password" class="form-control" id="upassword" name="upassword" placeholder="Enter password">
                </div>
                <div class="form-group">
                    <label for="upassword_re">Confirm Password*</label>
                    <input type="password" class="form-control" id="upassword_re" name="upassword_re" placeholder="Enter password again">
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


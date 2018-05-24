@extends('master')

@section('css')
    {{-- Select2 --}}
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    {{-- Tooltispter --}}
    <link rel="stylesheet" href="{{asset('plugins/tooltipster/tooltipster.css')}}">
    <!-- Bootstrap datepicker -->
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <!-- iCheck 1.0.1 -->
    <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">z
@endsection

@section('scripts')
    {{-- Select2 --}}
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    {{-- Tooltispter --}}
    <script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
    <!-- Bootstrap datepicker -->
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    {{-- Datatable --}}
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
    <script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>


    <script>
        $(document).ready(function () {
            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            //Red color scheme for iCheck
            $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });
            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            // Edit Chart of Accounts
            var chart_of_accounts_id;
            $('#edit_chart_of_accounts_modal').on('show.bs.modal', function(e) {
                var $modal = $(this);
                chart_of_accounts_id = e.relatedTarget.id;
                $.ajax({
                    cache: false,
                    type: 'GET',
                    url: '/chart_of_accounts/' + chart_of_accounts_id + '/edit',
                    data: chart_of_accounts_id,
                    success: function(data) {
                        $('input#edit_name').val(data.name);
                        $('input#edit_description').val(data.description);
                        if(data.is_payment_account === 1) {
                            $('#edit_is_payment_account').parent().attr({
                                'class': 'icheckbox_flat-green checked',
                                'aria-checked': true
                            });
                        } else {
                            $('#edit_is_payment_account').parent().attr({
                                'class': 'icheckbox_flat-green',
                                'aria-checked': false
                            });
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            });
            $('#btn_update_chart_of_accounts').click(function(e) {
                var form_data = $("#edit_chart_of_accounts_form").serializeArray();
                // console.log(form_data);
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: '/chart_of_accounts_update/' + chart_of_accounts_id,
                    data: form_data,
                    success: function(data) {
                        location.reload();
                    }
                });
            });
            $('#cancel_edit_chart_of_accounts_modal').click(function(e){
                chart_of_accounts_id = null;
            });

            // Delete Customer
            var chart_of_account_id;
            $('#confirm_delete').on('show.bs.modal', function(e) {
                var $modal = $(this);
                chart_of_account_id = e.relatedTarget.id;
                console.log(chart_of_account_id);
            });
            $('#cancel_delete_button').click(function(e){
                chart_of_account_id = null;
                console.log(chart_of_account_id);
            });
            $('#delete_chart_of_account').click(function(e){
                event.preventDefault();
                console.log(chart_of_account_id);
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'chart_of_account/' + chart_of_account_id + '/delete',
                    data: chart_of_account_id,
                    success: function(data) {
                        console.log(data);
                        location.reload();
                        $('#confirm_delete').modal('toggle');
                    }
                });
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
        Chart of accounts
        <small>Create Chart of Accounts operation</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Create Chart of Accounts</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            @foreach ($chartOfAccountsList as $chartOfAccounts)
                <h3>{{  $chartOfAccounts->name }}</h3>
                {!! Form::open(array('url' => 'create_chart_of_account_process', 'method' => 'post', 'id' => 'add_chart_of_accounts_form')) !!}
                    <input type="text" placeholder="Enter chart of account" name="name">
                    <input type="text" placeholder="Enter description" name="description">
                    <label>
                        <input type="checkbox" class="flat-red" name="is_payment_account" value="1">
                        Is payment account
                    </label>
                    <input hidden type="number" value="{{ $chartOfAccounts->id }}" name="parent_accounts_id">
                    <button type="submit" class="btn btn-xs btn-info">Add</button>
                {!! Form::close() !!}
                <ul>
                @foreach ($chartOfAccounts->chartOfAccounts as $chartOfAccount  )
                    <li>
                        {{$chartOfAccount->name}}
                        {{--<button class="btn btn-xs btn-primary">Edit</button>--}}
                        <a class="btn btn-xs btn-primary edit_complain_status" id="{{$chartOfAccount->id}}" data-toggle="modal" data-target="#edit_chart_of_accounts_modal">
                            <i class="glyphicon glyphicon-edit"></i> Edit
                        </a>
                        {{--<button class="btn btn-xs btn-danger">Delete</button>--}}
                        <a class="btn btn-xs btn-danger" id="{{$chartOfAccount->id}}"
                           data-toggle="modal" data-target="#confirm_delete">
                            <i class="glyphicon glyphicon-trash"></i> Delete
                        </a>
                    </li>
                @endforeach
                </ul>
            @endforeach
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- </div> -->

    <!-- Edit Chart of Accounts Modal -->
    <div class="modal fade" id="edit_chart_of_accounts_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content Start -->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Chart of Accounts</h4>
                </div>
                {!! Form::open(array('url' => '','method' => 'post', 'id' => 'edit_chart_of_accounts_form')) !!}
                <div class="modal-body">
                    <div class="form-group edit_name_modal">
                        <label>Name*</label>
                        <input type="text" class="form-control" id="edit_name" name="name" >
                    </div>
                    <div class="form-group edit_description_modal">
                        <label>Description*</label>
                        <input type="text" class="form-control" id="edit_description" name="description">
                    </div>
                    <label>
                        <input type="checkbox" class="flat-red" id="edit_is_payment_account" name="is_payment_account">
                        Is payment account
                    </label>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn_update_chart_of_accounts">Submit</button>
                    <button type="button" class="btn btn-default" id="cancel_edit_chart_of_accounts_modal" data-dismiss="modal">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /. Modal content ends here -->
        </div>
    </div>
    <!--  Edit Chart of Accounts Modal ends here -->

    <!-- Delete Customer Modal -->
    <div class="modal fade" id="confirm_delete" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Remove Parmanently</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure about this ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="delete_chart_of_account">Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_delete_button">Cancel</button>
                </div>
            </div>
            <!-- /. Modal content ends here -->
        </div>
    </div>
    <!--  Delete Customer Modal ends here -->

</section>
<!-- /.content -->

@endsection


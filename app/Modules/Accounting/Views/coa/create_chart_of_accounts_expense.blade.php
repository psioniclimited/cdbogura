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
{{-- Tooltispter --}}
<script src="{{asset('plugins/tooltipster/tooltipster.js')}}"></script>
<!-- Bootstrap datepicker -->
<script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
{{-- Datatable --}}
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="https://cdn.datatables.net/buttons/1.0.3/js/dataTables.buttons.min.js"></script>
<script src="{{asset('vendor/datatables/buttons.server-side.js')}}"></script>

<script>
$(document).ready(function () {
    var table = $('#chart_of_accounts_list').DataTable({
        "paging": false,
        // "pageLength": 50,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "ajax": "{{URL::to('/chart_of_accounts_list')}}",
        "columns": [
            {"data": "name"},
            {"data": "description"},
            {"data": "Link", name: 'link', orderable: false, searchable: false}
        ]
    });

    // Add Chart of Accounts
    var chart_of_accounts_id = null;
    $('#add_chart_of_accounts_modal').on('show.bs.modal', function(e) {
        var $modal = $(this);
    });
    $('#btn_add_chart_of_accounts').click(function (e) {
        var form_data = $("#add_chart_of_accounts_form").serializeArray();
        // console.log(form_data);
        e.preventDefault();
        $.ajax({
            cache: false,
            type: 'POST',
            url: '/chart_of_accounts_add_expense/',
            data: form_data,
            success: function (data) {
                console.log("Added Successfully");
                console.log(data);
                table.ajax.reload(null, false);
                add_expense_remove_errors_adding();
                $("#add_chart_of_accounts_form").trigger('reset');
                $('#add_chart_of_accounts_modal').modal('toggle');
            },
            error: function (data) {
                // Render the errors with js ...
                var errors = data.responseJSON;
                console.log(errors);
                add_expense_error_removing(errors);
            }
        });
    });

    $('#cancel_add_chart_of_accounts_modal').click(function (e) {
        chart_of_accounts_id = null;
    });

    // Edit Chart of Accounts
    $('#edit_chart_of_accounts_modal').on('show.bs.modal', function(e) {
        var $modal = $(this);
        let chart_of_accounts_id = e.relatedTarget.id;
        $.ajax({
            cache: false,
            type: 'GET',
            url: '/chart_of_accounts_expense/' + chart_of_accounts_id + '/edit',
            data: {
                chart_of_accounts_id
            },
            success: function(data) {
                // console.log(data);
                $('input#edit_id').val(data.id);
                $('input#edit_name').val(data.name);
                $('input#edit_description').val(data.description);
            },
            error: function(data) {
                console.log(data);
            }
        });
    });

    $('#btn_update_chart_of_accounts').click(function(e){
        var form_data = $("#edit_chart_of_accounts_form").serializeArray();
        // console.log(form_data);
        event.preventDefault();
        $.ajax({
            cache: false,
            type: 'POST',
            url: '/chart_of_accounts_update_expense/',
            data: form_data,
            success: function(data) {
                // console.log("Update Successfully");
                // console.log(data);
                if($( ".edit_name_modal" ).hasClass( "has-error" )){
                    $( ".edit_name_modal" ).removeClass( "has-error" );
                    $( ".edit_name_modal_error" ).remove();
                }
                if($( ".edit_description_modal" ).hasClass( "has-error" )){
                    $( ".edit_description_modal" ).removeClass( "has-error" );
                    $( ".edit_description_modal_error" ).remove();
                }
                table.ajax.reload(null, false);
                $("#edit_chart_of_accounts_form").trigger('reset');
                $('#edit_chart_of_accounts_modal').modal('toggle');
            },
            error: function(data) {
                var errors = data.responseJSON;
                console.log(errors);
                if(errors['name']) {
                    if(!$( ".edit_name_modal" ).hasClass( "has-error" )){
                        $( ".edit_name_modal" ).addClass( "has-error" );
                        $( "input#edit_name" ).after( '<p class=\"help-block edit_name_modal_error\">'+errors['name']+'</p>' );
                    }
                }
                else {
                    $( ".edit_name_modal" ).removeClass( "has-error" );
                    $( ".edit_name_modal_error" ).remove();
                }
                if(errors['description']) {
                    if(!$( ".edit_description_modal" ).hasClass( "has-error" )){
                        $( ".edit_description_modal" ).addClass( "has-error" );
                        $( "input#edit_description" ).after( '<p class=\"help-block add_description_modal_error\">'+errors['description']+'</p>' );
                    }
                }
                else {
                    $( ".edit_description_modal" ).removeClass( "has-error" );
                    $( ".edit_description_modal_error" ).remove();
                }
            }
        });
    });
    $('#cancel_edit_chart_of_accounts_modal').click(function(e){
        chart_of_accounts_id = null;
    });



    function add_expense_remove_errors_adding() {
        if ($(".add_name_modal").hasClass("has-error")) {
            $(".add_name_modal").removeClass("has-error");
            $(".add_name_modal_error").remove();
        }
        if ($(".add_description_modal").hasClass("has-error")) {
            $(".add_description_modal").removeClass("has-error");
            $(".add_description_modal_error").remove();
        }
    }
    function add_expense_error_removing(errors) {
        if (errors['name']) {
            if (!$(".add_name_modal").hasClass("has-error")) {
                $(".add_name_modal").addClass("has-error");
                $("input#add_name").after('<p class=\"help-block add_name_modal_error\">' + errors['name'] + '</p>');
            }
        }
        else {
            $(".add_name_modal").removeClass("has-error");
            $(".add_name_modal_error").remove();
        }
        if (errors['description']) {
            if (!$(".add_description_modal").hasClass("has-error")) {
                $(".add_description_modal").addClass("has-error");
                $("input#add_description").after('<p class=\"help-block add_description_modal_error\">' + errors['description'] + '</p>');
            }
        }
        else {
            $(".add_description_modal").removeClass("has-error");
            $(".add_description_modal_error").remove();
        }
    }




});

</script>

@endsection

@section('side_menu')

@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Expense Category
        <small>Expense Category Operation</small>
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Expense Category</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="col-md-6">
                <button type="button" class="btn btn-primary margin" data-toggle="modal" data-target="#add_chart_of_accounts_modal">ADD Expense</button>
                <table id="chart_of_accounts_list" class="table dataTable no-footer">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- user list -->
                    </tbody>
                </table>
            </div>
            <!-- /.col -->
            <div class="col-md-6">

            </div>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
    <!-- </div> -->

    <!-- Add Chart of Accounts Modal -->
    <div class="modal fade" id="add_chart_of_accounts_modal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content Start -->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Chart of Accounts</h4>
                </div>
                {!! Form::open(array('url' => '', 'method' => 'post', 'id' => 'add_chart_of_accounts_form')) !!}
                <div class="modal-body">
                    <div class="form-group add_name_modal">
                        <label>Name*</label>
                        <input type="text" class="form-control" id="add_name" name="name" placeholder="Enter Name" >
                    </div>
                    <div class="form-group add_description_modal">
                        <label>Description*</label>
                        <input type="text" class="form-control" id="add_description" name="description" placeholder="Enter Description">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="btn_add_chart_of_accounts">Submit</button>
                    <button type="button" class="btn btn-default" id="cancel_add_chart_of_accounts_modal" data-dismiss="modal">Cancel</button>
                </div>
                {!! Form::close() !!}
            </div>
            <!-- /. Modal content ends here -->
        </div>
    </div>
    <!--  Add Chart of Accounts Modal ends here -->


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
                    <input type="hidden" class="form-control" id="edit_id" name="id" >
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





</section>
<!-- /.content -->

@endsection


@extends('master')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('scripts')
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
    $(document).ready(function () {  

        // View Users
        var table = $('#all_user_list').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "stateSave": true,
            "ajax": "{{URL::to('/getusers')}}",
            "columns": [
                    {"data": "name", "name": "users.name"},
                    {"data": "email", "name": "users.email"},                    
                    {"data": "display_name", "name": "roles.display_name"},                    
                    {"data": "territory_name", "name": "territory.name"},                    
                    {"data": "Link", name: 'link', orderable: false, searchable: false}
                ],
            "order": [[0, 'asc']]
        });

        // Delete User
        $('#confirm_delete').on('show.bs.modal', function(e) {
            var $modal = $(this),
                user_id = e.relatedTarget.id;

            $('#cancel_delete_button').click(function(e){
                user_id = null;
            });

            $('#delete_user').click(function(e){    
                event.preventDefault();
                $.ajax({
                    cache: false,
                    type: 'POST',
                    url: 'users/' + user_id + '/delete',
                    data: user_id,
                    success: function(data){
                        table.ajax.reload(null, false);
                        $('#confirm_delete').modal('toggle');
                    }
                });
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
    
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">            

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">User list</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body table-responsive">
                    <table id="all_user_list" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Territory</th>
                                
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                            <!-- user list -->
                        </tbody>                        
                    </table>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <!-- Delete User Modal -->
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
                    <button type="button" class="btn btn-danger" id="delete_user">Delete</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_delete_button">Cancel</button>
                </div>
            </div>
            <!-- /. Modal content ends here -->
        </div>
    </div>
    <!--  Delete User Modal ends here -->
</section>
<!-- /.content -->
@endsection


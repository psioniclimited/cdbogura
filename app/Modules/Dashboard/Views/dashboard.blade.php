@extends('master')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@endsection

@section('scripts')
<!-- ChartJS 1.0.1 -->
<script src="{{asset('plugins/chartjs/Chart.min.js')}}"></script>
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<!-- Page Script -->
<script>
  $(document).ready(function () {
    // Customer list
    // $('#all_customer_list').DataTable({
    //     "paging": true,
    //     "lengthChange": false,
    //     "searching": true,
    //     "ordering": true,
    //     "info": true,
    //     "autoWidth": false,
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": "{{URL::to('/getcustomers')}}",
    //     "columns": [
    //             {"data": "customers_id"},
    //             {"data": "customer_code"},
    //             {"data": "name"},
    //             {"data": "phone"},                    
    //             {"data": "number_of_connections"},                    
    //             {"data": "monthly_bill"},                    
    //             {"data": "Link", name: 'link', orderable: false, searchable: false}
    //     ],
    //     "order": [[0, 'asc']]
    // });

    // Collection list
    // $('#all_collection_list').DataTable({
    //   "paging": true,
    //   "lengthChange": false,
    //   "searching": true,
    //   "ordering": true,
    //   "info": true,
    //   "autoWidth": false,
    //   "processing": true,
    //   "serverSide": true,
    //   "ajax": "{{URL::to('/getcollectionlist')}}",
    //   "columns": [
    //               {"data": "id"},                    
    //               {"data": "customers_id"},                    
    //               {"data": "created_at"},                    
    //               {"data": "total"}                    
                  
    //   ],
    //   "order": [[0, 'asc']]
    // });

    
  });

 
  
</script>
@endsection

@section('side_menu')

@endsection


@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>Dashboard</h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-yellow"><a href="{{url('allbillcollectors')}}"><i class="ion ion-ios-people-outline"></i></a></span>
            <div class="info-box-content">
              <span class="info-box-text">Collectors</span>
              <span class="info-box-number">{{$collector_count}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-black"><a href="{{url('allcustomers')}}"><i class="ion ion-ios-people-outline"></i></a></span>
            <div class="info-box-content">
              <span class="info-box-text">Subscribers</span>
              <span class="info-box-number">{{$customer_count}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-aqua"><a href="#"><i class="ion ion-ios-people-outline"></i></a></span>
            <div class="info-box-content">
              <span class="info-box-text">Total due</span>
              <span class="info-box-number">{{$total_due}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

      <!-- Main row -->
      <div class="row">
        <!-- Left col -->
        {{-- <div class="col-md-9">
          <!-- TABLE: LATEST COLLECTION -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Collection List</h3>
              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                <table id="all_collection_list" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>#</th>                                
                      <th>Customer ID</th>                                
                      <th>Timestamp</th>                                
                      <th>Total</th>                                
                    </tr>
                  </thead>
                  <tbody>                            
                      <!-- collection list -->
                  </tbody>                        
                </table>
                
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
             
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div> --}}
        <!-- /.col -->

        {{-- <div class="col-md-3">
          <div class="info-box bg-red">
            <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Daily Collection Count</span>
              <span class="info-box-number">{{$daily_collection_count}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="ion ion-ios-pricetag-outline"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Due Count</span>
              <span class="info-box-number">{{$total_due_count}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          /.info-box
          <div class="info-box bg-green">
            <span class="info-box-icon"><i class="ion ion-cash"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Daily Collection Amount</span>
              <span class="info-box-number">{{$daily_collection_amount}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
          <div class="info-box bg-yellow">
            <span class="info-box-icon"><i class="ion ion-cash"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Collection Amount</span>
              <span class="info-box-number">{{$total_collection_amount}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div> --}}
        <!-- /.col -->
      </div>
      <!-- /.row -->


       <!-- Main row -->
      {{-- <div class="row">
        <!-- Left col -->
        <div class="col-md-9">
          
          <!-- TABLE: LATEST ORDERS -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Subscribers List</h3>

              <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                </button>
                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <div class="table-responsive">
                 <table id="all_customer_list" class="table table-bordered table-striped">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>Customer Code</th>
                              <th>Name</th>
                              <th>Flat</th>
                              <th>Number of connections</th>
                              <th>Monthly bill</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>                            
                          <!-- subscriber list -->
                      </tbody>                        
                  </table>
              </div>
              <!-- /.table-responsive -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
              
            </div>
            <!-- /.box-footer -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->

        <div class="col-md-3">
        
         
        </div>
        <!-- /.col -->
        
      </div> --}}
      <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection
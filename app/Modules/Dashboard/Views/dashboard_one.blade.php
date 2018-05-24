@extends('master')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@endsection

@section('scripts')
<!-- ChartJS 1.0.1 -->
<!-- <script src="{{asset('plugins/chartjs/Chart.min.js')}}"></script> -->
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>
{{-- Select2 --}}
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
{{-- Utils --}}
<script src="{{asset('custom/js/utils.js')}}"></script>
<!-- Page Script -->
<script>
$(document).ready(function () {



});
</script>
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
            <span class="info-box-icon bg-black"><i class="ion ion-ios-people-outline"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Subscribers</span>
              <span class="banner-font">{{$customer_count}}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-green"><i class="ion ion-cash"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">This Month Collection </span>
              <span class="banner-font">{{$collection_of_this_month}} /-</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="ion ion-cash"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Dish Due</span>
              <span class="banner-font">{{$totalDishDue}} /-</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-md-3 col-sm-6 col-xs-12">
          <div class="info-box">
            <span class="info-box-icon bg-red"><i class="ion ion-cash"></i>	</span>
            <div class="info-box-content">
              <span class="info-box-text">Total Internet Due</span>
              <span class="banner-font">{{$totalInternetDue}} /-</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->



	
	


	</section>
    <!-- /.content -->
@endsection
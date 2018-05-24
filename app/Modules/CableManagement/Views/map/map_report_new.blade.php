0
@extends('master')

@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
<!-- daterange picker -->
<link rel="stylesheet" href="{{asset('plugins/daterangepicker/daterangepicker.css')}}">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">

@endsection

@section('scripts')
<!-- DataTables -->
<script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
{{-- Date Range Picker --}}
<script src="{{asset('plugins/moment/moment.min.js')}}"></script>
<script src="{{asset('plugins/daterangepicker/daterangepicker.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('plugins/select2/select2.full.js')}}"></script>
{{-- Google Map --}}

<script>
    $(document).ready(function () {

        //Date range picker
        $('#daterange').daterangepicker({
            "locale": {
                "format": "DD/MM/YYYY",
                "separator": "-",
            }

        });

        $('#bill_collector').select2({
            ajax: {
                dataType: 'json',
                url: "{{URL::to('/')}}/auto/allbillcollectors",
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

 


        $('#show').on('click', function(e){
            e.preventDefault();
            var bill_collector_id = $('#bill_collector').val();
            var daterange_id = $('#daterange').val();
            if(bill_collector_id != null || daterange_id != null){
                $.ajax({
                    url: "{{URL::to('/mapdata')}}",
                    data:{
                        "bill_collector_id": bill_collector_id, 
                        "daterange_id": daterange_id
                    }
                })
                .done(function( data ) {
                    var custLat = [];
                    var custLong = [];                     
                    var collectedBy = [];                     
                    var collectedAt = []; 
                    var customerCode = []; 
                    $.each(data, function(index, value){
                        custLat.push(value.lat);
                        custLong.push(value.lon);
                        collectedBy.push(value.bill_collector_name);
                        collectedAt.push(value.timestamp);
                        customerCode.push(value.customer_code);
                    });

                    var center = new google.maps.LatLng(custLat[0], custLong[0]);

                    var mapCanvas = document.getElementById("map");
                    var mapOptions = {center: center, zoom: 15};
                    var map = new google.maps.Map(mapCanvas,mapOptions);


                    var markers = [];
                    var infowindows = [];
                    var linePoints = [];


                    for(let i=0; i<custLat.length; i++) {   

                        markers[i] = new google.maps.Marker({position:new google.maps.LatLng(custLat[i], custLong[i]),
                                                            map:map});
                        linePoints.push(new google.maps.LatLng(custLat[i], custLong[i]));
                        infowindows[i] = new google.maps.InfoWindow({
                            content: "<b>Collected By: </b>" + collectedBy[i] + "</br>" + 
                                    " <b>Collected At: </b>" + collectedAt[i] + "</br>" +  
                                    " <b>Customer Code: </b>" + customerCode[i] 
                        });

                        google.maps.event.addListener(markers[i], 'click', function() {
                            infowindows[i].open(map, markers[i]);
                        });

                    }

                    var flightPath = new google.maps.Polyline({
                        path: linePoints,
                        strokeColor: "#0000FF",
                        strokeOpacity: 0.8,
                        strokeWeight: 2
                    });

                    flightPath.setMap(map);

                });
               
            }
            else{
                // Do nothing
            }

           
            
            
            


        });     



        $('#clear_filter').on('click', function(e){
            e.preventDefault();
            $('#bill_collector').val(null).trigger("change");
            $('#daterange').val(null).trigger("change");
            var map = new google.maps.Map(document.getElementById('map'), {
              zoom: 15,
              center: {lat: 23.875855, lng: 90.379544},
              disableDefaultUI: true
            });

        });

    });
</script>


@endsection

@section('side_menu')
<ul class="sidebar-menu">
    <li class="header">MAIN NAVIGATION</li>
    <li>
        <a href="dashboard">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>            
        </a>        
    </li>
    <li class="treeview active">
        <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Users</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li class="active"><a href="allusers"><i class="fa fa-circle-o"></i> All User</a></li>
            <li><a href="create_users"><i class="fa fa-circle-o"></i> New User</a></li>            
        </ul>
    </li>
    <li>
        <a href="../widgets.html">
            <i class="fa fa-th"></i> <span>Widgets</span>
            <span class="pull-right-container">
                <small class="label pull-right bg-green">Hot</small>
            </span>
        </a>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-pie-chart"></i>
            <span>Charts</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="../charts/chartjs.html"><i class="fa fa-circle-o"></i> ChartJS</a></li>
            <li><a href="../charts/morris.html"><i class="fa fa-circle-o"></i> Morris</a></li>
            <li><a href="../charts/flot.html"><i class="fa fa-circle-o"></i> Flot</a></li>
            <li><a href="../charts/inline.html"><i class="fa fa-circle-o"></i> Inline charts</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-laptop"></i>
            <span>UI Elements</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="../UI/general.html"><i class="fa fa-circle-o"></i> General</a></li>
            <li><a href="../UI/icons.html"><i class="fa fa-circle-o"></i> Icons</a></li>
            <li><a href="../UI/buttons.html"><i class="fa fa-circle-o"></i> Buttons</a></li>
            <li><a href="../UI/sliders.html"><i class="fa fa-circle-o"></i> Sliders</a></li>
            <li><a href="../UI/timeline.html"><i class="fa fa-circle-o"></i> Timeline</a></li>
            <li><a href="../UI/modals.html"><i class="fa fa-circle-o"></i> Modals</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-edit"></i> <span>Forms</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="../forms/general.html"><i class="fa fa-circle-o"></i> General Elements</a></li>
            <li><a href="../forms/advanced.html"><i class="fa fa-circle-o"></i> Advanced Elements</a></li>
            <li><a href="../forms/editors.html"><i class="fa fa-circle-o"></i> Editors</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-table"></i> <span>Tables</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="../tables/simple.html"><i class="fa fa-circle-o"></i> Simple tables</a></li>
            <li><a href="../tables/data.html"><i class="fa fa-circle-o"></i> Data tables</a></li>
        </ul>
    </li>
    <li>
        <a href="../calendar.html">
            <i class="fa fa-calendar"></i> <span>Calendar</span>
            <span class="pull-right-container">
                <small class="label pull-right bg-red">3</small>
                <small class="label pull-right bg-blue">17</small>
            </span>
        </a>
    </li>
    <li>
        <a href="../mailbox/mailbox.html">
            <i class="fa fa-envelope"></i> <span>Mailbox</span>
            <span class="pull-right-container">
                <small class="label pull-right bg-yellow">12</small>
                <small class="label pull-right bg-green">16</small>
                <small class="label pull-right bg-red">5</small>
            </span>
        </a>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-folder"></i> <span>Examples</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="invoice.html"><i class="fa fa-circle-o"></i> Invoice</a></li>
            <li><a href="profile.html"><i class="fa fa-circle-o"></i> Profile</a></li>
            <li><a href="login.html"><i class="fa fa-circle-o"></i> Login</a></li>
            <li><a href="register.html"><i class="fa fa-circle-o"></i> Register</a></li>
            <li><a href="lockscreen.html"><i class="fa fa-circle-o"></i> Lockscreen</a></li>
            <li><a href="404.html"><i class="fa fa-circle-o"></i> 404 Error</a></li>
            <li><a href="500.html"><i class="fa fa-circle-o"></i> 500 Error</a></li>
            <li class="active"><a href="blank.html"><i class="fa fa-circle-o"></i> Blank Page</a></li>
            <li><a href="pace.html"><i class="fa fa-circle-o"></i> Pace Page</a></li>
        </ul>
    </li>
    <li class="treeview">
        <a href="#">
            <i class="fa fa-share"></i> <span>Multilevel</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
            <li>
                <a href="#"><i class="fa fa-circle-o"></i> Level One
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="#"><i class="fa fa-circle-o"></i> Level Two</a></li>
                    <li>
                        <a href="#"><i class="fa fa-circle-o"></i> Level Two
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                            <li><a href="#"><i class="fa fa-circle-o"></i> Level Three</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <li><a href="#"><i class="fa fa-circle-o"></i> Level One</a></li>
        </ul>
    </li>
    <li><a href="../../documentation/index.html"><i class="fa fa-book"></i> <span>Documentation</span></a></li>
    <li class="header">LABELS</li>
    <li><a href="#"><i class="fa fa-circle-o text-red"></i> <span>Important</span></a></li>
    <li><a href="#"><i class="fa fa-circle-o text-yellow"></i> <span>Warning</span></a></li>
    <li><a href="#"><i class="fa fa-circle-o text-aqua"></i> <span>Information</span></a></li>
</ul>
@endsection

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Map Report
    </h1>
</section>


<!-- Main content -->
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Map Report</h3>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">  
          <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Bill Collector</label>
                        <select name="bill_collector" class="form-control select2" id="bill_collector" style="width: 100%;">

                        </select>
                    </div>
                </div>
                <!-- /.col -->
                <div class="col-md-4">
                    <!-- Date range -->
                    <div class="form-group">
                        <label>Date range:</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="daterange">
                        </div>
                        <!-- /.input group -->
                    </div>
                    <!-- /.form group -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-md-2">
                    <button type="submit" id="show" class="btn btn-info">Submit</button>
                    <button id="clear_filter" class="btn btn-warning">Clear</button>
                    <br><br>
                </div>
            </div>
            <div id="map" style="width:100%;height:500px"></div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
        </div>
    </div>
    <!-- /.box -->
</section>
<!-- /.content -->

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDxbFNHeyypnFqncl06PRtEZsfG9D4LI1I"></script>
@endsection


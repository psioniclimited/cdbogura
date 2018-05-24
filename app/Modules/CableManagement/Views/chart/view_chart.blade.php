@extends('master')

@section('css')
@endsection

@section('scripts')
<script src="{{asset('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/1.0.2/Chart.min.js')}}"></script>
<script>

$(document).ready(function () {

  function drawLineChart() {
    var jsonData = $.post('chart_data',{ name: "John", time: "2mp" }).done(function (results) {
      console.log(results);
      // Split timestamp and data into separate arrays
      var labels = [], data=[];

      // results["packets"].forEach(function(packet) {
      //   labels.push(new Date(packet.timestamp).formatMMDDYYYY());
      //   data.push(parseFloat(packet.payloadString));
      // });

      $.each(results.data, function( index, value ) {
        data.push(parseFloat(value));
      });

      $.each(results.labels, function( index, value ) {
        labels.push(value);
      });

      // Create the chart.js data structure using 'labels' and 'data'
      var tempData = {
        labels : labels,
        datasets : [{
          fillColor             : "rgba(151,187,205,0.2)",
          strokeColor           : "rgba(151,187,205,1)",
          pointColor            : "rgba(151,187,205,1)",
          pointStrokeColor      : "#fff",
          pointHighlightFill    : "#fff",
          pointHighlightStroke  : "rgba(151,187,205,1)",
          data                  : data
       }]
      };

      // Get the context of the canvas element we want to select
      var ctx = document.getElementById("myChart").getContext("2d");
      var myLineChart = new Chart(ctx).Bar(tempData);
    });
  }

  drawLineChart();
  //var context = document.querySelector("#myChart").getContext('2d');
  //new Chart(context).Bar(data);

   
});



</script>

@endsection

@section('side_menu')

@endsection

@section('content')
<div class="container">
  <canvas id="myChart" width="500" height="500"></canvas>
</div>


@endsection
<html>
<head>
<style>

body {
  font: 12px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

</style>
<script src="//d3js.org/d3.v3.min.js" charset="utf-8"></script>
<script>
function lineChart(dataset, yscale=0) {
  var margin = {top: 20, right: 20, bottom: 30, left: 50},
      width = 600 - margin.left - margin.right,
      height = 160 - margin.top - margin.bottom;

  var x = d3.scale.linear()
      .range([0, width]);

  var y;
  if (yscale) {
    y = d3.scale.log()
      .range([height, 0]);
  }
  else {
    y = d3.scale.linear()
      .range([height, 0]);
  }

  var xAxis = d3.svg.axis()
      .scale(x)
      .ticks(20)
      .orient("bottom");

  var yAxis = d3.svg.axis()
      .scale(y)
      .ticks(5)
      .orient("left");

  var line = d3.svg.line()
      .x(function(d, i) { return x(i); })
      .y(function(d) { if (yscale>0 && d==0) { return y(0.1); } else {return y(d);} });

  var svg = d3.select("body").append("svg")
      .attr("width", width + margin.left + margin.right)
      .attr("height", height + margin.top + margin.bottom)
    .append("g")
      .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  x.domain(d3.extent(dataset, function(d, i) { return i; }));
  if (yscale) {
    y.domain([0.1, d3.max(dataset)]);
  }
  else {
    y.domain(d3.extent(dataset, function(d, i) { return d; }));
  }

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis);
/*
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Price ($)");
  */

  svg.append("path")
      .datum(dataset)
      .attr("class", "line")
      .attr("d", line);
}

function plotHisto(dataset) {
  var xmax = 50;
  dataset = dataset.slice(0,xmax);

  //Create SVG element
  var w = 500;
  var h = 100;
  var barPadding = 1;

  var xScale = d3.scale.linear()
    .domain([0, xmax])
    .range([0, w]);
  var ymax = d3.max(dataset);
  if (ymax < 1) {
    ymax = 1;
  }
  var yScale = d3.scale.linear()
    .domain([1, ymax])
    .range([0, h]);

  var svg = d3.select("body")
    .append("svg")
    .attr("width", w)
    .attr("height", h);
  svg.selectAll("rect")
    .data(dataset)
    .enter()
    .append("rect")
    .attr("y", function(d) {
          //return h - d;  //Height minus data value
        return h-yScale(d);
    })
    .attr("width", w / dataset.length - barPadding)
    .attr("x", function(d, i) {
      return i * (w / dataset.length);
    })
    .attr("height", yScale);
    //.attr("height", function(d) {
      //return d;  //Just the data value
    //});
 
}
</script>
</head>
<body>
<?php
$redis = new Redis();
$redis->connect('127.0.0.1');
$devices = $redis->sMembers("devices");
foreach ($devices as $dev) {
  print("<b>Device: ".$dev . "</b><br />");
  $last_up = $redis->get("d".$dev.":last_update");
  $l1_thresh = $redis->get("d".$dev.":l1_thresh");
  $l2_thresh = $redis->get("d".$dev.":l2_thresh");
  //print("Last update:".$last_up."<br/>");
  print("Last update: ".gmdate("Y-m-d @ H:i:s",(int)$last_up/1000)." UTC <br/>");
  print("L1 threshold: ".$l1_thresh."<br/>");
  print("L2 threshold: ".$l2_thresh."<br/>");
  print("<br/>");
  $fps = $redis->lRange("d".$dev.":fps", 0, -1);
  $fps_csv = implode(",", $fps);
  $fps_avg = array_sum($fps)/count($fps);
  print("<b>FPS per XB: ( Average ".round($fps_avg,2)." )</b><br/>");
?>
<script>
lineChart([<?=$fps_csv?>]);
</script>
<br/>
<?php
  $n_evt = $redis->lRange("d".$dev.":n_evt", 0, -1);
  $nevt_avg = array_sum($n_evt)/count($n_evt);
  print("<b>evts per XB: ( Average ".round($nevt_avg,2)." )</b><br/>");
  $n_evt_csv = implode(",", $n_evt);
?>
<script>
lineChart([<?=$n_evt_csv?>]);
</script>
<br/>
<?php
  $n_pix = $redis->lRange("d".$dev.":n_pix", 0, -1);
  $npix_avg = array_sum($n_pix)/count($n_pix);
  print("<b>pix per XB: ( Average ".round($npix_avg,2)." )</b><br/>");
  $n_pix_csv = implode(",", $n_pix);
?>
<script>
lineChart([<?=$n_pix_csv?>]);
</script>
<?php
  print("<br />");
  // print the histogram
  print("<b>pix_val distribution:</b><br />");
  $histo=$redis->get("d".$dev.":pix_histogram");
?>
<script>
//plotHisto([<?=$histo?>]);
lineChart([<?=$histo?>].slice(0,50), 1);
</script>
<?php

  print("<br />");
  print("<br />");
  print("<hr/>");
}
?>
</body>
</html>

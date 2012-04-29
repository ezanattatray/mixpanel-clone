<title>Panel</title>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<? if(isset($property_id)): ?>
<script type="text/javascript">
   $(function () {
       var chart;
       $(document).ready(function() {
	   var options = {
	       chart: {
	           renderTo: 'container'
  	       },
	       title: {
	           text: '<?=$property->name ?>'
	       },
	       xAxis: {
	           type: 'datetime',
		   //dateTimeLabelFormats: { // don't display the dummy year
		   //month: '%e. %b',
		   //year: '%b'
	           //}
	       },
	       yAxis: {
	           title: { text: '' },
	           min: 0
	       },
	       series: []
	   };

	   $.get('async_values.php?i=<?=$property_id ?>', function(data) {
	       var lines = data.split('\n');
	       
	       $.each(lines, function(lineNo, line) {
		   var series = {
		   data: []
		   };

		   var items = line.split(',');
		   $.each(items, function(itemNo, item) {
		       if(itemNo == 0) {
			 series.name = item;
		       } else {
			 var t = item.split(/[- :]/);
			 var d = Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
			 series.data.push([d, itemNo]);
		       }
		     });
		   if(series.name != "") { // gross hack to fix a bug somewhere in my php
		     options.series.push(series);
		   }
		 });
	       chart = new Highcharts.Chart(options);
	     });
	 });
     });
</script>
<? endif; ?>
</head>
<body>
<script src="js/highcharts.js"></script>
<script src="js/modules/exporting.js"></script>
<!-- Additional files for the Highslide popup effect -->
<script type="text/javascript" src="http://www.highcharts.com/highslide/highslide-full.min.js"></script>
<script type="text/javascript" src="http://www.highcharts.com/highslide/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="http://www.highcharts.com/highslide/highslide.css" />
<div class="header"><?=$user->email ?> | <a href="logout.php">Logout</a></div>
<div class="box">
<center>
<h1>MixPanel</h1>
<ul class="horiz">
<? foreach($events as $event): ?>
   <li><a href="#" onClick="loadProperties(<?= $event->id ?>)"><?= $event->name ?></a></li>
<? endforeach; ?>
</ul>
<div id="properties"></div>
<div id="container">Select an event and property...</div>
</center>
</div>
</body>
</html>
<?// dump($companyUpdates, 1); ?>
<?// dump($impressionsUnique, 1); ?>
<?// dump($clicks, 1); ?>
<?// dump($likes, 1); ?>
<?// dump($comments, 1); ?>
<?// dump($engagement, 1); ?>

<div class="block-company_analytics_rich">
	<div class="title-big">Rich</div>

	<div class="company_analytics_rich-btns bg-blue">
		<div>
			<a class="active" href="#" onclick="return web.switchGraph(this)" data-graph="graph-impressions" data-block="company_analytics-leftrich">Impressions</a>
			<a href="#" onclick="return web.switchGraph(this)"  data-graph="graph-impressions-uniques" data-block="company_analytics-leftrich">Uniques</a>
		</div>
		<div>
			<a class="active" href="#" onclick="return web.switchGraph(this)" data-graph="graph-clicks" data-block="company_analytics-rightrich">Clicks</a>
			<a href="#" onclick="return web.switchGraph(this)" data-graph="graph-likes" data-block="company_analytics-rightrich">Likes</a>
			<a href="#" onclick="return web.switchGraph(this)" data-graph="graph-comments" data-block="company_analytics-rightrich">Comments</a>
			<a href="#" onclick="return web.switchGraph(this)" data-graph="graph-engagement" data-block="company_analytics-rightrich">Engagement %</a>
		</div>
	</div>

	<div>
		<div class="company_analytics-leftrich">
			<div class="graph-block graph-impressions active">
				<div class="graph-impressions" id="graph-impressions"></div>
				<div class="graph-impressions1" id="graph-impressions1"></div>
			</div>
			<div class="graph-block graph-impressions-uniques">
				<div class="graph-impressions-uniques" id="graph-impressions-uniques"></div>
				<div class="graph-impressions-uniques1" id="graph-impressions-uniques1"></div>
			</div>
		</div>
		<div class="company_analytics-rightrich">
			<div class="graph-block graph-clicks active">
				<div class="graph-clicks" id="graph-clicks"></div>
				<div class="graph-clicks1" id="graph-clicks1"></div>
			</div>
			<div class="graph-block graph-likes">
				<div class="graph-likes" id="graph-likes"></div>
				<div class="graph-likes1" id="graph-likes1"></div>
			</div>
			<div class="graph-block graph-comments">
				<div class="graph-comments" id="graph-comments"></div>
				<div class="graph-comments1" id="graph-comments1"></div>
			</div>
			<div class="graph-block graph-engagement">
				<div class="graph-engagement" id="graph-engagement"></div>
				<div class="graph-engagement1" id="graph-engagement1"></div>
			</div>
		</div>
	</div>
</div>



<script type="text/javascript">





google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);
function drawChart() {
	var data = google.visualization.arrayToDataTable([
		['Month', 'Impressions', {role: 'annotationText', p: {html:true}}],
		<? foreach($impressions as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> impressions'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 4,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-impressions'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Impressions'],
		<? foreach($impressions as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-impressions1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},

		});








var data = google.visualization.arrayToDataTable([
		['Month', 'Uniques visitors', {role: 'annotationText', p: {html:true}}],
		<? foreach($impressionsUnique as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> uniques visitors'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 4,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-impressions-uniques'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Uniques'],
		<? foreach($impressionsUnique as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-impressions-uniques1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},


		});





	var data = google.visualization.arrayToDataTable([
		['Month', 'Clicks', {role: 'annotationText', p: {html:true}}],
		<? foreach($clicks as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> clicks'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 4,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-clicks'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Clicks'],
		<? foreach($clicks as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-clicks1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},

		});







var data = google.visualization.arrayToDataTable([
		['Month', 'Likes', {role: 'annotationText', p: {html:true}}],
		<? foreach($likes as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> likes'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 4,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-likes'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Likes'],
		<? foreach($likes as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-likes1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},

		});






var data = google.visualization.arrayToDataTable([
		['Month', 'Comments', {role: 'annotationText', p: {html:true}}],
		<? foreach($comments as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> comments'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 4,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-comments'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Comments'],
		<? foreach($comments as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-comments1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},

		});







var data = google.visualization.arrayToDataTable([
		['Month', 'Engagement', {role: 'annotationText', p: {html:true}}],
		<? foreach($engagement as $month => $count) : ?>
		['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> %'  ],
		<? endforeach ?>
	]);

	var options = {
		title: 'visitors',
		width: 420,
		height: 300,
		chartArea:{
			left:10,top:10,width:'95%',height:'85%'
		},
		backgroundColor: 'none',
		legend: 'none',
		pointSize: 12,
		lineWidth: 2,
		colors: ['#119fcd'],

		vAxis:{
			textPosition: 'in',
			baseline: 0,
			baselineColor: '13a0ce',
			gridlines: {
				color: 'none'
			},
			maxValue: 0.001,
		},
	};

	var chart = new google.visualization.LineChart(document.getElementById('graph-engagement'));
	chart.draw(data, options);


	var data2 = google.visualization.arrayToDataTable([
		['Month', 'Engagement'],
		<? foreach($engagement as $month => $count) : ?>
		[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
		<? endforeach ?>
	]);

	chart2 = new google.visualization.LineChart(document.getElementById('graph-engagement1'));
	chart2.draw(data2,
		{
			width: 420,
			height: 300,
			chartArea:{
				left:10,top:10,width:'95%',height:'85%'
			},
			colors: ['#FFF'],
			legend: 'none',
			hAxis:{
				textPosition: 'none',
				format: '#',
				baselineColor: '#13a0ce',
				gridlines: {
					count: 7,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				}
			},
			vAxis:{
				textPosition: 'none',
				baseline: 0,
				baselineColor: '#13a0ce',
				gridlines: {
					count: 5,
					color: '#ececec'
				},
				minorGridlines: {
					count: 4,
					color: '#f5f5f5'
				},
			},

		});








}





</script>
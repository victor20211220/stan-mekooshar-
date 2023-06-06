<?// dump($statisticByMonth, 1); ?>
<?// dump($statisticByWeek, 1); ?>
<?// dump($statisticByDay, 1); ?>
<?// dump($f_Profile_ChangeGpaphStatistic, 1); ?>

<div class="block-profile-visits-graph">
	<div class="content-title">
<!--		<div class="content-title-icon"><div><div></div></div></div>-->
		<div>Your profile statistic</div>
	</div>
	<?= $f_Profile_ChangeGpaphStatistic->form->render(); ?>
	<div class="visits-graph">
		<div class="">
			<div class="graph-month" id="graph-month"></div>
			<div class="graph-month1" id="graph-month1"></div>
		</div>

		<div class="hidden">
			<div class="graph-week" id="graph-week"></div>
			<div class="graph-week1" id="graph-week1"></div>
		</div>

		<div class="hidden">
			<div class="graph-day" id="graph-day"></div>
			<div class="graph-day1" id="graph-day1"></div>
		</div>
	</div>
</div>

<script type="text/javascript">





	google.load("visualization", "1", {packages:["corechart"]});
	google.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Month', 'Visitors', {role: 'annotationText', p: {html:true}}],
			<? foreach($statisticByMonth as $month => $count) : ?>
				['<?= date('M', strtotime($month)) ?>',  <?= $count ?>, '<?= $count ?> visitors'  ],
			<? endforeach ?>
		]);

		var options = {
			title: 'visitors',
			width: 330,
			height: 270,
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
			},
			maxValue: 4,
		};

		var chart = new google.visualization.LineChart(document.getElementById('graph-month'));
		chart.draw(data, options);


		var data2 = google.visualization.arrayToDataTable([
			['Month', 'Visitors'],
			<? foreach($statisticByMonth as $month => $count) : ?>
				[<?= date('m', strtotime($month)) ?>,  <?= $count ?>],
			<? endforeach ?>
		]);

		chart2 = new google.visualization.LineChart(document.getElementById('graph-month1'));
		chart2.draw(data2,
			{
				width: 330,
				height: 270,
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
					maxValue: 4,
				},

			});











		var data = google.visualization.arrayToDataTable([
			['Week', 'Visitors', {role: 'annotationText', p: {html:true}}],
			<? foreach($statisticByWeek as $week => $count) : ?>
			['<?= date('d M', time() - 60*60*24*7*($week-1)) ?>',  <?= $count ?>, '<?= $count ?> visitors'  ],
			<? endforeach ?>
		]);

		var options = {
			title: 'visitors',
			width: 330,
			height: 270,
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

		var chart = new google.visualization.LineChart(document.getElementById('graph-week'));
		chart.draw(data, options);


		var data2 = google.visualization.arrayToDataTable([
			['Week', 'Visitors'],
			<? foreach($statisticByWeek as $week => $count) : ?>
			[<?= $week ?>,  <?= $count ?>],
			<? endforeach ?>
		]);

		chart2 = new google.visualization.LineChart(document.getElementById('graph-week1'));
		chart2.draw(data2,
			{
				width: 330,
				height: 270,
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
						count: 5,
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
					maxValue: 4,
				},

			});








		var data = google.visualization.arrayToDataTable([
			['Week', 'Visitors', {role: 'annotationText', p: {html:true}}],
			<? foreach($statisticByDay as $day => $count) : ?>
			['<?= date('d M', strtotime($day)) ?>',  <?= $count ?>, '<?= $count ?> visitors'  ],
			<? endforeach ?>
		]);

		var options = {
			title: 'visitors',
			width: 330,
			height: 270,
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

		var chart = new google.visualization.LineChart(document.getElementById('graph-day'));
		chart.draw(data, options);


		var data2 = google.visualization.arrayToDataTable([
			['Week', 'Visitors'],
			<? foreach($statisticByDay as $day => $count) : ?>
			[<?= $day ?>,  <?= $count ?>],
			<? endforeach ?>
		]);

		chart2 = new google.visualization.LineChart(document.getElementById('graph-day1'));
		chart2.draw(data2,
			{
				width: 330,
				height: 270,
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
						count: 8,
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
					maxValue: 4,
				},

			});
	}





</script>

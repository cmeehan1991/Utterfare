$(document).ready(function(){
	topTermsChart();
	searchCountChart();
	platformChart();
});

function topTermsChart(){
	var data = {
		action: 'getTermsData'
	};
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('topTermsChart','Top 5 Search Terms','doughnut', response);
		},
		error:function(){
			console.log('error');
		}
	});
}

function searchCountChart(){	
	var data = {
		action: 'getSearchCount'
	};
	
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('searchCountChart','Search Appearances','line', response);
		},
		error:function(){
			console.log('error');
		}
	})
}

function platformChart(){
	var data = {
		action: 'get_platforms'
	};
	
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function (response){
			drawChart('searchPlatformChart', "Searches by Platform", 'bar', response);
		}
	})
}

function drawChart(container, title, chartType, response){
	var label = [];
	var count = [];
	var totalCount = 0;
	
	$.each(JSON.parse(response), function(term, total){
		label.push(term);
		count.push(parseInt(total));
		totalCount += total;
	});
	
	var fill = null, options = null, borderColor = null;
	if(chartType === 'bar' || chartType === 'line'){
		borderColor = 'rgba(2,136,209,1.0)';
		fill = false;
		options = {
			title: {
				display: true,
				text: title,
				fontSize: 24,
			},
			responsive: true,
			scales :{ yAxes: 
				[{ 
					display: true, 
					ticks: {
						suggestedMin: 0,
						steps: 0.5,				
					}
				}]
			}
		};
	}else if(chartType === 'doughnut'){
		borderColor = 'rgba(0,0,0,0)';
		options = {
			title: {
				display: true,
				text: title,
				fontSize: 24,
			},
			responsive: true,
		}
	}

	var ctx = $("#" + container);
	var myChart = new Chart(ctx, {
	    type: chartType,
	    data: {
	        labels: label,
	        datasets: [{
	            label: title, 
	            data: count,  
	            fill: fill,
	            borderColor: borderColor,
	            backgroundColor: [
                'rgba(2,136,209, 1.0)',
                'rgba(2,136,209, 0.80)',
                'rgba(2,136,209, 0.60)',
                'rgba(2,136,209, 0.40)',
                'rgba(2,136,209, 0.20)',
				],
	        }],
	    },
	    options: options
	});
	
	if(myChart.data.datasets[0].data.length === 0){
		Chart.plugins.register({
			afterDraw: function(chart){
				ctx = myChart.chart.ctx;
				var width = myChart.chart.width;
				var height = myChart.chart.height
				//myChart.clear();
				
				ctx.save();
				ctx.textAlign = 'center';
				ctx.textBaseline = 'middle';
				ctx.font = "16px normal 'Helvetica Nueue'";
				ctx.canvas.title = myChart.options.title.text;
				ctx.fillText('No data to display', width / 2, height / 2);
				//ctx.restore();

			}
		})
	}
	/*Chart.plugins.register({
		afterDraw: function(chart){
			$(chart.canvas).each(function(){
				if(myChart.data.datasets.data === undefined){
					var ctx = myChart.chart.ctx;
					var width = myChart.chart.width;
					var height = myChart.chart.height
					myChart.clear();
					
					ctx.save();
					ctx.textAlign = 'center';
					ctx.textBaseline = 'middle';
					ctx.font = "16px normal 'Helvetica Nueue'";
					ctx.fillText('No data to display', width / 2, height / 2);
					ctx.restore();
				}
			});
		}
	});*/

} 
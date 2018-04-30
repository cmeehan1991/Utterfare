$(document).ready(function(){
	topTermsChart();
});

function topTermsChart(){
	var data = {
		action: 'getTermsData'
	}
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('topTermsContainer','Top Search Terms','doughnut', response);
		},
		error:function(){
			console.log('error');
		}
	});
}

function drawChart(container, title, chartType, response){
	var count = [];
	var terms = [];
	$.each(JSON.parse(response), function(index, term){
		count.push(term);
		terms.push(index);
	});
	
	console.log(count[0]);
	
	var chart = new CanvasJS.Chart(container, {
		animation: true, 
		title:{
			text:title, 
			horizontalAlignment: "center",
		},
		data: [{
			type:chartType,
			startAngle: 60, 
			//innerRadius:60,
			indexLabelFontSize: 17,
			percentFormatString: "#0,##################",
			indexLabel: "{label}",
			toolTipContent: "<b>{label}:</b> {y}",
			dataPoints:[
				{y: count[0], label: terms[0]},
				{y: count[1], label: terms[1]},
				{y: count[2], label: terms[2]},
				{y: count[3], label: terms[3]},
				{y: count[4], label: terms[4]},
			]			
		}]
	});
	chart.render();
}
$(document).ready(function(){
	topTermsChart();
	searchCountChart();
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
	console.log("Here");	var data = {
		action: 'getSearchCount'
	};
	
	$.ajax({
		data: data, 
		method: 'post', 
		url: 'includes/php/SearchAnalytics.php',
		success: function(response){
			drawChart('searchCountChart','Search Appearances','bar', response);
		},
		error:function(){
			console.log('error');
		}
	})
}

function drawChart(container, title, chartType, response){
	var label = [];
	var count = [];
	var totalCount = 0;
	console.log(response);
	$.each(JSON.parse(response), function(term, total){
		label.push(term);
		count.push(parseInt(total));
		totalCount += total;
	});
	
	var ctx = $("#" + container);
	var myChart = new Chart(ctx, {
	    type: chartType,
	    data: {
	        labels: label,
	        datasets: [{
	            label: title, 
	            data: count, 
	            
	            backgroundColor: [
                'rgba(2,136,209, 1.0)',
                'rgba(2,136,209, 0.80)',
                'rgba(2,136,209, 0.60)',
                'rgba(2,136,209, 0.40)',
                'rgba(2,136,209, 0.20)',
            ],
	        }],
	        
	    },
	});
} 
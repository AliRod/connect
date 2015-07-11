$(document).ready(function() {




	$('#clear-form').click(function(){
		$('[name=winename]').val('');
		$('[name=winery]').val('');
		$('[name=onhand]').val('');
		$('[name=ordered]').val('');
		$('[name=mincost]').val('');
		$('[name=maxcost]').val('');

		clearDBFields();
	});

	function clearDBFields() {

	}


	
});

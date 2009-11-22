var $j = jQuery.noConflict(); // because prototype is available in backend modules by default

$j(document).ready(function(){
	$j('#amsSelected input').click(function(){
		
		var id = $j(this).val().split('_');
		id = id[0];
		
		$ch = $(this).checked;
		
		$j('#amsSelected input').each(function(){
			var id2 = $j(this).val().split('_');
			id2 = id2[0];
			
			if (id == id2){
				$(this).checked = $ch;
			}			
		});
				
	});
		
});
(function($){
	$('#menu a').click(function(){
		$page =$(this).attr("href");
		$.ajax({
			url : $page,
			cache: false,
			success: function (html) {
				afficher(html);
			},
			error : function( jqXHR, textStatus, errorThrown){
				alert('textStatus');
			}
		})
		return false;
	});

})(jQuery);

function afficher (data) {
	$('#content').fadeOut(200,function(){
		$('#content').empty();
		$('#content').append(data);
		$('#content').fadeIn(200);
	})
}
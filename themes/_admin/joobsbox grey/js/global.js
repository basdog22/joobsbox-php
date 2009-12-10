$(document).ready(function(){
	$("li.item").mouseenter(function() {
		$(this).addClass("hover");
	});
	$("li.item").mouseleave(function() {
		$(this).removeClass("hover");
	});

	if(typeof(disableJqueryUI) == "undefined") {
  	$("#admin-menu").sortable({
  		distance: 15,
  		cancel: '#menu-dashboard, .separator',
  		update: function() {
  		  $.post(baseUrl + '/admin/sortmenu/', $("#admin-menu").sortable("serialize"));
  	  }
  	});
  }
});

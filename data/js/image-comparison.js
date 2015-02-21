$(document).ready(function () {
	$(".image-comparison > .overlay").resizable({
		handles: 'e',
		resize: function(e, ui) {
			var p = ui.element.parent();
			var percent_width = ui.element.width()/p.width();
			ui.element.css({
	            width: percent_width*100+"%",
	        });
	        $('img', ui.element).width(100/percent_width+"%");
		},
		containment: ".image-comparison"
	});
	$(document).scroll(function () {
		var offset = $('#main-download-game').offset().top;
		var viewbottom = $(document).scrollTop() + $(window).height();
		if(viewbottom > offset)
		{
			var viewoff = (viewbottom - offset) < 680 ? (viewbottom - offset) : 680;
			$('#main-download-game').css('background-position', 'center ' + (viewoff) + 'px');
			$('#main-download-game > .download-game-inner').css('top', 680 - (viewbottom - offset) / 1.5 + 'px');
		}
	});
});
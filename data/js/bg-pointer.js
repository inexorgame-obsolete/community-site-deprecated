$(document).ready(function () {
	var windowHeight = $(window).height();
	var windowWidth = $(window).width();
	$('.image-mover').css('background-position', '50% 50%');
	$(document).mousemove(function (e) {
		console.log();
		$('.image-mover').css('background-position', (e.pageX / windowWidth * 100) + '% ' + (e.pageY / windowHeight * 60 + 20) + '%');
		// console.log((e.pageY / windowHeight * 10 + 45));
	});

	$('a[href|="#nojump"]').click(function (e) {
		e.preventDefault();
		before_anchor_click_offset_top = $(document).scrollTop();
		window.location.hash = $(this).attr('href').substr(1);
		$(window).scrollTop(before_anchor_click_offset_top);
	});
});
$(document).ready(function () {
	var logo_link = base_url + 'data/images/logo_extrasmall.png';
	var logo_original = base_url + 'data/images/logo_small.png'
	var docked = false;
	$(window).scroll(function () {
		var _header = $('header');
		var _header_height = _header.height();
		if(docked == false) {
			if($(window).scrollTop() > _header_height+$('#main-eyecatcher').height())
			{
				docked = true;
				$('header .helper img').attr('src', logo_link);
				$('#header_placeholder').height(_header_height);
				_header
					.addClass('disable-transition')
					.css('margin-top', -_header_height + 'px');
				_header[0].offsetHeight;
				_header
					.addClass('docked')
					.removeClass('disable-transition')
					.css('margin-top', '0px');
				_user_padding = (($('header').height()-59)/2-1) + 'px';
				$('#users-nav > .user-profile').css({
					'padding-top'   : _user_padding,
					'padding-bottom': _user_padding,
				});
			}
		}
		else
		{
			if($(window).scrollTop() < _header_height+$('#main-eyecatcher').height())
			{
				docked = false;
				$('header .helper img').attr('src', logo_original);
				$('#header_placeholder').height(0);
				_header
					.css('margin-top', 0)
					.removeClass('docked');
					_user_padding = (($('header').height()-59)/2) + 'px';
				$('#users-nav > .user-profile').css({
					'padding-top'   : _user_padding,
					'padding-bottom': _user_padding,
				});
			}
		}
	});
});
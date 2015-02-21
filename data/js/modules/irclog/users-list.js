$(document).ready(function () {
	var _check_irc_users_height = function () {
		if ($(window).scrollTop() > topDistance) {
			if(($(window).scrollTop() - topDistance - 10) < $('#log').height() - $('#user-list').height()) {
				$('#user-list').css({
					'margin-top': $(window).scrollTop() - topDistance + $('header').height(),
					'max-height': window.innerHeight - $('header').height()
				});
			} else {
				$('#user-list').css({
					'margin-top': $('#log').height() - $('#user-list').height(),
					'max-height': window.innerHeight
				});
			}
		} else {
			$('#user-list').css({
				'margin-top': 0,
				'max-height': 'none'
			});
		}
	};
	var ircActions = {
	};
	var i = 0;
	var _userlist = $('#user-list');
	$('.user_message, .user_connection, .user_renaming').each(function () { $(this).data('id', i); i++; });
	$('.user-action').each(function () {
		var _this = $(this);
		ircActions[_this.parent().data('id')] = _this.data();
	});
	var topDistance = _userlist.offset().top;
	$(window).scroll(_check_irc_users_height);
	_check_irc_users_height();
	$('.user_message, .user_connection, .user_renaming').mouseenter(function () {
		var _this = $(this);
		var users = {};
		var id = _this.data('id');
		var renamings = [];
		var keys = [];
		var users_v = {
			ops    : [],
			voices : [],
			normal : []
		}
		$('#irc_selected_triangle').remove();
		$('#irc_selected').removeAttr('id');
		_this.children().last().append('<span id="irc_selected_triangle"><span></span></span>');
		_this.attr('id', 'irc_selected');
		for(var i = id; i <= _this.siblings().length + 1; i++) {
			if(ircActions[i] !== undefined)
			{
				if(ircActions[i].userList !== undefined)
				{
					users = ircActions[i].userList;
					break;
				} else if(ircActions[i].userRename !== undefined) {
					renamings.push(ircActions[i].userRename);
				}
			}
		}
		for(var i = 0; i < renamings.length; i++) {
			users[renamings[i][1]] = users[renamings[i][0]];
			delete users[renamings[i][0]];
		}
		var _ul = $('#user-list > ul');
		$('li', _ul).remove();
		$(_ul).append('<li class="title"><sup></sup>at the selected message were online:</li>');
		$.each(users, function (i, v) {
			if(v == '@') users_v.ops.push(i);
			else if(v == '+') users_v.voices.push(i);
			else users_v.normal.push(i);
		});
		users_v.ops.sort();
		users_v.voices.sort();
		users_v.normal.sort();

		for(var i = 0; i < users_v.ops.length; i++) {
			_ul.append('<li class="timed">@' + users_v.ops[i] + '</li>');
		}
		for(var i = 0; i < users_v.voices.length; i++) {
			_ul.append('<li class="timed">+' + users_v.voices[i] + '</li>');
		}
		for(var i = 0; i < users_v.normal.length; i++) {
			_ul.append('<li class="timed">' + users_v.normal[i] + '</li>');
		}
		$('#user-list .users-count').text('(' + (users_v.ops.length + users_v.voices.length + users_v.normal.length) + ')');
		_check_irc_users_height();
	});
});
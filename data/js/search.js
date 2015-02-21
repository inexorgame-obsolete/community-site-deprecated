;(function ($, document, window, undefined){
	var _defaults = {
		'id_attribute': 'searchid',
		'default_parser': function(data) {return _objectToTable(data); },
		'base_url': base_url,
		'build_url': function (api, search) {return _build_url(api, search); },
		'min_chars': 3
	},
	_settings = {},
	_searchs = [];

	$.fn.search = function (api, options) {
		if(options != undefined) {
			if( options.parser != undefined ) {
				var parser = options.parser;
				delete options.parser;
			}
			if( typeof options.on_arrow_down == 'function' ) {
				var on_arrow_down = options.on_arrow_down;
				delete options.on_arrow_down;
			}
			if( typeof options.on_change_callback == 'function' ) {
				var on_change_callback = options.on_change_callback;
				delete options.on_change_callback;
			}
		}
		_settings = $.extend({}, _defaults, options);
		this.each(function () {
			var search = {input: $(this)};
			if((search.id = search.input.data(_settings['id_attribute'])) != undefined)
			{
				search.result = $('.result[data-' + _settings['id_attribute'] + '="' + search.id + '"]');
				search.api = api;
				if(parser != undefined) search.parser = parser;
				else search.parser = _settings.default_parser;
				_searchs[search.id] = search;
				if( on_change_callback != undefined ) { $.search().set_on_change_callback(search.id, on_change_callback); }
				if( on_arrow_down != undefined ) { $.search().set_on_arrow_down(search.id, on_arrow_down); }
				$.search().set_parser(search.id, search.parser);
			}
		});
		return this;
	};

	$.search = function (id) { 
		return {
			set_parser: function (search_id, parser) {
				if(typeof parser == 'function')
				{
					_searchs[search_id].parser = parser;
					_bind_search(_searchs[search_id]);
					return true;
				}
				return false;
			},
			set_api: function (search_id, api) {
				_searchs[search_id].api = api;
				_bind_search(_searchs[search_id]);
				return true;
			},
			set_on_arrow_down: function (search_id, on_arrow_down)
			{
				if(typeof on_arrow_down == 'function' && typeof _searchs[search_id] == 'object')
				{
					_searchs[search_id].on_arrow_down = on_arrow_down;
					_bind_search(_searchs[search_id]);
					return true;
				}
				return false;
			},
			set_on_change_callback: function (search_id, on_change_callback) 
			{
				if(typeof on_change_callback == 'function' && typeof _searchs[search_id] == 'object')
				{
					_searchs[search_id].on_change_callback = on_change_callback;
					_bind_search(_searchs[search_id]);
					return true;
				}
				return false;
			}
		}
	};
	var _bind_search = function (search) {
		var obj = search.input;
		obj.unbind('keyup');
		obj.keyup(function (e) {
			if($(this).val().length >= _settings.min_chars) {
				if(e.keyCode == 40 && typeof search.on_arrow_down == 'function') {
					search.on_arrow_down(search.input, search.result);
				} else {
					$.get(_settings.build_url(search.api, $(this).val()), function (data) {
						search.result.html(search.parser(data));
						if(typeof search.on_change_callback == 'function') search.on_change_callback(search.input, search.result);
					});
				}
			} else {
				search.result.html('');
			}
		});
	};

	var _objectToTable = function (data) {
		if(typeof data != 'object')	data = $.parseJSON(data);
		if(data.results == 0)
		{
			return '<div class="no-results">There are no results for your search.</div>';
		} else if(data.error != undefined) {
			return '<div class="error">' + data.error.message + '</div>';
		} else {
			var i = 0;
			var html = '<table class="result">';
			$.each(data, function (key, value) {
				if(i == 0)
				{
					html += '<tr>';
					$.each(value, function (kkey, vvalue) {
						html += '<th>' + kkey + '</th>';
					});
					html += '</tr>';
				}
				html += '<tr>';
				$.each(value, function (kkey, vvalue) {
					html += '<td>' + vvalue + '</td>';
				});
				html += '</tr>';
				i++;
			});
			html += '</table>';
			return html;
		}
	};

	var _build_url = function (api, search) {
		if(api[0] == '/') api = api.substr(1);
		if(api[api.length-1] != '/') api += '/';
		var url = _settings.base_url + api;
		if(typeof search == 'object') {
			url += '?';
			$.each(search, function (key, value) {
				url += key + '=' + value + '&';
			});
			url = url.substr(0, url.length-2);
		} else {
			url += search;
		}
		return url;
	};

}(jQuery));
var template = {
	showname: function (username, first_name, last_name, span_class) {
		if(typeof username == 'object')
		{
			if(typeof first_name == 'string') span_class = first_name;
			first_name = username.first_name;
			last_name = username.last_name;
			username = username.username;
		}
		if(typeof first_name == 'string') if(first_name.length == 0) first_name = undefined;
		if(typeof last_name == 'string') if(last_name.length == 0) last_name = undefined;
		if(typeof span_class != 'string' || span_class.length == 0) span_class = 'user'; 
		var html = '<span class="' + span_class + '">';
		if(typeof first_name == 'string') html += template.html_entities(first_name) + ' &laquo;';
		else if(typeof last_name == 'string') html += ' &laquo;';
		html += template.html_entities(username);
		if(typeof last_name == 'string') html += '&raquo; ' + template.html_entities(last_name);
		else if(typeof first_name == 'string') html += '&raquo;';
		html += '</span>';
		return html;
	},
	html_entities: function (string) {
		return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
}
var _mainsearch = {
	parser: function (data) {
		$('#search-window').addClass('show-results');
		var html = '<div class="user-list-container intransparent">';
		if(data.results == 0) {
			html += '<a class="user-section"><h4 class="no-search-results">There are no results for your search.</h4></a>';
		} else {
			delete data.results;
			$.each(data, function (key, value) {
				html += '<a href="' + base_url + 'user/' + value.id + '" class="user-section"';
				if(value.images.background != false && value.images.background != undefined) {
					html += ' style="background-image:url(' + value.images.background + ');"';
				}
				html += '><span class="avatar" style="background-image:url(' + value.images.avatar + ');"></span><h3 class="text-contrast">' + template.showname(value) + '</h3></a>';
			});
		}
		html += '</div>';
		return html;
	},
	on_arrow_down: function (input, result) {
		$('.user-section:first', result).focus();
	},
	on_change_callback: function (input, result) {
		$('.user-section', result).keydown(function (e) {
			if(e.keyCode == 38 && $(this).prev().hasClass('user-section')) { $(this).prev().focus(); } 
			else { input.focus(); }
			if(e.keyCode == 40 && $(this).next().hasClass('user-section')) { $(this).next().focus(); } 
		});
	}
};
$(document).ready(function () {
	$('#search-window input[data-searchid="main_search"]').search('search/api/user', _mainsearch);
});
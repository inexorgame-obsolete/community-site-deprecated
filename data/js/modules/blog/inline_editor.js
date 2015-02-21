tinymce.init({
    selector: "#edit-headline",
    inline: true,
    toolbar: "undo redo | bold italic underline strikethrough superscript subscript",
    menubar: false
});
var debug;
function file_browser() {
	var self = this;
	var acceptextension = {
		image: ['png', 'jpg', 'jpeg', 'gif']
	};
	var requested_format;
	var data;
	var current_dir;
	var _dir_updated;
	var input_field;
	var text = {
		exceeded_file_limit: 'You have exceeded your file-limit! Please remove some folders to create new ones or request more space.',
		exceeded_folder_limit: 'You have exceeded your folder-limit! Please remove some files to upload new ones or request a higher folder-limit.',
		parent_dir: 'Parent directory',
		directory: 'Directory',
		empty_or_without_file: 'Empty or without %s-file'
	}
	this.update_messages = function (message, clear) {
		$('.browse-data.message-container').removeClass('hidden');
		if(clear != false) clear = true; 
		if(clear == true) self.clear_messages();
		$('<p class="browse-data message-item">' + message + '</p>')
			.appendTo('.browse-data.messages')
			.animate({opacity: 0}, 250, 'swing', function () {$(this).animate({opacity: 1}, 250);});
		if($('.browse-data.message-item').length > 1)
		{
			$('.browse-data.message-container .label').text('Messages');
		} else {
			$('.browse-data.message-container .label').text('Message');
		}
	}
	this.clear_messages = function () {
		$('.browse-data.messages').html('');
	},
	this.display = function (field, url, type, win) {
		if(field == undefined) field = input_field;
		else input_field = field;
		if(type != undefined) requested_format = type;
		var _input 	= $('#' . field);
		var browse_data = $('#browse-data').removeClass('hidden');
		$('.browse-data.message-container').addClass('hidden');
		$.getJSON(base_url + 'data/api/', function (d) {
			data = d;
			if(url != undefined && url.indexOf(data.base)===0)
			{
				current_dir = url.substr(data.base.length).split('/');
			}
			if(data.uploadleft.folders < 1) { $('.browse-data.create-dir').addClass('hidden');
			self.update_messages(text.exceeded_dir_limit); }
			else { $('.browse-data.create-dir').addClass('remove'); }
			if(data.uploadleft.files < 1) { 
				$('.browse-data.upload').addClass('hidden');
				self.update_messages(text.exceeded_file_limit, false);
			} else { $('.browse-data.upload').removeClass('hidden'); }
			if(typeof self.changedir == 'function') self.changedir($('.browse-data.content', browse_data));
		});
		$('.browse-data.close', browse_data).click(function () {
			browse_data.addClass('hidden');
		});
		return;
	};
	this.add_file = function (name, size, enabled, appendTo) {
		if(typeof appendTo == 'string') appendTo = $(appendTo);
		if(enabled !== false) enabled = true;

		var link = '';
		if(current_dir.length>0) link = current_dir.join('/') + '/';
		link = self.link(link + name);
		var fileext = name.split('.');
		fileext = self.htmlentities(fileext[fileext.length-1]);
		var element = $('<div class="browse-data item file browse-' + fileext.replace(new RegExp(' ', 'g'), '') + '">' + self.htmlentities(name) + '<div class="browse-data description" data-link="' + link + '">' + self.bytesToSize(size) + '</div></div>').appendTo(appendTo);
		if(enabled == false) element.addClass('disabled');
		if(enabled == true) {
			$(element).click(function () {
				$('#' + input_field).val(link);
				$('#browse-data').addClass('hidden');
			});
		}
	}
	this.link = function (link) {
		link = base_url + user_dir + link;
		return link
	}
	this.changedir = function (_this) {
		var _content 	= data;
		var _base		= data.base;
		var _current_dir = _content.content;
		if(typeof current_dir == 'object' && current_dir.length > 0) {
			for(var i = 0; i < current_dir.length; i++)
			{
				var _current_dir = _current_dir[current_dir[i]];
			}
		} else {
			current_dir = [];
		}
		_this.html('');
		if(current_dir.length > 0) {
			var element = $('<div class="browse-data item dir" data-filetyp="dir">../<div class="browse-data description">' + text.parent_dir + '</div></div>').appendTo(_this);
			$(element).click(function () {
				current_dir.splice(current_dir.length-1, 1);
				self.changedir(_this);
			});
		}
		if(!$.isEmptyObject(_current_dir)) {
			var _for_each_current_dir = function (i, v) {
				if(typeof v == 'number') {
					var enabled = true;
					var fileext = i.split('.');
					fileext = self.htmlentities(fileext[fileext.length-1]);
					if(acceptextension[requested_format] == undefined || acceptextension[requested_format].indexOf(fileext.toLowerCase()) == -1) enabled = false;
					self.add_file(i, v, enabled, _this);
				} else {
					var empty = self.empty(_current_dir[i], requested_format);
					self.addDir(i, $('.browse-data.content'), empty, false);
				}
			}; $.each(_current_dir, _for_each_current_dir);
		}
	};
	this.empty = function (dir_tree, requested_format)
	{
		if($.isEmptyObject(dir_tree))
		{
			return true;
		}
		else
		{
			var empty = true;
			var _for_each_dir_tree = function (i, v) {
				if(typeof v == 'object')
				{
					empty = self.empty(dir_tree[i]);
					if(empty == false) return false;
				} else {
					var fileext = i.split('.');
					fileext = self.htmlentities(fileext[fileext.length-1]);
					if(acceptextension[requested_format] == undefined || acceptextension[requested_format].indexOf(fileext.toLowerCase()) != -1) {
						empty = false;
						return false;
					}
				}
			}; $.each(dir_tree, _for_each_dir_tree);
			return empty;
		}
	}
	this.htmlentities = function (string) {
    	return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
	this.bytesToSize = function (bytes) {
		if(bytes == 0) return '0 Byte';
		var k = 1024;
		var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		var i = Math.floor(Math.log(bytes) / Math.log(k));
		return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
	}
	this.addDir = function (name, element, empty, prepend) {
		if(prepend != true) prepend = false;
		if(typeof element == 'string') element = $(element);
		var _this = element;
		var element = $('<div class="browse-data item dir" data-filetyp="dir">./' + name + '/</div>').appendTo(_this);
		
		if(prepend == false) {
			if(empty) element.addClass('disabled').append('<div class="browse-data description">' + text.empty_or_without_file.replace('%s', requested_format) + '</div>');
			else element.append('<div class="browse-data description">' + text.directory + '</div>');
		} else {
			if(empty) element.addClass('disabled').prepend('<div class="browse-data description">' + text.empty_or_without_file.replace('%s', requested_format) + '</div>');
			else element.prepend('<div class="browse-data description">' + text.directory + '</div>');
		}
		$(element).click(function () {
			if(_dir_updated == true)
			{
				self.display();
				_dir_updated = false;
			}
			current_dir.push(name)
			self.changedir(_this, current_dir);
		});
		return element;
	}
	this.current_dir = function () {
		return current_dir.join('/');
	}
	this.type = function () {
		return requested_format;
	}
	this.update = function () {
		_dir_updated = true;
	};
}
var file_browser = new file_browser();
tinymce.init({
    selector: "#edit-body",
    inline: true,
    plugins: [
        "autolink autoresize code contextmenu image link lists paste preview table pagebreak"
    ], // media
    pagebreak_seperator: '<!--pagebreak-->',
    file_browser_callback: file_browser.display,
    image_class_list: [
    	{title: 'Inline', value: 'inline'},
    	{title: 'Presentation collapsed', value: 'presentation-collapse'},
    	{title: 'Presentation', value: 'presentation'}
    ],
    toolbar: "pagebreak | undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
    relative_url: false,
    convert_urls: false
});
$(document).ready(function () {
	$('.editable.hidden').removeClass('hidden');
	$('.js-hide').addClass('hidden');
	$('form[data-create="form"]').submit(function () {
		$('*[data-create="headline-form"]').val(tinyMCE.get('edit-headline').getContent());
		$('*[data-create="text-form"]').val(tinyMCE.get('edit-body').getContent());
	});
	$('.ajax-submit').submit(function () {
		var _this = $(this);
		_this.children('input[name="parent_dirs"]').val(file_browser.get_current_dir());
		$.post(_this.attr('action'), _this.serializeArray(), function (data) {
			data = $.parseJSON(data);
			if(data.success==true)
			{
				var name = _this.children('input[name="dir"]').val();
				file_browser.addDir(name, '.browse-data.content', true, true);
				file_browser.update();
			}
		});
		event.preventDefault();
	});
});
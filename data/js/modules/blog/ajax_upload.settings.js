$(document).ready(function()
{
    var options = { 
        beforeSend: function() 
        {
        	$("#loader").show();
        	$("#loader > div").width('0%');
        },
        uploadProgress: function(event, position, total, percentComplete) 
        {
        	$("#loader > div").width(percentComplete+'%');
        },
        success: function() 
        {
            $("#loader > div").width('100%').fadeOut();
        },
        complete: function(response) 
        {
            console.log(response.responseText);
            var res = $.parseJSON(response.responseText);
            if(res.success == true) {
                file_browser.display();
            } else {
                for(var i = 0; i < res.messages.length; i++)
                {
                    file_browser.clear_messages();
                    file_browser.update_messages(res.messages[i]);
                }
            }
        },
        error: function()
        {
        	$("#message").html("<font color='red'> ERROR: unable to upload files</font>");
        }
    };

    $("#browse-data .ajax-upload").click(function () {
        $('input[name="directory"]', this).val(file_browser.current_dir());
        $('input[name="type"]', this).val(file_browser.type());
        $(this).ajaxForm(options)
    });

});
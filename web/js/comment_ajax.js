$(function(){
    $('.ajax-task-complete').on({
        click: function(e){
            var comment_id = $(this).attr('name');
            var dataArray = {
				'comment_id':	comment_id,
				'type':			'on'
	        };
            e.preventDefault();
            $.ajax({
                type: "post",
                url: "/ajax/removecmment.php",
                data: dataArray,
                dataType: "json",
                success: function(msg){
                    if (msg.res == '1'){
                        $('#comment_button_add').css('display', 'none');
                        $('#comment_button_change').css('display', 'block');
                        $('#is_commented').val(1);
                        $('#is_commented').attr('name',msg.comment_id);
                        $('#comment_edited').html(comment);
                        $('#button_comment_value').parent().css('display', 'block');
                        $('#button_comment_value').html($('#button_comment_value').html()*1 + 1);
                        generate_comment_button();
                    }else{
                        alert("Ooops! Some error.");
                    }
                },
                error: function() {
                    alert("Ooops! Some error.");
                }
            });
        }
    });
});

function vote_up(here){
    if ($("#user_id").length == 0){
        alert('Please register to vote!');
        return false;
    }
    if($(here).attr('name') == 1)
        return false;
    var vote = 1;
    var user_id = $('#user_id').val();
    var tikit_id = $(here).parent().attr('name');
    var dataString = "task=LikeWinery&vote="+vote+"&tikit_id="+tikit_id+"&user_id="+user_id;
    $.ajax({
        type: "post",
        url: "/addtikitvote/",
        data: dataString,
        dataType: "json",
        success: function(msg){
            $(here).attr('name', 1);
            $(here).attr('class', 'arrow down login-required checked');
            //window.location = "/myvivino/shop-to-do/?id="+id;
        },
        error: function() {}
    });
}

function vote_down(here){
    if ($("#user_id").length == 0){
        alert('Please register to vote!');
        return false;
    }
    if($(here).attr('name') == 1)
        return false;
    var vote = -1;
    var user_id = $('#user_id').val();
    var tikit_id = $(here).parent().attr('name');
    var dataString = "task=LikeWinery&vote="+vote+"&tikit_id="+tikit_id+"&user_id="+user_id;
    $.ajax({
        type: "post",
        url: "/addtikitvote/",
        data: dataString,
        dataType: "json",
        success: function(msg){
            $(here).attr('name', 1);
            $(here).attr('class', 'arrow down login-required checked');
            //window.location = "/myvivino/shop-to-do/?id="+id;
        },
        error: function() {}
    });
}
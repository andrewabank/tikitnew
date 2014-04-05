
function submit_usertext(){
    //$('#form-comment').submit();
    var comment = $('#comment').val();
    var user_id = $('#user_id').val();
    var tikit_id = $('#tikit_id').val();
    var dataString = "task=LikeWinery&comment="+comment+"&tikit_id="+tikit_id+"&user_id="+user_id;
    $.ajax({
        type: "post",
        url: "/addcomment/",
        data: dataString,
        dataType: "json",
        success: function(msg){
            //window.location = "/myvivino/shop-to-do/?id="+id;
        },
        error: function() {}
    });
}
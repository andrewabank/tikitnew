
function LoadDataFlag()
{
    this.value = false;
}

function showLoading() {
    document.getElementById('loadingmsg').style.display = 'block';
    document.getElementById('loadingover').style.display = 'block';
}
function hideLoading(){
    document.getElementById('loadingmsg').style.display = 'none';
    document.getElementById('loadingover').style.display = 'none';
}

var load_link_data = new LoadDataFlag();

$(document).ready(function() {
    $("a.load-linked-data").click(function(){
        var url = $("#tikit_tikit_url").val();
        loadData(url);
    });
    $("#asset_link input.link_url").keyup(function(e){
        if (e.keyCode == 13) {
            //loadLinkData( $("#asset_link"), load_link_data );
        }
    });

    /*$("#asset_post_wrapper").delegate('#asset_link_post', 'click', function() {
        postLinkData( $("#asset_link") );
    });*/
});

    function loadData(url)
    {
        var dataString = "url=" + url;
        $.ajax({
            type: "post",
            url: "/loadlinkdata/",
            data: dataString,
            dataType: "json",
            success: function(data){
                     if (data.status == 1) {
                         var $asset = $('.attach-asset');
                         displayLinkData($asset, data.data);
                         hideLoading()
                     } else {
                         alert(data.message);
                         hideLinkData($asset);
                     }
                //$(here).attr('name', 1);
                //$(here).attr('class', 'arrow down login-required checked');
                //window.location = "/myvivino/shop-to-do/?id="+id;
            }
            //error: function() {}
        });
    }

    function loadLinkData($asset, load_data)
    {
        if (load_data.value) return;

        var url = getLinkURL( $asset.find("div.attach-link") );
        if (url) {
            $asset.find("div.link-data-loading").show();
            load_data.value = true;

            if (cache_link_data.hasOwnProperty(url)) {
                displayLinkData($asset, cache_link_data[url], checkDataType);
                load_data.value = false;
            } else {
                var data = {
                    'signature' : $('#system-ajax-signature').val(),
                    'ownerid'   : $('#system-sessionowner-id').val(),
                    'url'       : url
                };

                $.post("/api/feeds/loadlinkdata", data, function(data, textStatus, jqXHR){
                     load_data.value = false;
                     if ((data.status == 1) || (data.code == 1)) {
                         cache_link_data[url] = data.data;
                         displayLinkData($asset, data.data, checkDataType);
                     } else if (data.status == 0) {
                         hideLinkData($asset);
                     } else {
                         hideLinkData($asset);
                         //Edutone.handleError("<?php echo $this->translate('UNKNOWN ERROR') ?>");
                     }
                 }, "json");
             }
        }
    }



    function displayLinkData($asset, data, checkDataType)
    {
        /*var valid = true;
        if ($.isFunction(checkDataType)) {
            valid = checkDataType.call(this, $asset, data);
        }
        */
        if (data) {
            var $link_data = $asset.find("div.link-data");
            $link_data.find("div.link-title").text(data.title);
            $link_data.find("div.link-url").text(data.url);
            $link_data.find("p.link-description").text(data.description);
            $link_data.find("input.link_type").val(data.type);

            if (data.hasOwnProperty('video') && (data.video != '')) {
                $link_data.find("input.link_video_url").val(data.video.url);
                $link_data.find("input.link_video_type").val(data.video.type);
                /*$link_data.find("input.link_video_width").val(data.video.width);
                $link_data.find("input.link_video_height").val(data.video.height);*/
            } else {
                $link_data.find("input.link_video_url").val('');
                $link_data.find("input.link_video_type").val('');
                /*$link_data.find("input.link_video_width").val('');
                $link_data.find("input.link_video_height").val('');*/
            }

            $link_data.find("a.remove-link-handler").show();
            $link_data.find("a.icon-prev-handler").
                removeClass('ui-state-default').
                addClass('ui-state-disabled');

            $link_data.find("div.link-image img").remove();
            if ((typeof(data.images) == 'object') && (data.images instanceof Array) && (data.images.length > 0)) {
                $.each(data.images, function(index, value){
                    $link_data.find("div.link-image").append('<img src="' + value + '" />');
                });
                $link_data.find("div.link-image img:first").show();

                if (data.images.length > 1) {
                    $link_data.find("a.icon-next-handler").
                        removeClass('ui-state-disabled').
                        addClass('ui-state-default');
                } else {
                    $link_data.find("a.icon-next-handler").
                        removeClass('ui-state-default').
                        addClass('ui-state-disabled');
                }

                $link_data.find("div.icon-count span:first").text('1');
                $link_data.find("div.icon-count span:last").text(data.images.length);
                $link_data.find("div.disable-icon").show();
                $link_data.find("div.icon-nav").show();
            } else {
                $link_data.find("div.icon-count span:first").text('0');
                $link_data.find("div.icon-count span:last").text('0');
                $link_data.find("div.disable-icon").hide();
                $link_data.find("div.icon-nav").hide();
                $link_data.find("div.link-image").hide();
                $link_data.addClass("no-icon");

                $link_data.find("a.choose-image-handler")
                    .removeClass("ui-state-active")
                    .addClass("ui-state-disabled");
                $link_data.find("a.upload-image-handler").addClass("ui-state-active");
                $link_data.find("form.link-form").show();
            }

            //$('.link-attach-asset').css('display','block');
            //alert(2);
            $asset.show();
            //$asset.hide();
            //$asset.find("div.link-data-loading").hide();
        }
    }

    function hideLinkData($asset)
    {
        $asset.find("div.attach-link").show();
        $asset.find("div.attach-asset").hide();
        $asset.find("div.link-data-loading").hide();

        var $link_data = $asset.find("div.link-data");
        $link_data.removeClass("no-icon");
        $link_data.find("div.link-image").show();
        $link_data.find("a.remove-link-handler").hide();

        $link_data.find("div.icon-nav").show();
        $link_data.find("a.choose-image-handler")
            .addClass("ui-state-active")
            .removeClass("ui-state-disabled");
        $link_data.find("a.upload-image-handler").removeClass("ui-state-active");
        $link_data.find("div.image-type").show();
        $link_data.find("div.disable-icon input").attr("checked", false);

        $link_data.find("form.link-form").hide();
        var $image = $link_data.find("form.link-form input.link_image").clone();
        $image.val("");
        $link_data.find("form.link-form input.link_image").replaceWith( $image );
    }


    function getCurrentLinkImage($link_image)
    {
        var $img = $link_image.find("img:visible");
        if ($img.length <= 0) {
            $link_image.find("img").each(function(){
                if (($(this).css("display") == 'inline') || ($(this).css("display") == 'block')) {
                    $img = $(this);
                }
            });
        }
        return $img;
    }


        $("div.asset-data a.icon-next-handler").click(function(){
            var $link_data = $(this).parents(".link-data");
            var $img = getCurrentLinkImage( $link_data.find("div.link-image") );
            if (!$(this).hasClass("ui-state-disabled") && ($img.nextAll().length > 0)) {
                $img.hide();
                var $current = $img.next();
                $current.show();
                var counter = $current.prevAll().length + 1;
                $link_data.find("div.icon-count span:first").text(counter);

                if ($current.nextAll().length <= 0) {
                    $link_data.find("a.icon-next-handler").
                        removeClass('ui-state-default').
                        addClass('ui-state-disabled');
                }
                if (counter == 2) {
                    $link_data.find("a.icon-prev-handler").
                        removeClass('ui-state-disabled').
                        addClass('ui-state-default');
                }
            }
        });

        $("div.asset-data a.icon-prev-handler").click(function(){
            var $link_data = $(this).parents(".link-data");
            var $img = getCurrentLinkImage( $link_data.find("div.link-image") );
            if (!$(this).hasClass("ui-state-disabled") && ($img.prevAll().length > 0)) {
                $img.hide();
                var $current = $img.prev();
                $current.show();
                $link_data.find("div.icon-count span:first").text($current.prevAll().length + 1);

                if ($current.prevAll().length <= 0) {
                    $link_data.find("a.icon-prev-handler").
                        removeClass('ui-state-default').
                        addClass('ui-state-disabled');
                }
                if ($current.nextAll().length == 1) {
                    $link_data.find("a.icon-next-handler").
                        removeClass('ui-state-disabled').
                        addClass('ui-state-default');
                }
            }
        });



        $('div.asset-data div.disable-icon input').change(function(){
            var $link_data = $(this).parents(".link-data");
            if ($(this).is(':checked')) {
                $link_data.find("div.link-image img:visible").hide().addClass("hide");
                $link_data.find("div.image-type").hide();
                $link_data.find("div.icon-nav").hide();
                $link_data.find("form.link-form").hide();
            } else {
                $link_data.find("div.link-image img.hide").removeClass("hide").show();
                $link_data.find("div.image-type").show();
                if ($link_data.find("a.choose-image-handler").hasClass("ui-state-active")) {
                    $link_data.find("div.icon-nav").show();
                } else {
                    $link_data.find("form.link-form").show();
                }
            }
        });




//http://victorjonsson.se/168/jquery-editable?from=jQuery

$('.link-title.editable').editable();
$('.link-description.editable').editable();

// There are some options you can configure when initiating
// the editable feature as well as a callback function that
// will be called when textarea gets blurred.
/*$('.link-title.editable').editable({
    touch : true, // Whether or not to support touch (default true)
    lineBreaks : true, // Whether or not to convert \n to <br /> (default true)
    toggleFontSize : true, // Whether or not it should be possible to change font size (defualt true)
    closeOnEnter : false, // Whether or not pressing the enter key should close the editor (default false)
    event : 'click', // The event that triggers the editor
    callback : function( data ) {
        // Callback that will be called once the editor looses focus
        if( data.content ) {
            // Content has changed...
        }
        if( data.fontSize ) {
            // the font size is changed
        }

        // data.$el gives you a reference to the element that was edited
        data.$el.effect('blink');
    }
});*/






/*

        $('.link-title.editable').editable({
            onEdit : function(content) {
                $("#tmp").html( this.find("input").val() );
                content.current = $("#tmp").text();
                this.find("input").val(content.current);
            },
            onSubmit : function(content) {
                this.text(content.current);
            }
        });
        $('.link-description.editable').editable({
            type : 'textarea',
            onEdit : function(content) {
                $("#tmp").html( this.find("textarea").val() );
                content.current = $("#tmp").text();
                this.find("textarea").val(content.current);
            },
            onSubmit : function(content) {
                this.text(content.current);
            }
        });
        $('.link-data .editable').hover(function() {
            if (!$(this).hasClass("hover"))
                $(this).addClass('hover');
        }, function() {
            if ($(this).hasClass("hover"))
                $(this).removeClass('hover');
        });

*/

    $("#asset_post_wrapper").delegate('#asset_link_post', 'click', function() {
        postLinkData( $("#asset_link") );
    });


    function postLinkData($asset)
    {
        var $link_data = $asset.find("div.link-data");

        var valid_link = $link_data.is(":visible");
        var $attach_link = $asset.find("div.attach-link");


        if (valid_link) {
            $link_data.find("form.link-form").ajaxSubmit({
                beforeSubmit : function (formData, jqForm, options) {
                    var image = '';
                    if (!$link_data.find("div.disable-icon input").is(':checked')) {
                        if ($link_data.find("a.choose-image-handler").hasClass("ui-state-active")) {
                            var $img = getCurrentLinkImage( $link_data.find("div.link-image") );
                            if ($img) {
                                image = $img.attr('src');
                            }

                            var $image = jqForm.find('input.link_image').clone();
                            $image.val('');
                            jqForm.find('input.link_image').replaceWith( $image );
                        }
                    }

                    var targets = new Array();
                    $('.asset.active .selected-target input[name=\"target\"]').each(function() {
                        targets.push( $(this).val() );
                    });
                    var target = targets.join(',');
                    var params = {
                        'status'  : $asset.find(".wallpost-input").val(),
                        'targets' : target,
                        'attachments[0][type]'              : '<?php echo WallService::LINK_ATTACH_TYPE; ?>',
                        'attachments[0][data][title]'       : $link_data.find("div.link-title").text(),
                        'attachments[0][data][url]'         : $link_data.find("div.link-url").text(),
                        'attachments[0][data][description]' : $link_data.find("p.link-description").text(),
                        'attachments[0][data][type]'        : $link_data.find("input.link_type").val(),
                        'attachments[0][data][image]'       : image
                    };

                    var $video_url = $link_data.find("input.link_video_url");
                    if (($video_url.length > 0) && ($video_url.val() != '')) {
                        params['attachments[0][data][data][video_url]'] = $video_url.val();
                        params['attachments[0][data][data][type]'] = $link_data.find("input.link_video_type").val();
                    }

                    options.extraData = params;
                    options.dataType = 'json';
                    options.iframe = true;

                    setTimeout(function(){
                        $.modal.close();
                        showLoading();
                    }, 100);
                },
                success : function(data, statusText, xhr, $form) {
                    $.modal.close();
                    //$(dialog).find(".simplemodal-close").click();
                    if (data.status == 1) {
                        alert("POST SUCCESSFULLY SAVED");
                        document.location.reload();
                    } else {
                        alert(data.message);
                    }
                }
            });
        }
    }


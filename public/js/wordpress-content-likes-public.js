(function($) {
    let finger = require('fingerprintjs2');
    var user;
    if (window.requestIdleCallback) {
        requestIdleCallback(function () {
            finger.get({}, function (components) {
                var values = components.map(function (component) { return component.value })
                var murmur = finger.x64hash128(values.join(''), 31)
                user = murmur;
                if (readCookie('hasVoted') === null) {
                    document.cookie = 'hasVoted' + '=' + murmur;
                }
            })
        })
    }

    let running = 'requestRunning';

    $(document).ready(function() {
       $('.social-likes').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var postid;
            var pageid;
            var disabled;
            var $button = $(this);

            clicktype = $button.attr('clicktype');
            disabled = $(e.target).closest('a');

            if (disabled.data(running) ) {
                return;
            }

            disabled.data(running, true);

            if ( $('body[class*="postid"]').length){
                 postid = $('body[class*="postid"]').attr('class').split('postid-');
                 postid = postid[1].split(" ")[0];
            }

            if ( $('body[class*="page-id"]').length){
                 pageid = $('body[class*="page-id"]').attr('class').split('page-id-');
                 pageid = pageid[1].split(" ")[0];
            }

            var likedata = {
                'action': '_s_likebtn__handler',
                'content_like_id': postid ? postid : pageid,
                'uniq' : user
            };

            jQuery.post({
                url : ajax_data.ajax_url,
                type : 'POST',
                data : likedata,
                dataType: 'json',
                success : function( response ){
                    response = Number(response);
                    if (response >= 1) {
                     $('.likes-count').text(response);
                     $('.likes-count').show();
                     $('.social-likes').toggleClass('active');
                    } else {
                        $('.likes-count').hide()
                        $('.social-likes').toggleClass('active');
                    }
                },
                complete: function(){
                    disabled.data(running, false);
                }
            });
        });
     });

 function createCookie(name, value) {
    var expires = '',
        date = new Date();
    if (days) {
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toGMTString();
    }
    document.cookie = name + '=' + value;
}

function readCookie(name) {
    var nameEQ = name + '=',
        allCookies = document.cookie.split(';'),
        i,
        cookie;
    for (i = 0; i < allCookies.length; i += 1) {
        cookie = allCookies[i];
        while (cookie.charAt(0) === ' ') {
            cookie = cookie.substring(1, cookie.length);
        }
        if (cookie.indexOf(nameEQ) === 0) {
            return cookie.substring(nameEQ.length, cookie.length);
        }
    }
    return null;
}

}) (jQuery);

<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       www.gulosolutions.com
 * @since      1.0.0
 *
 * @package    Wordpress_Content_Likes
 * @subpackage Wordpress_Content_Likes/public/partials
 */

// <!-- This file should primarily consist of HTML with a little bit of PHP. -->


function _s_like_button()
{
    $content = <<<EOS
<a role="button" clicktype=0 class="social social-likes">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     width="35px" height="35px" viewBox="0 0 37 37" style="enable-background:new 0 0 37 37;" xml:space="preserve">
                <g id="Page-1">
                    <g id="Asn-blog-individual" transform="translate(-246.000000, -1423.000000)">
                        <g id="share" transform="translate(246.000000, 1250.000000)">
                            <g id="like" transform="translate(1.000000, 171.000000)">
                                <g id="np_heart_888700_000000" transform="translate(0.000000, 3.000000)">
                                    <g id="fb-copy" transform="translate(0.000000, 0.000000)">
                                        <path id="icon-bg-" class="st0 outline" d="M17.5-0.5c-9.9,0-18,8.1-18,18c0,9.9,8.1,18,18,18s18-8.1,18-18
                                            C35.5,7.6,27.4-0.5,17.5-0.5L17.5-0.5z"/>
                                    </g>
                                    <path id="Path" class="st1 form" d="M26.9,12c-0.6-1.2-4.7-5.2-9.4,0.6c-4.9-5.8-8.8-1.8-9.4-0.6c-1.2,2.2-0.5,5.6,1.2,7.2l8.2,8.4
                                        l8.2-8.4C27.4,17.5,28.1,14.2,26.9,12L26.9,12z"/>
                                </g>
                            </g>
                        </g>
                    </g>
                </g>
            </svg>
        </a>
EOS;
    return $content;
}

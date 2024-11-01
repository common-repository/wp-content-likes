<?php

class QueryContent
{
    public static function getPostsLikes()
    {
        global $wpdb;
        $pref = $wpdb->prefix;

        $query = "SELECT SUM({$pref}postmeta.meta_value) AS LIKES from {$pref}postmeta
            LEFT JOIN {$pref}posts  on {$pref}posts.ID = {$pref}postmeta.post_id
            where {$pref}postmeta.meta_key = 'likes'
            and {$pref}posts.post_type = 'post'";

        $the_sum_posts = $wpdb->get_row($query);

        return $the_sum_posts;
    }

    public static function getCustomPostsLikes()
    {
        global $wpdb;
        $pref = $wpdb->prefix;

        $custom_post_types = (QueryContent::getCustomPosts());

        $post_types = QueryContent::getCustomPosts();

        $custom_posts = implode("','", $post_types);
        $custom_posts = "'".$custom_posts."'";

        $query = "SELECT SUM(meta_value) AS LIKES, POST_TITLE from {$pref}postmeta
            LEFT JOIN {$pref}posts  on {$pref}posts.ID = {$pref}postmeta.post_id
            WHERE meta_key = 'likes'
            and {$pref}posts.post_type IN ({$custom_posts})
            GROUP BY POST_TITLE";

        $the_sum = $wpdb->get_row($query);

        return $the_sum;
    }

    public static function getPagesLikes()
    {
        global $wpdb;
        $pref = $wpdb->prefix;

        $query = "SELECT SUM({$pref}postmeta.meta_value) AS LIKES from {$pref}postmeta
            LEFT JOIN {$pref}posts  on {$pref}posts.ID = {$pref}postmeta.post_id
            WHERE {$pref}postmeta.meta_key = 'likes'
            and {$pref}posts.post_type = 'page'";

        $the_max_pages = $wpdb->get_row($query);

        return $the_max_pages;
    }

    public static function getCustomPosts()
    {
        $args = array(
            'public' => true,
            '_builtin' => false,
            'post__not_in' => array('page', 'post', 'attachment'),
         );

        $output = 'names';

        $post_types = get_post_types($args, $output);

        return $post_types;
    }
}


$custom_post_types = (QueryContent::getCustomPosts());

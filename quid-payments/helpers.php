<?php

namespace QUIDHelperFunctions {

    function getSiteTitle() {
        $blogTitle = get_bloginfo('name');
        if (strlen($blogTitle) < 5) {
            $blogTitle .= " website";
        }
        return $blogTitle;
    }

    function getPostTitle($post) {
        $postTitle = get_the_title($post);
        if (strlen($postTitle) < 5) {
            $postTitle .= " post";
        }
        return $postTitle;
    }

    function getPostSlug($post) {
        $postSlug = $post->post_name;
        if (strlen($postSlug) < 5) {
            $postSlug .= "-post";
        }
        return $postSlug;
    }

    function getPostURL($post) {
        $permalink = get_permalink($post);
        if ($permalink == "") {
            return site_url();
        }
        return $permalink;
    }
    
}

?>
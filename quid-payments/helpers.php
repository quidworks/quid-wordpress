<?php

namespace QUIDHelperFunctions {

    function getSiteTitle() {
        $blogTitle = get_bloginfo('name');
        if (strlen($blogTitle) < 5) {
            $blogTitle .= "-website";
        }
        return $blogTitle;
    }

    function getSiteTitleSlug() {
        $blogTitle = strtolower(html_entity_decode(get_bloginfo('name'), ENT_QUOTES | ENT_XML1, 'UTF-8'));
        $blogTitleSlug = preg_replace('/[^a-z0-9\. -]/', '', $blogTitle);
        $blogTitleSlug = preg_replace('/  */', '-', $blogTitleSlug);
        if (strlen($blogTitleSlug) < 5) {
            $blogTitleSlug .= "-website";
        }
        return $blogTitleSlug;
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

    function buttonAlignment($alignArg) {

        if (!isset($alignArg)) {
            if ($alignOption == "") $alignOption = 'right';
            $alignArg = $alignOption;
        }

        if ($alignArg == 'center') return 'center';
        if ($alignArg == 'left') return 'flex-start';
        if ($alignArg == 'right') return 'flex-end';

        return 'flex-end';
    }
    
}

?>
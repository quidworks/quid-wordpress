<?php

namespace QUIDPaymentsPost {

    use QUIDPaymentsInputs as Inputs;

    class Post {

        function filterPostContent($content) {
            global $post;
            $inputs = new Inputs\Inputs();
            if ($post->post_type != 'post') return $content;
            $type = get_post_meta($post->ID, 'quid_field_type', true);
            $input = get_post_meta($post->ID, 'quid_field_input', true);
            if ($type == "Required") {
                if ($input == "Buttons") return $inputs->quidButton([]);
                else return $inputs->quidSlider([]);
            } else if ($type == "Optional") {
                if ($input == "Buttons") return $content.$inputs->quidButton([]);
                else return $content.$inputs->quidSlider([]);
            } else {
                return $content;
            }
        }
    
    }

}

?>
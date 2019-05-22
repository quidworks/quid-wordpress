<?php

namespace QUIDPaymentsPost {

    use QUIDPaymentsInputs as Inputs;
    use QUIDPaymentsMeta as Meta;

    class Post {

        function filterPostContent($content) {
            global $post;
            $inputs = new Inputs\Inputs();

            if ($post->post_type != 'post') return $content;

            $type = get_post_meta($post->ID, 'quid_field_type', true);
            $input = get_post_meta($post->ID, 'quid_field_input', true);

            if ($type == "Required") {
                if ($input == "Buttons") return $this->handleButtonWithExcerpt();
                return $this->handleSliderWithExcerpt();
            } else if ($type == "Optional") {
                if ($input == "Buttons") return $this->handleButtonWithoutExcerpt($content);
                return $this->handleSliderWithoutExcerpt($content);
            } else {
                return $content;
            }
        }

        function handleSliderWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}"><p id="{$post->ID}-excerpt">{$post->post_excerpt}</p>
HTML;

            $html .= $inputs->quidSlider($meta);
            $html .= `</div>`;

            return $html;
        }

        function handleSliderWithoutExcerpt(&$content) {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;

            $html .= $content.$inputs->quidSlider($meta);
            $html .= `</div>`;
            return $html;
        }

        function handleButtonWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}"><p id="{$post->ID}-excerpt">$post->post_excerpt</p>
HTML;

            $html .= $inputs->quidButton($meta);
            $html .= `</div>`;
            return $html;
        }

        function handleButtonWithoutExcerpt(&$content) {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;

            $html .= $content.$inputs->quidButton($meta);
            $html .= `</div>`;
            return $html;
        }
    
    }

}

?>
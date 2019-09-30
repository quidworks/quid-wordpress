<?php

namespace QUIDPaymentsPost {

    use QUIDPaymentsInputs as Inputs;
    use QUIDPaymentsMeta as Meta;
    use QUIDHelperFunctions as Helpers;

    class Post {

        function filterPostContent($content) {
            global $post;
            global $pagenow;
            $inputs = new Inputs\Inputs();

            if ($pagenow == 'post.php') return $content;
            if ($post->post_type != 'post') return $content;

            $postCategoriesArray = get_the_category($post);
            
            $categorySlug = $postCategoriesArray[0]->slug;

            $categoryOptionsJSON = get_option('quid-category-options');
            $categoryOptions = json_decode($categoryOptionsJSON, true);
            if (isset($categoryOptions[$categorySlug])) {
                $this->postCategoryOptions = $categoryOptions[$categorySlug];
            } else {
                $this->postCategoryOptions = null;
            }

            $type = get_post_meta($post->ID, 'quid_field_type', true);
            $input = get_post_meta($post->ID, 'quid_field_input', true);

            if ($type == "Required") {
                if ($input == "Buttons") return $this->handleButtonWithExcerpt();
                return $this->handleSliderWithExcerpt();
            } else if ($type == "Optional") {
                if ($input == "Buttons") return $this->handleButtonWithoutExcerpt($content);
                return $this->handleSliderWithoutExcerpt($content);
            } else {
                if ($this->postCategoryOptions['payment-type'] === "Required") {
                    if ($this->postCategoryOptions['input-type'] == "Buttons") return $this->handleButtonWithExcerpt();
                    return $this->handleSliderWithExcerpt();
                } else if ($this->postCategoryOptions['payment-type'] == "Optional") {
                    if ($this->postCategoryOptions['input-type'] == "Buttons") return $this->handleButtonWithoutExcerpt($content);
                    return $this->handleSliderWithoutExcerpt($content);
                } else {
                    return $content;
                }
            }
        }

        function handleSliderWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);
            
            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty($meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['payment-type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['min'] = $this->postCategoryOptions['min'];
                $meta['max'] = $this->postCategoryOptions['max'];
                $meta['initial'] = $this->postCategoryOptions['initial'];
            }

            $justification = Helpers\buttonAlignment($meta['align']);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$meta['id']}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$meta['id']}" style="display: none;">Read More</button>
                    </div>
HTML;

            $html .= $inputs->quidSlider($meta, true);
            $html .= '</div>';

            return $html;
        }

        function handleSliderWithoutExcerpt(&$content) {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            // Debug
            error_log(print_r($meta, true));
            error_log($this->postCategoryOptions !== null);
            error_log(gettype($meta['type']));
            error_log(empty($meta['type']));
            // Debug

            if ($meta['type'] === "None" || empty($meta['type'])) {
                if ($this->postCategoryOptions !== null) {
                    error_log('Using category settings');
                    $meta['type'] = $this->postCategoryOptions['payment-type'];
                    $meta['text'] = $this->postCategoryOptions['text'];
                    $meta['paid'] = $this->postCategoryOptions['paid-text'];
                    $meta['min'] = $this->postCategoryOptions['min'];
                    $meta['max'] = $this->postCategoryOptions['max'];
                    $meta['initial'] = $this->postCategoryOptions['initial'];
                    $meta['location'] = $this->postCategoryOptions['location'];
                    $meta['locations'] = $this->postCategoryOptions['locations'];
                } else {
                    return $content;
                }
            }

            error_log(print_r($meta, true));
            $newContent = $content;

            if ($meta['locations']['top']) {
                $newContent = $inputs->quidSlider($meta, true) . $newContent;
            }

            if ($meta['locations']['nearTop']) {
                $postLength = strlen($newContent);
                $locationCharacters = round($postLength * 0.05);
                $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
            }

            if ($meta['locations']['nearMiddle']) {
                $postLength = strlen($newContent);
                $locationCharacters = round($postLength * 0.50);
                $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
            }

            if ($meta['locations']['nearBottom']) {
                $postLength = strlen($newContent);
                $locationCharacters = round($postLength * 0.95);
                $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
            }

            if ($meta['locations']['bottom']) {
                $newContent .= $inputs->quidSlider($meta, true);
            }

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;
            $html .= $newContent;
            $html .= `</div>`;
            return $html;
        }

        function handleButtonWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty($meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['payment-type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['price'] = $this->postCategoryOptions['price'];
            }

            $justification = Helpers\buttonAlignment($meta['align']);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$meta['id']}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$meta['id']}" style="display: none;">Read More</button>
                    </div>
HTML;

            $html .= $inputs->quidButton($meta, true);
            $html .= `</div>`;
            return $html;
        }

        function handleButtonWithoutExcerpt(&$content) {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty(meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['payment-type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['price'] = $this->postCategoryOptions['price'];
                $meta['location'] = $this->postCategoryOptions['location'];
                $meta['locations'] = $this->postCategoryOptions['locations'];
            }

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;

            if ($meta['location'] === "Top" || $meta['location'] === "Both") {
                $html .= $inputs->quidButton($meta, true);
            }

            $html .= $content;

            if ($meta['location'] === "Bottom" || $meta['location'] === "Both") {
                $html .= $inputs->quidButton($meta, true);
            }

            $html .= `</div>`;
            return $html;
        }
    
    }

}

?>
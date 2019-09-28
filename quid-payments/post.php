<?php

namespace QUIDPaymentsPost {

    use QUIDPaymentsInputs as Inputs;
    use QUIDPaymentsMeta as Meta;
    use QUIDHelperFunctions as Helpers;

    class Post {

        function filterPostContent($content) {
            global $post;
            $inputs = new Inputs\Inputs();

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
            
            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty(meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['payment-type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['min'] = $this->postCategoryOptions['min'];
                $meta['max'] = $this->postCategoryOptions['max'];
                $meta['initial'] = $this->postCategoryOptions['initial'];
                $meta['location'] = $this->postCategoryOptions['location'];
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

            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty($meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['payment-type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['min'] = $this->postCategoryOptions['min'];
                $meta['max'] = $this->postCategoryOptions['max'];
                $meta['initial'] = $this->postCategoryOptions['initial'];
                $meta['location'] = $this->postCategoryOptions['location'];
            }

            $postLength = strlen($content);
            $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
            error_log('Length: ' . $postLength . '; 10%: ' . $tenPercent . '; 90%: ' . $nintyPercent . '; Near top: ' . $nearTop . '; Near bottom: ' . $nearBottom);

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;

            if ($meta['location'] === "Top" ) {
                $html .= $inputs->quidSlider($meta, true);
            }

            if ($meta['location'] === "Near Top" ) {
                $fivePercentLocation = round($postLength * 0.05);
                $nearTop = strpos($content, '</p>', $fivePercentLocation);
                $newContent = substr_replace($content, $replacementString, $nearTop, 4);
            } else if ($meta['location'] === "Near Bottom" ) {
                $nintyFivePercentLocation = round($postLength * 0.95);
                $nearBottom = strpos($content, '</p>', $nintyFivePercentLocation);
                $newContent = substr_replace($content, $replacementString, $nearBottom, 4);
            } else {
                $newContent = $content;
            }

            $html .= $newContent;

            if ($meta['location'] === "Bottom") {
                $html .= $inputs->quidSlider($meta, true);
            }

            $html .= `</div>`;
            return $html;
        }

        function handleButtonWithExcerpt() {
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
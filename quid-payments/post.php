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
            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($pagenow == 'post.php') return $content;
            if ($post->post_type != 'post') return $content;

            if ($meta['type'] == "Required") {
                if ($meta['input'] == "Buttons") {
                    return $this->handleButtonWithExcerpt();
                } else {
                    return $this->handleSliderWithExcerpt();
                }
            } else if ($meta['type'] == "Optional") {
                if ($meta['input'] == "Buttons") {
                    return $this->handleButtonWithoutExcerpt($content);
                } else {
                    return $this->handleSliderWithoutExcerpt($content);
                }
            } else {
                return $content;
            }
        }

        function handleSliderWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);
            
            error_log('QUID: handleSliderWithExcerpt');

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

            error_log('QUID: handleSliderWithoutExcerpt');

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($meta['type'] == 'None' or empty($meta['type'])) {
                return $content;
            }

            $newContent = $content;

            if (isset($meta['locations']['top'])) {
                if ($meta['locations']['top']) {
                    $newContent = $inputs->quidSlider($meta, true) . $newContent;
                }
            }

            if (isset($meta['locations']['nearTop'])) {
                if ($meta['locations']['nearTop']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.05);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['nearMiddle'])) {
                if ($meta['locations']['nearMiddle']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.50);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['nearBottom'])) {
                if ($meta['locations']['nearBottom']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.95);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidSlider($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['bottom'])) {
                if ($meta['locations']['bottom']) {
                    $newContent .= $inputs->quidSlider($meta, true);
                }
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

            error_log('QUID: handleButtonWithExcerpt');

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($this->postCategoryOptions !== null && ($meta['type'] === "None" || empty($meta['type']))) {
                $meta['type'] = $this->postCategoryOptions['type'];
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

            error_log('QUID: handleButtonWithoutExcerpt');

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($meta['type'] == 'None' or empty($meta['type'])) {
                return $content;
            }

            $newContent = $content;

            if (isset($meta['locations']['top'])) {
                if ($meta['locations']['top']) {
                    $newContent = $inputs->quidButton($meta, true) . $newContent;
                }
            }

            if (isset($meta['locations']['nearTop'])) {
                if ($meta['locations']['nearTop']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.05);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidButton($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['nearMiddle'])) {
                if ($meta['locations']['nearMiddle']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.50);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidButton($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['nearBottom'])) {
                if ($meta['locations']['nearBottom']) {
                    $postLength = strlen($newContent);
                    $locationCharacters = round($postLength * 0.95);
                    $locationParagraph = strpos($newContent, '</p>', $locationCharacters);
                    $replacementString = '</p>' . $inputs->quidButton($meta, true); 
                    $newContent = substr_replace($newContent, $replacementString, $locationParagraph, 4);
                }
            }

            if (isset($meta['locations']['bottom'])) {
                if ($meta['locations']['bottom']) {
                    $newContent .= $inputs->quidButton($meta, true);
                }
            }

            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$meta['id']}">
HTML;
            $html .= $newContent;
            $html .= `</div>`;
            return $html;
        }
    
    }

}

?>
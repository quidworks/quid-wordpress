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

            $readMore = get_option('quid-read-more');
            if ($meta['type'] == "Required" || $readMore) {
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
            
            if (isset($meta['align'])) {
                $alignOption = $meta['align'];
            } else {
                $alignOption = get_option('quid-align');
            }
            $justification = Helpers\buttonAlignment($alignOption);

            $html = <<<HTML
            <div style="width: 100%" id="post-container-{$post->ID}">
                <div style="width: 100%;" id="post-content-{$post->ID}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$post->ID}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$post->ID}" style="display: none;">Read More</button>
                    </div>
HTML;
            $html .= '</div>';
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
                <div style="width: 100%;" id="post-content-{$post->ID}">
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

            if (isset($meta['align'])) {
                $justification = Helpers\buttonAlignment($meta['align']);
            } else {
                $justification = Helpers\buttonAlignment();
            }

            $html = <<<HTML
            <div style="width: 100%" id="post-container-{$post->ID}">
                <div style="width: 100%;" id="post-content-{$post->ID}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$post->ID}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$post->ID}" style="display: none;">Read More</button>
                    </div>
HTML;
            $html .= '</div>';
            $html .= $inputs->quidButton($meta, true);
            $html .= '</div>';
            return $html;
        }

        function handleButtonWithoutExcerpt(&$content) {
            global $post;
            $inputs = new Inputs\Inputs();

            error_log('QUID: handleButtonWithoutExcerpt');

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            $newContent = $content;

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

            error_log('$post->ID: ' . $post->ID);
            $html = <<<HTML
                <div style="width: 100%;" id="post-content-{$post->ID}">
HTML;

            $html .= $newContent;
            $html .= `</div>`;
            return $html;
        }
    
    }

}

?>
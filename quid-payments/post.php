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

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            if ($categoriesOverride ) {
                $handlerResult = $this->categoryHandler();
                if ($handlerResult !== null) return $handlerResult;
                $handlerResult = $this->metaHandler($meta);
                if ($handlerResult !== null) return $handlerResult;
            } else {
                $handlerResult = $this->metaHandler($meta);
                if ($handlerResult !== null) return $handlerResult;
                $handlerResult = $this->categoryHandler();
                if ($handlerResult !== null) return $handlerResult;
            }
            
            return $content;
        }

        function metaHandler($meta) {
            if ($meta['type'] === "Required" || $meta['type'] === "Optional") {
                if ($meta['input'] === "Buttons") return $this->handleButtonWithExcerpt();
                if ($meta['input'] === "Slider") {
                    return $this->handleSliderWithExcerpt();
                }
            }
            return null;
        }

        function categoryHandler() {
            if ($this->postCategoryOptions['input-type'] === "Required" || $this->postCategoryOptions['input-type'] === "Optional") {
                if ($this->postCategoryOptions['input-type'] === "Buttons") return $this->handleButtonWithExcerpt();
                if ($this->postCategoryOptions['input-type'] === "Slider") return $this->handleSliderWithExcerpt();
            }
            return null;
        }

        function handleSliderWithExcerpt() {
            global $post;
            $inputs = new Inputs\Inputs();

            $metaInstance = new Meta\Meta();
            $meta = $metaInstance->getMetaFields($post);

            error_log('QUID: handleSliderWithExcerpt');
            
            if ($this->postCategoryOptions !== null && $meta['type'] === "None") {
                $meta['type'] = $this->postCategoryOptions['type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['price'] = $this->postCategoryOptions['price'];
            }

            $justification = Helpers\buttonAlignment($meta['align']);

            $html = <<<HTML
            <div style="width: 100%" id="post-container-{$meta['id']}">
                <div style="width: 100%;" id="post-content-{$meta['id']}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$meta['id']}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$meta['id']}" style="display: none;">Read More</button>
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

            if ($this->postCategoryOptions !== null && $meta['type'] === "None") {
                $meta['type'] = $this->postCategoryOptions['type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['price'] = $this->postCategoryOptions['price'];

                $newContent = $content.$inputs->quidSlider($meta, true);
            } else {

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
            <div style="width: 100%" id="post-container-{$meta['id']}">
                <div style="width: 100%;" id="post-content-{$meta['id']}">
                    <p>{$post->post_excerpt}</p>
                    <p id="read-more-content-{$meta['id']}" style="display: none;"></p>
                    <div style="display: flex; justify-content: {$justification}">
                        <button class="quid-pay-button quid-pay-button-default" id="read-more-button-{$meta['id']}" style="display: none;">Read More</button>
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

            if ($this->postCategoryOptions !== null && $meta['type'] === "None") {
                $meta['type'] = $this->postCategoryOptions['type'];
                $meta['text'] = $this->postCategoryOptions['text'];
                $meta['paid'] = $this->postCategoryOptions['paid-text'];
                $meta['price'] = $this->postCategoryOptions['price'];

                $newContent = $content.$inputs->quidButton($meta, true);
            } else {
                
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
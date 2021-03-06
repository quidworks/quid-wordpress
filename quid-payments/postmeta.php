<?php

namespace QUIDPaymentsMeta {

    use QUIDHelperFunctions as Helpers;

    class Meta {

        public function getMetaFields($post) {

            global $pagenow;

            $postCategoriesArray = get_the_category($post);
            $categorySlug = $postCategoriesArray[0]->slug;
            $categorySettings = json_decode(get_option('quid-category-options'), true);
            if (isset($categorySettings[$categorySlug])) {
                if (!isset($categorySettings[$categorySlug]['category-enabled'])) {
                    $categoryEnabled = "Off";
                } else {
                    $categoryEnabled = $categorySettings[$categorySlug]['category-enabled'];
                }
            }
            if (isset($categorySettings[$categorySlug])) {
                if (!isset($categorySettings[$categorySlug]['post-override'])) {
                    $postOverride = "Off";
                } else {
                    $postOverride = $categorySettings[$categorySlug]['post-override'];
                }
            }

            $postSettings = [];
            $postHasSettings = get_post_meta($post->ID, 'quid_post_settings', true) || get_post_meta($post->ID, 'quid_field_type', true) ? true : false;

            if ($categoryEnabled == "On" && $postOverride == "On" && $pagenow != 'post.php') {
                $postSettings = $categorySettings[$categorySlug];
                $postSettings['settingSource'] = "Category and post override";
            } else if ($categoryEnabled == "On" && $postOverride == "Off" && !$postHasSettings && $pagenow != 'post.php') {
                $postSettings = $categorySettings[$categorySlug];
                $postSettings['settingSource'] = "Category and no post settings";
            } else {
                $postSettings = json_decode(get_post_meta($post->ID, 'quid_post_settings', true), true);
                if ( empty($postSettings)) {
                    $postSettings = array(
                        "type" => get_post_meta($post->ID, 'quid_field_type', true),
                        "input" => get_post_meta($post->ID, 'quid_field_input', true),
                        "text" => get_post_meta($post->ID, 'quid_field_text', true),
                        "paid" => get_post_meta($post->ID, 'quid_field_paid', true),
                        "price" => get_post_meta($post->ID, 'quid_field_price', true),
                        "min" => get_post_meta($post->ID, 'quid_field_min', true),
                        "max" => get_post_meta($post->ID, 'quid_field_max', true),
                        "initial" => get_post_meta($post->ID, 'quid_field_initial', true),
                        "locations" => json_decode(get_post_meta($post->ID, 'quid_field_locations', true), true),
                    );
                    $postSettings['settingSource'] = "Old post metadata";
                } else {
                    $postSettings['settingSource'] = "New post json";
                }
            }
            $postSettings['id'] = $post->ID;
            return $postSettings;
        }

        public function addMetaFields() {
            $screens = ['post', 'wporg_cpt'];
            foreach ($screens as $screen) {
                add_meta_box(
                    'wporg_box_id',           // Unique ID
                    'QUID - Post Settings',      // Box title
                    array( $this, 'renderMetaFieldsTemplate' ),  // Content callback, must be of type callable
                    $screen                   // Post type
                );
            }
        }

        public function renderMetaFieldsTemplate($post) {
            global $quidPluginVersion;
            $meta = $this->getMetaFields($post);

            $buttonsReadOnly = "";
            $sliderReadOnly = "";
            $requiredReadOnly = "";

            if ($meta['input'] == 'Buttons') {
                $buttonsReadOnly = "readonly";
            } else if ($meta['input'] == 'Slider') {
                $sliderReadOnly = "readonly";
            }

            if ($meta['type'] == 'Required') {
                $requiredReadOnly = "readonly";
            }
            
            wp_register_style( 'css_quid_meta', plugins_url( 'css/meta.css?quid-plugin='.$quidPluginVersion, __FILE__ ) );
            wp_enqueue_style( 'css_quid_meta' );

            wp_register_script( 'js_quid_meta_'.$meta['id'], plugins_url( 'js/meta.js?quid-plugin='.$quidPluginVersion, __FILE__ ) );
            wp_localize_script( 'js_quid_meta_'.$meta['id'], 'dataMetaJS', null );
            wp_enqueue_script( 'js_quid_meta_'.$meta['id'] );
            ?>
            <div class="quid-post-meta">
                <div>
                    <label>Payment Type</label>
                    <select onchange="quidPostMeta.handlePaymentTypeChange(this)" name="quid_field_type">
                        <option <?php if ($meta['type'] == "None") echo "selected"; ?> value="None">None</option>
                        <option <?php if ($meta['type'] == "Required") echo "selected"; ?> value="Required">Required</option>
                        <option <?php if ($meta['type'] == "Optional") echo "selected"; ?> value="Optional">Optional</option>
                    </select>
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Input Type</label>
                    <select onchange="quidPostMeta.handleInputTypeChange(this)" name="quid_field_input">
                        <option class="quid-button-option" <?php if ($meta['input'] == "Buttons") echo "selected"; ?> value="Buttons">Buttons</option>
                        <option class="quid-slider-option" <?php if ($meta['input'] == "Slider") echo "selected"; ?> value="Slider">Slider</option>
                    </select>
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Button Text</label>
                    <input name="quid_field_text" placeholder="Button Text" value="<?php echo $meta['text'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Paid Text</label>
                    <input name="quid_field_paid" placeholder="Paid Button Text" value="<?php echo $meta['paid'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Price</label>
                    <input class="quid-post-meta-button-only" <?php echo $sliderReadOnly ?> onkeyup="quidPostMeta.handlePriceKeypress(event)"
                        name="quid_field_price" placeholder="Price ($0.01 - $10)" type="number" step="0.01" value="<?php echo $meta['price'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Minimum Slider Value</label>
                    <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handleMinKeypress(event)"
                        name="quid_field_min" placeholder="Min Amount ($0.01 or more)" type="number" step="0.01" value="<?php echo $meta['min'] != '' ? $meta['min'] : '0.01' ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Maximum Slider Value</label>
                    <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handleMaxKeypress(event)"
                        name="quid_field_max" placeholder="Max Amount ($10 or less)" type="number" step="0.01" value="<?php echo $meta['max'] != '' ? $meta['max'] : '10.00' ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Initial Slider Value</label>
                    <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handlePriceKeypress(event)"
                        name="quid_field_initial" placeholder="Initial Amount ($0.01 - $10)" type="number" step="0.01" value="<?php echo $meta['initial'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div class="quid-post-locations">
                    <label>Locations within post body (optional payments only)</label>
                    <div class="quid-post-checkbox-container">
                        <div class="quid-post-checkbox-label">Top</div><input class="quid-post-meta-optional-only" name="quid_field_locations[top]" value=true type="checkbox"
                            <?php echo $requiredReadOnly ?> <?php if (isset($meta['locations']['top'])) { if ($meta['locations']['top']) { echo 'checked'; } } ?> />
                        <div class="quid-post-checkbox-label">Near top</div><input class="quid-post-meta-optional-only" name="quid_field_locations[nearTop]" value=true type="checkbox"
                            <?php echo $requiredReadOnly ?> <?php if (isset($meta['locations']['nearTop'])) { if ($meta['locations']['nearTop']) { echo 'checked'; } } ?> />
                        <div class="quid-post-checkbox-label">Near middle</div><input class="quid-post-meta-optional-only" name="quid_field_locations[nearMiddle]" value=true type="checkbox"
                            <?php echo $requiredReadOnly ?> <?php if (isset($meta['locations']['nearMiddle'])) { if ($meta['locations']['nearMiddle']) { echo 'checked'; } } ?> />
                        <div class="quid-post-checkbox-label">Near bottom</div><input class="quid-post-meta-optional-only" name="quid_field_locations[nearBottom]" value=true type="checkbox"
                            <?php echo $requiredReadOnly ?> <?php if (isset($meta['locations']['nearBottom'])) { if ($meta['locations']['nearBottom']) { echo 'checked'; } } ?> />
                        <div class="quid-post-checkbox-label">Bottom</div><input class="quid-post-meta-optional-only" name="quid_field_locations[bottom]" value=true type="checkbox"
                            <?php echo $requiredReadOnly ?> <?php if (isset($meta['locations']['bottom'])) { if ($meta['locations']['bottom']) { echo 'checked'; } } ?> />
                    </div>
                </div>
                <div style="display: none;">
                    <label>Product ID</label>
                    <input name="quid_field_id" readonly placeholder="Product ID" value="<?php echo Helpers\getPostSlug($post); ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div style="display: none;">
                    <label>Product Name</label>
                    <input name="quid_field_name" readonly placeholder="Name" value="<?php echo Helpers\getPostTitle($post) ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div style="display: none;">
                    <label>Product Description</label>
                    <input name="quid_field_description" readonly placeholder="Description" value="<?php echo Helpers\getSiteTitle() ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div style="display: none;">
                    <label>Product URL</label>
                    <input name="quid_field_url" readonly placeholder="URL" value="<?php echo get_permalink($post) ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
            </div>
            <?php
        }

        public function save_postdata($post_id) {
            $this->maxAndMinMustBeDifferent($_POST['quid_field_min'], $_POST['quid_field_max']);

            $names = ['type', 'input', 'text', 'paid', 'price', 'min', 'max', 'initial', 'locations'];
            foreach ($names as $name) {

                if (isset($_POST['quid_field_'.$name])) {
                    $value = $_POST['quid_field_'.$name];

                    if ($name == 'initial' || $name == 'price') {
                        $value = $this->limitPrice($value, false);
                    }

                    if (gettype($value) == 'string') {
                        $postSettings[$name] = stripslashes(sanitize_text_field($value));
                    } else {
                        $postSettings[$name] = $value;
                    }
                }

            }

            update_post_meta(
                $post_id,
                'quid_post_settings',
                json_encode($postSettings, JSON_UNESCAPED_SLASHES)
            );
        }

        private function limitMinPrice($value, $allowBlank) {
            if ($allowBlank && $value == "") return $value;

            $price = floatval($value);
            if ($price < 0.01 || $price > 10.00) return "0.01";

            return $value;
        }

        private function limitMaxPrice($value, $allowBlank) {
            if ($allowBlank && $value == "") return $value;

            $price = floatval($value);
            if ($price < 0.01 || $price > 10.00) return "10.00";

            return $value;
        }

        private function limitPrice($value, $allowBlank) {
            if ($allowBlank && $value == "") return $value;

            $price = floatval($value);

            if ($price < 0.01 || $price > 10.00) $price = 1.00;

            return strval(number_format($price, 2, '.', ','));
        }

        private function maxAndMinMustBeDifferent(&$minprice, &$maxprice) {
            $minprice = $this->limitMinPrice($minprice, false);
            $maxprice = $this->limitMaxPrice($maxprice, false);

            $minpriceFloat = floatval($minprice);
            $maxpriceFloat = floatval($maxprice);

            if ($maxpriceFloat == $minpriceFloat) {
                if ($maxpriceFloat == 10.00) {
                    $minpriceFloat -= 0.01;
                    $minprice = strval($minpriceFloat);
                } else {
                    $maxpriceFloat += 0.01;
                    $maxprice = strval($maxpriceFloat);
                }
            }
        }

    }

}

?>

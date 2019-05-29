<?php

namespace QUIDPaymentsMeta {

    class Meta {

        public function getMetaFields($post) {
            return array(
                "type" => get_post_meta($post->ID, 'quid_field_type', true),
                "input" => get_post_meta($post->ID, 'quid_field_input', true),
                "id" => get_post_meta($post->ID, 'quid_field_id', true),
                "name" => get_post_meta($post->ID, 'quid_field_name', true),
                "description" => get_post_meta($post->ID, 'quid_field_description', true),
                "url" => get_post_meta($post->ID, 'quid_field_url', true),
                "text" => get_post_meta($post->ID, 'quid_field_text', true),
                "paid" => get_post_meta($post->ID, 'quid_field_paid', true),
                "price" => get_post_meta($post->ID, 'quid_field_price', true),
                "min" => get_post_meta($post->ID, 'quid_field_min', true),
                "max" => get_post_meta($post->ID, 'quid_field_max', true),
                "initial" => get_post_meta($post->ID, 'quid_field_initial', true),
            );
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
            $meta = $this->getMetaFields($post);
            wp_register_style( 'css_quid_meta', plugins_url( 'css/meta.css', __FILE__ ) );
            wp_enqueue_style( 'css_quid_meta' );

            wp_register_script( 'js_quid_meta_'.$meta['id'], plugins_url( 'js/meta.js', __FILE__ ) );
            wp_localize_script( 'js_quid_meta_'.$meta['id'], 'dataMetaJS', null );
            wp_enqueue_script( 'js_quid_meta_'.$meta['id'] );
            ?>
            <div class="quid-post-meta">
                <div>
                    <label>Payment Type</label>
                    <select name="quid_field_type">
                        <option <?php if ($meta['type'] == "None") echo "selected"; ?> value="None">None</option>
                        <option <?php if ($meta['type'] == "Required") echo "selected"; ?> value="Required">Required</option>
                        <option <?php if ($meta['type'] == "Optional") echo "selected"; ?> value="Optional">Optional</option>
                    </select>
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Input Type</label>
                    <select name="quid_field_input">
                        <option <?php if ($meta['input'] == "Buttons") echo "selected"; ?> value="Buttons">Buttons</option>
                        <option <?php if ($meta['input'] == "Slider") echo "selected"; ?> value="Slider">Slider</option>
                    </select>
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Product ID</label>
                    <input name="quid_field_id" placeholder="Product ID" value="<?php echo $meta['id'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Product Name</label>
                    <input name="quid_field_name" placeholder="Name" value="<?php echo $meta['name'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Product Description</label>
                    <input name="quid_field_description" placeholder="Description" value="<?php echo $meta['description'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Product URL</label>
                    <input name="quid_field_url" placeholder="URL" value="<?php echo $meta['url'] ?>" />
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
                    <input name="quid_field_price" placeholder="Price ($0.01 - $2)" type="number" value="<?php echo $meta['price'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Minimum Slider Value</label>
                    <input onkeyup="quidPostMeta.handleMinKeypress(event)" name="quid_field_min" placeholder="Min Amount ($0.01 or more)" type="number" value="<?php echo $meta['min'] != '' ? $meta['min'] : '0.01' ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Maximum Slider Value</label>
                    <input onkeyup="quidPostMeta.handleMaxKeypress(event)" name="quid_field_max" placeholder="Max Amount ($2 or less)" type="number" value="<?php echo $meta['max'] != '' ? $meta['max'] : '2.00' ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
                <div>
                    <label>Initial Slider Value</label>
                    <input name="quid_field_initial" placeholder="Initial Amount ($0.01 - $2)" type="number" value="<?php echo $meta['initial'] ?>" />
                    <div class="quid-post-meta-message" style="display: none;"></div>
                </div>
            </div>
            <?php
        }

        public function save_postdata($post_id) {
            $this->maxAndMinMustBeDifferent($_POST['quid_field_min'], $_POST['quid_field_max']);

            $names = ['type', 'input', 'id', 'name', 'description', 'url', 'text', 'paid', 'price', 'min', 'max', 'initial'];
            foreach ($names as $name) {

                $value = $_POST['quid_field_'.$name];

                switch ($name) {
                    case 'price':
                        $value = $this->limitPrice($value, false);
                    case 'initial':
                        $value = $this->limitPrice($value, true);
                    default:
                        break;
                }

                if (array_key_exists('quid_field_'.$name, $_POST)) {
                    update_post_meta(
                        $post_id,
                        'quid_field_'.$name,
                        sanitize_text_field($value)
                    );
                }
            }
        }

        private function limitPrice($value, $allowBlank) {
            if ($allowBlank && $value == "") return $value;

            $price = floatval($value);

            if ($price == 0) return "2.00";
            if ($price < 0.01) return "0.01";
            if ($price > 2.00) return "2.00";

            return $value;
        }

        private function maxAndMinMustBeDifferent(&$minprice, &$maxprice) {
            $minprice = $this->limitPrice($minprice, true);
            $maxprice = $this->limitPrice($maxprice, true);

            $minpriceFloat = floatval($minprice);
            $maxpriceFloat = floatval($maxprice);

            if ($minprice == "" || $minpriceFloat == 0) $minprice = "0.01";
            if ($maxprice == "" || $maxpriceFloat == 0) $maxprice = "2.00";

            if ($maxprice == $minprice) {
                if ($maxpriceFloat == 2.00) {
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
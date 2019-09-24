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
        <input class="quid-post-meta-button-only" <?php echo $sliderReadOnly ?> onkeyup="quidPostMeta.handlePriceKeypress(event)" name="quid_field_price" placeholder="Price ($0.01 - $2)" type="number" value="<?php echo $meta['price'] ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div>
        <label>Minimum Slider Value</label>
        <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handleMinKeypress(event)" name="quid_field_min" placeholder="Min Amount ($0.01 or more)" type="number" value="<?php echo $meta['min'] != '' ? $meta['min'] : '0.01' ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div>
        <label>Maximum Slider Value</label>
        <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handleMaxKeypress(event)" name="quid_field_max" placeholder="Max Amount ($2 or less)" type="number" value="<?php echo $meta['max'] != '' ? $meta['max'] : '2.00' ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div>
        <label>Initial Slider Value</label>
        <input class="quid-post-meta-slider-only" <?php echo $buttonsReadOnly ?> onkeyup="quidPostMeta.handlePriceKeypress(event)" name="quid_field_initial" placeholder="Initial Amount ($0.01 - $2)" type="number" value="<?php echo $meta['initial'] ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div style="display: none;">
        <label>Product ID</label>
        <input name="quid_field_id" readonly placeholder="Product ID" value="<?php echo $postSlug; ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div style="display: none;">
        <label>Product Name</label>
        <input name="quid_field_name" readonly placeholder="Name" value="<?php echo $postTitle; ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div style="display: none;">
        <label>Product Description</label>
        <input name="quid_field_description" readonly placeholder="Description" value="<?php echo $siteTitle; ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
    <div style="display: none;">
        <label>Product URL</label>
        <input name="quid_field_url" readonly placeholder="URL" value="<?php echo get_permalink($post) ?>" />
        <div class="quid-post-meta-message" style="display: none;"></div>
    </div>
</div>
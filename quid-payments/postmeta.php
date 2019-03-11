<?php

add_action('save_post', 'wporg_save_postdata');
add_action('add_meta_boxes', 'wporg_add_custom_box');

function getMetaFields($post) {
    $object = new stdClass();

    $object->type = get_post_meta($post->ID, 'quid_field_type', true);
    $object->input = get_post_meta($post->ID, 'quid_field_input', true);
    $object->id = get_post_meta($post->ID, 'quid_field_id', true);
    $object->name = get_post_meta($post->ID, 'quid_field_name', true);
    $object->description = get_post_meta($post->ID, 'quid_field_description', true);
    $object->url = get_post_meta($post->ID, 'quid_field_url', true);
    $object->text = get_post_meta($post->ID, 'quid_field_text', true);
    $object->paid = get_post_meta($post->ID, 'quid_field_paid', true);
    $object->price = get_post_meta($post->ID, 'quid_field_price', true);
    $object->min = get_post_meta($post->ID, 'quid_field_min', true);
    $object->max = get_post_meta($post->ID, 'quid_field_max', true);
    $object->initial = get_post_meta($post->ID, 'quid_field_initial', true);

    return $object;
}

function wporg_add_custom_box() {
    $screens = ['post', 'wporg_cpt'];
    foreach ($screens as $screen) {
        add_meta_box(
            'wporg_box_id',           // Unique ID
            'QUID - Post Settings',      // Box title
            'wporg_custom_box_html',  // Content callback, must be of type callable
            $screen                   // Post type
        );
    }
}

function wporg_custom_box_html($post) {
    $meta = getMetaFields($post);
    ?>
    <style>
        .quid-post-meta {
            display: flex;
            align-items: center;
            flex-flow: wrap;
        }
        .quid-post-meta div {
            width: 25%;
            box-sizing: border-box;
            padding: 5px;
        }
        .quid-post-meta input, .quid-post-meta select {
            width: 100%;
            border-radius: 4px;
            border: solid 1px rgba(0,0,0,0.25);
        }
    </style>
    <div class="quid-post-meta">
        <div>
            <label>Payment Type</label>
            <select name="quid_field_type">
                <option <?php if ($meta->type == "None") echo "selected"; ?> value="None">None</option>
                <option <?php if ($meta->type == "Required") echo "selected"; ?> value="Required">Required</option>
                <option <?php if ($meta->type == "Optional") echo "selected"; ?> value="Optional">Optional</option>
            </select>
        </div>
        <div>
            <label>Input Type</label>
            <select name="quid_field_input">
                <option <?php if ($meta->input == "Buttons") echo "selected"; ?> value="Buttons">Buttons</option>
                <option <?php if ($meta->input == "Slider") echo "selected"; ?> value="Slider">Slider</option>
            </select>
        </div>
        <div>
            <label>Product ID</label>
            <input name="quid_field_id" placeholder="ID" value="<?php echo $meta->id ?>" />
        </div>
        <div>
            <label>Product Name</label>
            <input name="quid_field_name" placeholder="Name" value="<?php echo $meta->name ?>" />
        </div>
        <div>
            <label>Product Description</label>
            <input name="quid_field_description" placeholder="Description" value="<?php echo $meta->description ?>" />
        </div>
        <div>
            <label>Product URL</label>
            <input name="quid_field_url" placeholder="URL" value="<?php echo $meta->url ?>" />
        </div>
        <div>
            <label>Button Text</label>
            <input name="quid_field_text" placeholder="Button Text" value="<?php echo $meta->text ?>" />
        </div>
        <div>
            <label>Paid Text</label>
            <input name="quid_field_paid" placeholder="Paid Button Text" value="<?php echo $meta->paid ?>" />
        </div>
        <div>
            <label>Price</label>
            <input name="quid_field_price" placeholder="Price" type="number" value="<?php echo $meta->price ?>" />
        </div>
        <div>
            <label>Minimum Slider Value</label>
            <input name="quid_field_min" placeholder="Min Amount" type="number" value="<?php echo $meta->min ?>" />
        </div>
        <div>
            <label>Maximum Slider Value</label>
            <input name="quid_field_max" placeholder="Max Amount" type="number" value="<?php echo $meta->max ?>" />
        </div>
        <div>
            <label>Initial Slider Value</label>
            <input name="quid_field_initial" placeholder="Initial Amount" type="number" value="<?php echo $meta->initial ?>" />
        </div>
    </div>
    <?php
}

function wporg_save_postdata($post_id) {
    $names = ['type', 'input', 'id', 'name', 'description', 'url', 'text', 'paid', 'price', 'min', 'max', 'initial'];
    foreach ($names as $name) {
        if (array_key_exists('quid_field_'.$name, $_POST)) {
            update_post_meta(
                $post_id,
                'quid_field_'.$name,
                $_POST['quid_field_'.$name]
            );
        }
    }
}

?>
<?php

function isTitleFoundInDB($postTitle) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT ID FROM {$wpdb->dbname}.{$wpdb->prefix}posts WHERE post_status = 'private' AND post_title = '%s' ORDER BY ID DESC LIMIT 1", $postTitle);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

function hasPurchasedAlready($userHash, $productID) {
    global $wpdb;
    $sql = $wpdb->prepare("SELECT `product-id` FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE user = '%s' AND `product-id`='%s' LIMIT 1", $userHash, $productID);
    $results = $wpdb->get_results( $sql, ARRAY_N );
    return sizeof($results) > 0;
}

// https://codex.wordpress.org/Creating_Tables_with_Plugins
register_activation_hook( __FILE__, 'createPurchaseDatabase' );

function createPurchaseDatabase() {
    global $wpdb;
    $table_name = $wpdb->prefix . "quidPurchases";

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		$sql = "CREATE TABLE ".$table_name." (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `user` VARCHAR(60) NOT NULL,
            `product-id` VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        );";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
}

?>
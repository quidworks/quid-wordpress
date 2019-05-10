<?php

namespace QUIDPaymentsDatabase {

    class Database {

        function hasPurchasedAlready($userHash, $productID) {
            global $wpdb;
            $sql = $wpdb->prepare("SELECT `product-id` FROM {$wpdb->dbname}.{$wpdb->prefix}quidPurchases WHERE tip!='true' AND user = '%s' AND `product-id`='%s' LIMIT 1", $userHash, $productID);
            $results = $wpdb->get_results( $sql, ARRAY_N );
            return sizeof($results) > 0;
        }

        function createPurchaseDatabase() {
            global $wpdb;
            $table_name = $wpdb->prefix . "quidPurchases";

            if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
                $sql = "CREATE TABLE ".$table_name." (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                    `user` VARCHAR(60) NOT NULL,
                    `product-id` VARCHAR(255) NOT NULL,
                    `tip` VARCHAR(5) DEFAULT 'false' NOT NULL,
                    PRIMARY KEY (id)
                );";
                
                require_once(ABSPATH. 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

    }

}

?>
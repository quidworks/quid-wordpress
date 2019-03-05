<?php

// This is included in the head of every wordpress page
add_action( 'wp_head', 'quidInit' );

// <script src='https://js.quid.works/v1/client.js'></script>
// <link rel='stylesheet' type='text/css' href='https://js.quid.works/v1/assets/quid.css' />
// <script src='http://localhost:8082/dist/client.dev.js'></script>
// <link rel='stylesheet' type='text/css' href='http://localhost:8082/assets/quid.css' />

// quid.works.client.js
function quidInit() {
    print_r("
        <script src='https://js.quid.works/v1/client.js'></script>
        <link rel='stylesheet' type='text/css' href='https://js.quid.works/v1/assets/quid.css' />
        <style>
        .quid-pay-buttons {
            margin: 0px!important;
            text-align: center!important;
        }
        .wp-quid-error {
            font-family: sans-serif;
            font-size: 15px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            color: rgba(0,0,0,0.6);
            border-radius: 4px;
            padding: 10px 20px;
            margin: 10px 0px;
            background-color: #eee;
            box-shadow: 0px 0px 1px 1px rgba(0,0,0,0.2);
        }
        .wp-quid-error-image {
            height: 25px;
            margin-right: 20px;
        }
        .quid-slider-wrapper.quid-slider-default {
            margin: 0px auto;
            width: 300px;
        }
        </style>
        <script>
            let qButton;
            let qSlider;
            let baseElement;
            let alreadyPaid;
            _quid_wp_global = {};
        </script>
    ");
}

?>
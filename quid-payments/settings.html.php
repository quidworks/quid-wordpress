<div class='quid-pay-settings'>
    <div class="quid-pay-settings-header">
        <h1 class='quid-pay-settings-page-title'>QUID Settings</h1>
        <div class="quid-pay-settings-save-top"><button class="button button-primary" type='button' onclick='quidSettings.submitQuidSettings()'>Save</button></div>
    </div>

    <div id="quidSettingsMessageContainer" class="quid-settings-message notice is-dismissible"></div>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>QUID Merchant Account Settings</div>
        <div class='quid-pay-settings-section-description'>
        You must have a QUID merchant account to use this plugin. <a target='_blank' href='https://app.quid.works/sell'>Signup for your free account</a> or access <a target='_blank' href='https://app.quid.works/merchant'>your QUID merchant dashboard</a> to create your API key and secret.
        </div>
    </div>

    <div class="quid-settings-tooltip-container quid-settings-label-tooltip">
        <label>Currency</label>
    </div>
    <div class="quid-settings-tooltip-container">
        <select id='quid-currency' class='quid-pay-settings-dropdown quid-field' name="quid-currency">
            <option disabled <?php echo $quidCurrency === '' ? 'selected' : '' ?> value> -- Select your currency -- </option>
            <option value="CAD" <?php echo $quidCurrency === 'CAD' ? 'selected' : '' ?>>CAD</option>
            <option value="USD" <?php echo $quidCurrency === 'USD' ? 'selected' : '' ?>>USD</option>
        </select>
    </div>

    <div class="quid-settings-tooltip-container quid-settings-label-tooltip">
        <label>API Keys</label>
    </div>
    <input id='quid-publicKey' class="quid-field" name="quid-key-public" style='margin-bottom: 10px' value='<?php echo $quidPublicKey; ?>' placeholder='API Key' /><br />
    <input id='quid-secretKey' class="quid-field" name="quid-key-secret" type='password' placeholder='API Secret' />
    <p class="quid-pay-settings-input-description">Your secret key is not displayed to keep it extra safe</p>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>Inline Button Settings</div>
        <div class='quid-pay-settings-section-description'>
        Inline buttons are displayed within your blog posts using the <a target='_blank' href='https://how.quid.works/en/articles/3046949-blog-post-payment-fields'>QUID payment fields</a> for each blog post or using <a target='_blank' href='https://how.quid.works/en/articles/3047042-shortcodes'>shortcodes</a>.
        </div>
    </div>

    <label>Display Excerpts for Paid Items</label>
    <div class="quid-settings-tooltip-container">
        <select id='quid-align' class='quid-pay-settings-dropdown quid-field' name="quid-read-more">
            <option value="true" <?php echo $quidFabSettings['quid-read-more'] === 'true' || $quidFabSettings['quid-read-more'] === '' ? 'selected' : '' ?>>Enabled</option>
            <option value="false" <?php echo $quidFabSettings['quid-read-more'] === 'false' ? 'selected' : '' ?>>Disabled</option>
        </select>
    </div>

    <label>Default Button Alignment</label>
    <div class="quid-settings-tooltip-container">
        <select id='quid-align' class='quid-pay-settings-dropdown quid-field' name="quid-button-position">
            <option value="right" <?php echo $quidAlign === 'right' || $quidAlign === '' ? 'selected' : '' ?>>Right</option>
            <option value="center" <?php echo $quidAlign === 'center' ? 'selected' : '' ?>>Center</option>
            <option value="left" <?php echo $quidAlign === 'left' ? 'selected' : '' ?>>Left</option>
        </select>
    </div>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>Floating Tip Button Settings</div>
        <div class='quid-pay-settings-section-description'>
        A <a target='_blank' href='http://how.quid.works/en/articles/3213187-the-floating-tip-button'>floating tip button</a> is displayed in the bottom right or left corner for visitors to your site and is visible in the same location on all pages while the visitor scrolls and navigates through your site.
        </div>
    </div>

    <div class="quid-settings-tooltip-container">
        <div id="quidFabSwitch" class="quid-fab-switch <?php echo $quidFabEnabled ? "quid-fab-switched-on" : "quid-fab-switched-off"; ?>" onclick="quidSettings.toggleSwitch()">
            <input class="quid-field" type="checkbox" name="quid-fab-enabled" value="<?php echo $quidFabEnabled ? "true" : "false"; ?>" />
            <span class="quid-fab-switch-text"><?php echo $quidFabEnabled ? "ON" : "OFF"; ?></span>
            <span class="quid-fab-switch-handle"></span>
        </div>
    </div>

    <div class="quid-fab-settings">
        <div>
            <label>Position</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-position">
                    <option <?php echo 'right' == $quidFabSettings['quid-fab-position'] ? 'selected' : '' ?> value="right">Right</option>
                    <option <?php echo 'left' == $quidFabSettings['quid-fab-position'] ? 'selected' : '' ?> value="left">Left</option>
                </select>
            </div>
        </div>
        <div>
            <label>Palette</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-palette">
                    <option <?php echo 'default' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="default">Default</option>
                    <option <?php echo 'green' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="green">Green</option>
                    <option <?php echo 'blue' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="blue">Blue</option>
                    <option <?php echo 'red' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="red">Red</option>
                    <option <?php echo 'orange' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="orange">Orange</option>
                    <option <?php echo 'grey' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="grey">Grey</option>
                    <option <?php echo 'dark' == $quidFabSettings['quid-fab-palette'] ? 'selected' : '' ?> value="dark">Dark</option>
                </select>
            </div>
        </div>
        <div>
            <label>Demo</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-demo">
                    <option <?php echo "true" === $quidFabSettings['quid-fab-demo'] ? 'selected' : '' ?> value="true">Enabled</option>
                    <option <?php echo "false" === $quidFabSettings['quid-fab-demo'] ? 'selected' : '' ?> value="false">Disabled</option>
                </select>
            </div>
        </div>
        <div>
            <label>Reminder</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-reminder">
                    <option <?php echo "true" === $quidFabSettings['quid-fab-reminder'] ? 'selected' : '' ?> value="true">Enabled</option>
                    <option <?php echo "false" === $quidFabSettings['quid-fab-reminder'] ? 'selected' : '' ?> value="false">Disabled</option>
                </select>
            </div>
        </div>
        <div>
            <label>Text (Max 45 Characters)</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" name="quid-fab-text" maxlength="45" placeholder="Button Text" value="<?php echo $quidFabSettings['quid-fab-text'] === '' ? 'Slide the Q to leave a tip!' : $quidFabSettings['quid-fab-text']; ?>" />
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Paid Text (Max 25 Characters)</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" name="quid-fab-paid" maxlength="25" placeholder="Paid Button Text" value="<?php echo $quidFabSettings['quid-fab-paid'] === "" ? 'Thank You!' : $quidFabSettings['quid-fab-paid']; ?>" />
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Minimum Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handleMinKeypress(event)" name="quid-fab-min" placeholder="Min Amount ($0.01 or more)" type="number" value="<?php echo $quidFabSettings['quid-fab-min'] === "" ? "0.01" : $quidFabSettings['quid-fab-min']; ?>" />
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Maximum Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handleMaxKeypress(event)" name="quid-fab-max" placeholder="Max Amount ($2 or less)" type="number" value="<?php echo $quidFabSettings['quid-fab-max'] === "" ? "2.00" : $quidFabSettings['quid-fab-max']; ?>" />
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Default Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handlePriceKeypress(event)" name="quid-fab-initial" placeholder="Initial Amount ($0.01 - $2)" type="number" value="<?php echo $quidFabSettings['quid-fab-initial'] === "" ? "0.01" : $quidFabSettings['quid-fab-initial']; ?>" />
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Payment Description</label>
            <div class="quid-settings-tooltip-container">
                <textarea placeholder="Description..." class="quid-field" name="quid-fab-description" rows="1"><?php echo $quidFabSettings['quid-fab-description'] === "" ? 'Thanks for the support!' : $quidFabSettings['quid-fab-description']; ?></textarea>
            </div>
        </div>
    </div>

    <div class="quid-pay-settings-save-bottom"><button class="button button-primary" type='button' onclick='quidSettings.submitQuidSettings()'>Save</button></div>
</div>

<?php
    if(isset($_GET['quid-debug'])) {
        ?>
        <h1 class='quid-pay-settings-page-title'>QUID Payments Debugging Tools</h1>
        <div class='quid-pay-settings-subtitle'>Test QUID payments database table</div>
        <?php
        if(isset($_POST['quid-db-test'])){
            $dbTestResult = $this->testStorePurchase();
            if($dbTestResult){
                ?>DB Connection Test: <font color="green"><strong>Passed</strong></font><?php
            } else {
                global $wpdb;
                $table_name = $wpdb->prefix . "quidPurchases";
                ?>
                DB Connection Test: <font color="red"><strong>Failed</strong></font>
                <p>The QUID Payments plugin was unable to write a test payment to your WordPress database table <?php echo $table_name ?>.</p>
                <?php
            }
        } else {
            ?>
            <form  method="post">
                <input type="submit" name="quid-db-test" value="TEST" class="button button-primary">
            </form>
            <?php
        }

    }
?>
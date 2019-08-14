<div class='quid-pay-settings'>
    <div class="quid-pay-settings-header">
        <h1 class='quid-pay-settings-page-title'>QUID Settings</h1>
        <div class="quid-pay-settings-save-top"><button class="button button-primary" type='button' onclick='quidSettings.submitQuidSettings()'>Save</button></div>
    </div>

    <div id="quidSettingsMessageContainer" class="quid-settings-message notice is-dismissible"></div>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>General Settings</div>
        <div class='quid-pay-settings-section-description'>
            This is a description
        </div>
    </div>

    <div class="quid-settings-tooltip-container quid-settings-label-tooltip">
        <label>Currency of your Merchant Account</label>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
    </div>
    <div class="quid-settings-tooltip-container">
        <select id='quid-currency' class='quid-pay-settings-dropdown quid-field' name="quid-currency">
            <option disabled <?php echo $quidCurrency === '' ? 'selected' : '' ?> value> -- Select your currency -- </option>
            <option value="CAD" <?php echo $quidCurrency === 'CAD' ? 'selected' : '' ?>>CAD</option>
            <option value="USD" <?php echo $quidCurrency === 'USD' ? 'selected' : '' ?>>USD</option>
        </select>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
        <div class="quid-settings-tooltip-text">industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more</div>
    </div>

    <div class='quid-pay-settings-subtitle'>API Keys can be found on your <a target='_blank' href='https://app.quid.works/merchant'>QUID merchant page</a></div>

    <div class="quid-settings-tooltip-container quid-settings-label-tooltip">
        <label>Public and Secret Keys</label>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
        <div class="quid-settings-tooltip-text">industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more</div>
    </div>
    <input id='quid-publicKey' class="quid-field" name="quid-key-public" style='margin-bottom: 10px' value='<?php echo $quidPublicKey; ?>' placeholder='Public API Key' /><br />
    <input id='quid-secretKey' class="quid-field" name="quid-key-secret" type='password' placeholder='Secret API Key' />
    <p class="quid-pay-settings-input-description">secret key is not displayed to keep it extra safe</p>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>Post Defaults</div>
        <div class='quid-pay-settings-section-description'></div>
    </div>

    <label>Read More Button</label>
    <div class="quid-settings-tooltip-container">
        <select id='quid-align' class='quid-pay-settings-dropdown quid-field' name="quid-read-more">
            <option value="true" <?php echo $quidFabSettings['quid-read-more'] === 'true' || $quidFabSettings['quid-read-more'] === '' ? 'selected' : '' ?>>Enabled</option>
            <option value="false" <?php echo $quidFabSettings['quid-read-more'] === 'false' ? 'selected' : '' ?>>Disabled</option>
        </select>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
    </div>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>Button Defaults</div>
        <div class='quid-pay-settings-section-description'></div>
    </div>
    

    <label>Default Button Alignment</label>
    <div class="quid-settings-tooltip-container">
        <select id='quid-align' class='quid-pay-settings-dropdown quid-field' name="quid-button-position">
            <option value="right" <?php echo $quidAlign === 'right' || $quidAlign === '' ? 'selected' : '' ?>>Right</option>
            <option value="center" <?php echo $quidAlign === 'center' ? 'selected' : '' ?>>Center</option>
            <option value="left" <?php echo $quidAlign === 'left' ? 'selected' : '' ?>>Left</option>
        </select>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
    </div>

    <div class='quid-pay-settings-section-header'>
        <div class='quid-pay-settings-section-title'>Floating Button Settings</div>
        <div class='quid-pay-settings-section-description'></div>
    </div>

    <div class="quid-settings-tooltip-container">
        <div id="quidFabSwitch" class="quid-fab-switch <?php echo $quidFabEnabled ? "quid-fab-switched-on" : "quid-fab-switched-off"; ?>" onclick="quidSettings.toggleSwitch()">
            <input class="quid-field" type="checkbox" name="quid-fab-enabled" value="<?php echo $quidFabEnabled ? "true" : "false"; ?>" />
            <span class="quid-fab-switch-text"><?php echo $quidFabEnabled ? "ON" : "OFF"; ?></span>
            <span class="quid-fab-switch-handle"></span>
        </div>
        <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
    </div>

    <div class="quid-fab-settings">
        <div>
            <label>Position</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-position">
                    <option <?php echo 'right' == $quidFabSettings['quid-fab-position'] ? 'selected' : '' ?> value="right">Right</option>
                    <option <?php echo 'left' == $quidFabSettings['quid-fab-position'] ? 'selected' : '' ?> value="left">Left</option>
                </select>
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
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
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
        </div>
        <div>
            <label>Demo</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-demo">
                    <option <?php echo "true" === $quidFabSettings['quid-fab-demo'] ? 'selected' : '' ?> value="true">Enabled</option>
                    <option <?php echo "false" === $quidFabSettings['quid-fab-demo'] ? 'selected' : '' ?> value="false">Disabled</option>
                </select>
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
        </div>
        <div>
            <label>Reminder</label>
            <div class="quid-settings-tooltip-container">
                <select class="quid-pay-settings-dropdown quid-field" name="quid-fab-reminder">
                    <option <?php echo "true" === $quidFabSettings['quid-fab-reminder'] ? 'selected' : '' ?> value="true">Enabled</option>
                    <option <?php echo "false" === $quidFabSettings['quid-fab-reminder'] ? 'selected' : '' ?> value="false">Disabled</option>
                </select>
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
        </div>
        <div>
            <label>Text (Max 45 Characters)</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" name="quid-fab-text" maxlength="45" placeholder="Button Text" value="<?php echo $quidFabSettings['quid-fab-text'] === '' ? 'Slide the Q to leave a tip!' : $quidFabSettings['quid-fab-text']; ?>" />
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Paid Text (Max 25 Characters)</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" name="quid-fab-paid" maxlength="25" placeholder="Paid Button Text" value="<?php echo $quidFabSettings['quid-fab-paid'] === "" ? 'Thank You!' : $quidFabSettings['quid-fab-paid']; ?>" />
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Minimum Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handleMinKeypress(event)" name="quid-fab-min" placeholder="Min Amount ($0.01 or more)" type="number" value="<?php echo $quidFabSettings['quid-fab-min'] === "" ? "0.01" : $quidFabSettings['quid-fab-min']; ?>" />
                <div class="quid-settings-tooltip">
                    <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
                    <div class="quid-settings-tooltip-text">industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more</div>
                </div>
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Maximum Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handleMaxKeypress(event)" name="quid-fab-max" placeholder="Max Amount ($2 or less)" type="number" value="<?php echo $quidFabSettings['quid-fab-max'] === "" ? "2.00" : $quidFabSettings['quid-fab-max']; ?>" />
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Default Price</label>
            <div class="quid-settings-tooltip-container">
                <input class="quid-field" onkeyup="quidSettings.handlePriceKeypress(event)" name="quid-fab-initial" placeholder="Initial Amount ($0.01 - $2)" type="number" value="<?php echo $quidFabSettings['quid-fab-initial'] === "" ? "0.01" : $quidFabSettings['quid-fab-initial']; ?>" />
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
            </div>
            <div class="quid-fab-setting-message" style="display: none;"></div>
        </div>
        <div>
            <label>Payment Description</label>
            <div class="quid-settings-tooltip-container">
                <textarea placeholder="Description..." class="quid-field" name="quid-fab-description" rows="1"><?php echo $quidFabSettings['quid-fab-description'] === "" ? 'Thanks for the support!' : $quidFabSettings['quid-fab-description']; ?></textarea>
                <div class="quid-settings-tooltip-icon" title="this is a title">?</div>
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
<?php

function wctn_register_settings()
{
    register_setting('wctn_settings_group', 'wctn_telegram_bot_token');
    register_setting('wctn_settings_group', 'wctn_telegram_chat_id');
}

add_action('admin_init', 'wctn_register_settings');

function wctn_create_settings_page()
{
    add_options_page('WooCommerce Telegram Notifier', 'WooCommerce Telegram Notifier', 'manage_options', 'wctn_settings', 'wctn_settings_page');
}

add_action('admin_menu', 'wctn_create_settings_page');

function wctn_settings_page()
{
?>
    <div class="wrap">
        <h1>WooCommerce Telegram Notifier</h1>
        <form method="post" action="options.php">
            <?php settings_fields('wctn_settings_group'); ?>
            <?php do_settings_sections('wctn_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Telegram Bot Token</th>
                    <td><input type="text" name="wctn_telegram_bot_token" value="<?php echo esc_attr(get_option('wctn_telegram_bot_token')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Telegram Chat ID</th>
                    <td><input type="text" name="wctn_telegram_chat_id" value="<?php echo esc_attr(get_option('wctn_telegram_chat_id')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

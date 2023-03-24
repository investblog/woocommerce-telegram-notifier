<?php
/*
Plugin Name: WooCommerce Telegram Notifier
Plugin URI: https://github.com/investblog/woocommerce-telegram-notifier/
Description: Этот плагин отправляет уведомления в Telegram-группу при успешной покупке в вашем магазине на WooCommerce.
Version: 1.0.0
Author: Alex Smirnoff
Author URI: https://investblog.io
License: GPLv2 or later
Text Domain: woocommerce-telegram-notifier
*/


// Проверка на активацию WooCommerce
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_action( 'admin_notices', 'woocommerce_not_active_notice' );
    return;
}

function woocommerce_not_active_notice() {
    ?>
    <div class="notice notice-error is-dismissible">
        <p><?php _e( 'WooCommerce Telegram Notifier требует установленного и активного плагина WooCommerce.', 'woocommerce-telegram-notifier' ); ?></p>
    </div>
    <?php
}

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'settings-page.php';

function wctn_send_telegram_message($message)
{
    $telegram_bot_token = get_option('wctn_telegram_bot_token');
    $telegram_chat_id = get_option('wctn_telegram_chat_id');

    if (empty($telegram_bot_token) || empty($telegram_chat_id)) {
        return;
    }

    $url = "https://api.telegram.org/bot" . $telegram_bot_token . "/sendMessage";
    $data = array(
        'chat_id' => $telegram_chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    );

    $options = array(
        'http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

function wctn_on_order_status_changed($order_id, $old_status, $new_status)
{
    if ($new_status == 'completed') {
        $order = wc_get_order($order_id);
        $message = "Новый заказ завершен! Заказ №{$order_id}\n";
        $message .= "Покупатель: {$order->get_billing_first_name()} {$order->get_billing_last_name()}\n";
        $message .= "Email: {$order->get_billing_email()}\n";
        $message .= "Телефон: {$order->get_billing_phone()}\n";

        wctn_send_telegram_message($message);
    }
}

add_action('woocommerce_order_status_changed', 'wctn_on_order_status_changed', 10, 3);

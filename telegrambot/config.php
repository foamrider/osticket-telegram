<?php

require_once INCLUDE_DIR . 'class.plugin.php';

class TelegramPluginConfig extends PluginConfig {
    function getOptions() {
        return array(
            'telegram' => new SectionBreakField(array(
                'label' => 'Telegram Bot',
            )),
            'telegram-webhook-url' => new TextboxField(array(
                'label' => 'Telegram Bot URL',
                'configuration' => array('size'=>100, 'length'=>200),
            )),
            'telegram-chat-id' => new TextboxField(array(
                'label' => 'Chat ID',
                'configuration' => array('size'=>100, 'length'=>200),
            )),
            'telegram-include-body' => new BooleanField(array(
                'label' => 'Include Body',
                'default' => 0,
            )),
        );
    }
}

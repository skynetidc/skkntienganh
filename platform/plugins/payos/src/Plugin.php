<?php

namespace Binjuhor\PayOs;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::query()
            ->whereIn('key', [
                'payment_payos_name',
                'payment_payos_description',
                'payment_payos_client_url',
                'payment_payos_tmncode',
                'payment_payos_secret',
            ])
            ->delete();
    }
}

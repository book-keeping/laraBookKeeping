<?php

namespace App\Http\Responder\Settings;

use App\Http\Responder\BaseLayoutViewResponder;

class SettingsViewResponder extends BaseLayoutViewResponder
{
    /**
     * List of Navigation links for Settings.
     *
     * @return array
     */
    public function navilinks(): array
    {
        return [
            ['link' => 'settings_tokens', 'caption' => __('Personal access tokens')],
        ];
    }
}

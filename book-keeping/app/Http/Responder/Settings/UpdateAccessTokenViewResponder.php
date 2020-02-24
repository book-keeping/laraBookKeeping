<?php

namespace App\Http\Responder\Settings;

use Illuminate\Http\Response;

class UpdateAccessTokenViewResponder extends SettingsViewResponder
{
    /**
     * Respond the UpdateAccessTokenView.
     *
     * @param array $context
     *
     * @return Illuminate\Http\Response
     */
    public function response(array $context): Response
    {
        $message_for_new_token = null;
        $message_for_no_token = null;

        if ($context['token'] != null) {
            $message_for_new_token = __('Make sure to copy your new personal access token now.').__('You won’t be able to see it again!');
        }
        if ($context['timestamp'] == null) {
            $message_for_no_token = __('There is no token available.');
        }
        $this->response->setContent($this->view->make('settings.accesstokens', [
            'selflinkname'          => 'settings_tokens',
            'settingnavilinks'      => $this->navilinks(),
            'message_for_new_token' => $message_for_new_token,
            'message_for_no_token'  => $message_for_no_token,
            'token'                 => $context['token'],
            'timestamp'             => __('The token was generated at ').$context['timestamp'],
        ]));
        $this->response->setStatusCode(Response::HTTP_OK);

        return $this->response;
    }
}

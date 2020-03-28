<?php

namespace SocialAuther\Adapter;

class Yandex extends AbstractAdapter
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFieldsMap = array(
            'socialId'   => 'id',
            'email'      => 'default_email',
            'name'       => 'real_name',
            'socialPage' => 'link',
            'avatar'     => 'picture',
            'sex'        => 'sex',
            'birthday'   => 'birthday'
        );

        $this->provider = 'yandex';
    }

    /**
     * Authenticate and return bool result of authentication
     *
     * @return bool
     */
    public function authenticate()
    {
        $result = false;

        if (isset($_GET['code'])) {
            $params = array(
                'grant_type' => 'authorization_code',
                'code' => $_GET['code'],
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret
            );

            $tokenInfo = $this->post('https://oauth.yandex.ru/token', $params);
			echo '<pre>';
			print_r($tokenInfo);
			echo '</pre>';
            if (isset($tokenInfo['access_token'])) {
                $params = array(
                    'format' => 'json',
                    'oauth_token' => $tokenInfo['access_token']
                );

                $userInfo = $this->get('https://login.yandex.ru/info', $params);
				print_r($userInfo);
                if (isset($userInfo['id'])) {
                    $this->userInfo = $userInfo;
                    $result = true;
                }
            }
        }

        return $result;
    }

	
	
	public function getAvatar()
    {
        $result = null;

        if (isset($this->userInfo['default_avatar_id'])) {
            $result = "https://avatars.yandex.net/get-yapic/".$this->userInfo['default_avatar_id']."/islands-200";
        }

        return $result;
    }

    /**
     * Prepare params for authentication url
     *
     * @return array
     */
    public function prepareAuthParams()
    {
        return array(
            'auth_url'    => 'https://oauth.yandex.ru/authorize',
            'auth_params' => array(
                'response_type' => 'code',
                'client_id'     => $this->clientId,
                'display'       => 'popup'
            )
        );
    }
}
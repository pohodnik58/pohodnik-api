<?php

namespace SocialAuther\Adapter;

class Strava extends AbstractAdapter
{
    public function __construct($config)
    {
        parent::__construct($config);

        $this->socialFieldsMap = array(
            'socialId'   => 'id',
            'email'      => 'email',
            'avatar'     => 'profile'
        );

        $this->provider = 'strava';
    }

    /**
     * Get user name or null if it is not set
     *
     * @return string|null
     */
    public function getName()
    {
        $result = null;

        if (isset($this->userInfo['firstname']) && isset($this->userInfo['lastname'])) {
            $result = $this->userInfo['firstname'] . ' ' . $this->userInfo['lastname'];
        } elseif (isset($this->userInfo['firstname']) && !isset($this->userInfo['lastname'])) {
            $result = $this->userInfo['firstname'];
        } elseif (!isset($this->userInfo['firstname']) && isset($this->userInfo['lastname'])) {
            $result = $this->userInfo['lastname'];
        }

        return $result;
    }

    /**
     * Get user social id or null if it is not set
     *
     * @return string|null
     */
    public function getSocialPage()
    {
        $result = null;

        if (isset($this->userInfo['id'])) {
            $result = 'https://www.strava.com/athletes/' . $this->userInfo['id'];
        }

        return $result;
    }

    /**
     * Get user sex or null if it is not set
     *
     * @return string|null
     */
    public function getSex()
    {
        $result = null;
        if (isset($this->userInfo['sex'])) {
            $result = $this->userInfo['sex'] == 'M' ? 'male' : 'female';
        }

        return $result;
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
             $params= array(
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $_GET['code']
            );

            $tokenInfo = $this->post('https://www.strava.com/oauth/token', $params);
			
            if (isset($tokenInfo['access_token'])) {
               $params = array(
                    'access_token' => $tokenInfo['access_token']
                );

                $userInfo = $this->get('https://www.strava.com/api/v3/athlete', $params);
				
				//print_r($userInfo);
				
                if (isset($userInfo['id'])) {
                    $this->userInfo = $userInfo;
                    $this->userInfo['access_token'] = $tokenInfo['access_token'];
                    $result = true;
                }
            } else {
				die('no ACCESS TOKEN');
			}
        } else {
			die('no CODE');
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
            'auth_url'    => 'https://www.strava.com/oauth/authorize',
            'auth_params' => array(
                'client_id'     => $this->clientId,
                'scope'         => 'profile:read_all',
                'redirect_uri'  => $this->redirectUri,
                'response_type' => 'code',
				'state'=>'ok'
            )
        );
    }
}
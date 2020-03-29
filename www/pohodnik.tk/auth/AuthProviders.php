<?php
        include 'resource.php';
require_once '../ajax/lib/SocialAuther/autoload.php';

class AuthProviders
{
	private static $_instance = null;
	private static $adapters = array();

	private function getProviders() {
	    return array(
            'vk' => array(
                'client_id'     => '6499514',
                'client_secret' => 'thztiCXqcTYm2oaXZq4h',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=vk',
        		'bg'=>'#6383A8',
        		'icon'=>'icons/vk.svg',
        		'name'=>'ВКонтакте'

            ),
            'odnoklassniki' => array(		'bg'=>'#F4731C',
        		'icon'=>'icons/odnoklassniki.svg',
                'client_id'     => '1267313664',
                'client_secret' => '41FBFA3E60F32D40987922E2',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=odnoklassniki',
                'public_key'    => 'CBANKKIMEBABABABA',
        		'name'=>'Одноклассники'

            ),
            'mailru' => array(
                'client_id'     => '760410',
                'client_secret' => '357aaf458d5acded3ec62dd04a0c5b94',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=mailru',
        		'bg'=>'#168DE2',
        		'icon'=>'icons/mail-dot-ru.svg',
        		'name'=>'Mail.Ru'
            ),
            'yandex' => array(
                'client_id'     => '94f124fc09334032bba0acb78954db81',
                'client_secret' => 'fec8018a1528402099b92e1ce9b0e8cf',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=yandex',
        		'bg'=>'#FF0000',
        		'icon'=>'icons/yandex.svg',
        		'name'=>'Яндекс'
            ),
            'google' => array(
                'client_id'     => '922332876703-smn70a4fbdr7teo3uihrvoab5smdbvl5.apps.googleusercontent.com',
                'client_secret' => '5LI8TK8hajc-ktIU_H8bnGcW',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=google',
        		'bg'=>'#4285F4',
        		'icon'=>'icons/google.svg',
        		'name'=>'Google'
            ),
            'facebook' => array(
                'client_id'     => '2139245699623747',
                'client_secret' => '6d4c2b42b9f2255783fd45515f89541b',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=facebook',
        		'bg'=>'#3B5998',
        		'icon'=>'icons/facebook.svg',
        		'name'=>'Facebook'
            ),
            'strava' => array(
                'client_id'     => '15626',
                'client_secret' => '45cc15025003629e265487c81ddafbeb1f668a59',
                'redirect_uri'  => 'https://pohodnik.tk/auth/?provider=strava',
        		'bg'=>'#FC4C02',
        		'icon'=>'icons/strava.svg',
        		'name'=>'Strava'
            )
        );
	}

	private function __construct () {
		$adapterConfigs = $this->getProviders();
		$this->adapters = array();
        foreach ($adapterConfigs as $adapter => $settings) {
            $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
            $this->adapters[$adapter] = new $class($settings);
        }
	}

	private function __clone () {}
	private function __wakeup () {}

	public static function getInstance()
	{
		if (self::$_instance != null) {
			return self::$_instance;
		}

		return new self;
	}

	public function getSettings() {
	    return $this->adapters;
	}
}
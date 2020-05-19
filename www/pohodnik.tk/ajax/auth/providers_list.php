<?php
    require_once '../lib/SocialAuther/autoload.php';
    include './adapters_config.php';
    include '../../blocks/global.php';
    $adapters = array();

    $res = array();
    foreach ($adapterConfigs as $adapter => $settings) {
        $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
        $adapters[$adapter] = new $class($settings);
        $res[] = array(
            "id" => $adapter,
            "url" => $adapters[$adapter]->getAuthUrl(),
            "name" => $adapters[$adapter]->getFullName()
        );
    }

    die(out($res));
?>

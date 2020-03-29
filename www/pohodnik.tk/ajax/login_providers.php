<?php
require_once '../auth/AuthProviders.php';

$adapters = AuthProviders::getInstance()->getSettings();

echo(json_encode($adapters, true));
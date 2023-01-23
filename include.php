<?php

use Axelsoft\Task\CustomProperty;
use Axelsoft\Task\Controller\Reviews;
use Bitrix\Main\Loader;

//Автозагрузка наших классов
Loader::registerAutoLoadClasses(
    'axelsoft.task', [
    '\\Axelsoft\\Task\\CustomProperty' => 'lib/CustomProperty.php',
    '\\Axelsoft\\Task\\Controller\\Reviews' => 'lib/Controller/Reviews.php'
]);
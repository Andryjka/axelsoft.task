<?php

use Bitrix\Main\ModuleManager;
use Bitrix\Main\EventManager;

class axelsoft_task extends CModule{

    /** @var EventManager  */
    private EventManager $eventManager;

    public function __construct()
    {
        $arModuleVersion = [];

        if(file_exists(__DIR__. '/version.php')){
            include_once(__DIR__ . '/version.php');
        }

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = 'axelsoft.task';
        $this->MODULE_NAME = 'Axelsoft тестовое задание';
        $this->MODULE_DESCRIPTION = '';
        $this->PARTNER_NAME = 'dev';
        $this->PARTNER_URI = '';

        $this->eventManager = EventManager::getInstance();
    }


    public function DoInstall(): bool
    {
        global $APPLICATION;

        if (ModuleManager::isModuleInstalled('axelsoft.task')) {
            return false;
        }

        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallEvents();

        $APPLICATION->includeAdminFile(
            'Установка',
            __DIR__.'/step.php'
        );


        return true;
    }

    public function DoUninstall(): bool
    {
        global $APPLICATION;

        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->includeAdminFile(
            'Удаление',
            __DIR__.'/unstep.php'
        );

        return true;
    }

    public function InstallEvents(): void
    {
        $this->eventManager->registerEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            CustomProperty::class,
            'GetUserTypeDescription'
        );
    }

    public function UnInstallEvents(): void
    {
        $this->eventManager->unRegisterEventHandler(
            'iblock',
            'OnIBlockPropertyBuildList',
            $this->MODULE_ID,
            CustomProperty::class,
            'GetUserTypeDescription'
        );
    }

}
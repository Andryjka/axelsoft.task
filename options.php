<?php

// получаем идентификатор модуля
use Axelsoft\Task\Helper;
use Bitrix\Main\Config\Option;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;

$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialchars($request['mid'] != '' ? $request['mid'] : $request['id']);
Loader::includeModule($module_id);

$iblockListArr = [];
$iblockList = Helper::getIblockList();
foreach($iblockList as $iblock){
    $iblockListArr[$iblock['ID']] = "[{$iblock['IBLOCK_TYPE_NAME']}] {$iblock['NAME']}";
}

/*
 * Параметры модуля со значениями по умолчанию
 */
$aTabs = [
    [
        'DIV'     => 'edit1',
        'TAB'     => 'Основные настройки',
        'TITLE'   => 'Основные настройки',
        'OPTIONS' => [
            [
                'iblock_id',
                'Инфоблок',
                '',
                [
                    'selectbox',
                    $iblockListArr
                ]
            ],
        ]
    ]
];

/**
 * Форма для редактирования параметров модуля
 */
$tabControl = new CAdminTabControl(
    'tabControl',
    $aTabs
);

$tabControl->begin();
?>
    <form action="<?= $APPLICATION->getCurPage() ?>?mid=<?=$module_id ?>&lang=ru" method="post">
        <?= bitrix_sessid_post() ?>
        <?php
        foreach ($aTabs as $aTab) {
            if ($aTab['OPTIONS']) {
                $tabControl->beginNextTab();
                __AdmSettingsDrawList($module_id, $aTab['OPTIONS']);
            }
        }
        $tabControl->buttons();
        ?>
        <input type="submit" name="apply"
               value="Применить" class="adm-btn-save" />
    </form>

<?php
$tabControl->end();

/*
 * Обрабатываем данные после отправки формы
 */
if ($request->isPost() && check_bitrix_sessid()) {
    foreach($aTabs as $aTab){
        foreach ($aTab['OPTIONS'] as $arOption) {
            if ($request['apply']) {
                $optionValue = $request->getPost($arOption[0]);
                Option::set($module_id, $arOption[0], $optionValue);
            }
        }
    }

    LocalRedirect($APPLICATION->getCurPage().'?mid='.$module_id.'&lang='.LANGUAGE_ID);
}
<?php

namespace Axelsoft\Task;

use Bitrix\Iblock\SectionTable;

class CustomProperty
{
    /**
     * Метод возвращает массив описания собственного типа свойств
     * @return array
     */
    public function GetUserTypeDescription(): array
    {
        return [
            'USER_TYPE_ID' => 'infoblock_list',
            'USER_TYPE' => 'INFOBLOCKS',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => '[dev] Разделы инфоблока',
            'PROPERTY_TYPE' => Iblock\PropertyTable::TYPE_STRING,
            'ConvertToDB' => [__CLASS__, 'ConvertToDB'],
            'ConvertFromDB' => [__CLASS__, 'ConvertFromDB'],
            'GetPropertyFieldHtml' => [__CLASS__, 'GetPropertyFieldHtml'],
        ];
    }

    /**
     * Обработка данных перед сохранением в БД
     * @param $arProperty
     * @param $value
     * @return mixed
     */
    public static function ConvertToDB($arProperty, $value)
    {
        return $value;
    }

    /**
     * Обработка данных при извлечении из БД
     * @param $arProperty
     * @param $value
     * @param string $format
     * @return string
     */
    public static function ConvertFromDB($arProperty, $value, string $format = '')
    {
        return $value;
    }

    /**
     * Представление формы редактирования значения
     */
    public static function GetPropertyFieldHtml($arProperty, $value, $arHtmlControl)
    {

        // Получаем список инфоблоков вместе с названиями типов этих инфоблоков
        $iblockList = Helper::getIblockList();
        $sectionsList = self::getSectionsList();

        $itemId = 'row_' . substr(md5($arHtmlControl['VALUE']), 0, 10); //ID для js
        $fieldName =  htmlspecialcharsbx($arHtmlControl['VALUE']);

        $html = '
            <script>
                function onIblockChanged(selectedIblockId)
                {
                    let sectionSelector = BX("bx-sections-list");
                    for(let i = sectionSelector.length - 1; i >= 0; i--){
                        sectionSelector.remove(i);
                    }
                        
                    let sectionsList = '. \CUtil::PhpToJSObject(self::getSectionsList()).';
                    if(sectionsList[selectedIblockId]){
                        sectionsList[selectedIblockId].forEach(function(section, i, array){
                            let newOption = new Option(section.NAME, section.ID, false, false);
                            sectionSelector.options.add(newOption); 
                        });
                    }
                }
            </script>
        ';

        $select = '<select class="iblock-selector" onchange="onIblockChanged(this.value)">';
        $select .= '<option value="">Не выбрано</option>';
        foreach ($iblockList as $type){
            $select .= '<option value="'. $type['ID'] .'">['. $type['IBLOCK_TYPE_NAME'] .'] ' . $type['NAME'] .'</option>';
        }
        $select .= '</select>';


        $html .= '<div class="iblock-list">';
        $html .= $select;
        $html .= '</div><br/>';


        $html .= '<div class="property_row" id="'. $itemId .'">';
        $selectSections = '<select id="bx-sections-list" name="'. $fieldName .'[IBLOCK_SECTION_ID]">>';
        $selectSections .= '<option value="">Выберите инфоблок</option>';
        foreach ($sectionsList as $section){
            $selectSections .= '<option value="'. $section['ID'] .'">'. $section['NAME'] .'</option>';
        }

        $selectSections .= '</select>';
        $html .= $selectSections;
        $html .= '</div><br/>';

        return $html;
    }


    public static function getSectionsList(): array
    {
        $sectionsList = [];

        $sections = SectionTable::getList([
           'select' => [
               'ID',
               'NAME',
               'DEPTH_LEVEL',
               'IBLOCK_ID'
           ],
           'filter' => [
               'ACTIVE' => 'Y'
           ]
       ])->fetchAll();

       foreach($sections as $section){
           $sectionsList[$section['IBLOCK_ID']][] = $section;
       }

       return $sectionsList;
    }
}
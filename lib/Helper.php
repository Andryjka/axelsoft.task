<?php

namespace Axelsoft\Task;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\Entity\ReferenceField;

class Helper
{
    /**
     * Получение списка инфоблоков с названием их типов
     * @return array
     */
    public static function getIblockList(): array
    {
        return IblockTable::getList([
            'select' => [
                'ID',
                'NAME',
                'IBLOCK_TYPE_NAME' => 'IBLOCK_TYPES.LANG_MESSAGE.NAME'
            ],
            'filter' => [
                '=IBLOCK_TYPES.LANG_MESSAGE.LANGUAGE_ID' => 'ru'
            ],
            'runtime' => [
                new ReferenceField(
                    'IBLOCK_TYPES',
                    TypeTable::class,
                    ['=this.IBLOCK_TYPE_ID' => 'ref.ID'],
                    ['join_type' => 'LEFT']
                )
            ],
            'cache' => [
                'ttl' => 3600
            ]
        ])->fetchAll();
    }
}
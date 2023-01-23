<?php

namespace Axelsoft\Task\Controller;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\CurrentUser;
use \Bitrix\Main\Error;

class Reviews extends Controller
{
    /** @var int  */
    private const DEFAULT_LIMIT = 10;
    /** @var int  */
    private const DEFAULT_OFFSET = 0;

    /**
     * axelsoft:task.api.Reviews.getItems
     * Действие контроллера на получение элементов инфоблока
     * получаем параметры в запросе. с помощью них настраиваем пагинацию
     * @return array|null
     */
    public function getItemsAction(){
        $request = $this->getRequest();

        if(!CurrentUser::get()->getId()){
            $this->addError(new Error('Неавторизован'));
            return null;
        }

        $items = \Bitrix\Iblock\ElementTable::getList([
            'select' => ['*'],
            'filter' => [
                'IBLOCK_ID' => (int) Option::get($this->getModuleId(), 'iblock_id')
            ],
            'limit' => $request['fields']['limit']  ?? self::DEFAULT_LIMIT,
            'offset' => $request['fields']['page'] ?? self::DEFAULT_OFFSET,
            'count_total' => true
        ]);
        if(!$items){
            $this->addError(new Error('Элементы не найдены'));
            return null;
        }

        return [
            'list' => $items->fetchAll(),
            'all_count' => $items->getCount(),
        ];
    }
}
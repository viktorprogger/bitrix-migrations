<?php

namespace Arrilot\BitrixMigrations\Autocreate\Handlers;

use Bitrix\Main\Entity\Event;

class HighloadblockModuleHandlers extends AbstractHandlers
{
    /**
     * Create migration OnBeforeIBlockAdd.
     *
     * @param Event $event
     *
     * @return bool
     */
    public function onBeforeUpdateHandler(Event $event)
    {
        $params = $event->getParameters();
        echo "<pre>"; var_dump($params); echo "</pre>";die();
    }
}

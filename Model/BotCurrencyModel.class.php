<?php
    class BotcurrencyModel extends FLModel {

        function getByChatBotId ($chat_bot_id, $currency, $limit = 0)
        {
            $where = array (
                'ORDER' => 'id'
            );
            $where['AND']['is_del'] = 0;
            $where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['currency'] = $currency;
            
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('bot_currency', '*', $where);
            return $ret;
        }
    }

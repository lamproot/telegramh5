<?php
    class AdvertModel extends FLModel {

        function add ($bot_id, $from_id, $from_username)
        {
            return $this->db->insert ('bot_advert', [
                'bot_id' => $bot_id,
                'from_id' => $from_id,
                'from_username' => $from_username,
                'type' => 1,
                'created_at' => time()
            ]);
        }

        function find ($bot_id, $from_id)
        {
            return $this->db->get ('bot_advert', '*', [
                'type' => 1,
                'bot_id' => $bot_id,
                'from_id' => $from_id
            ]);
        }

        function my_find ($chat_bot_id, $limit = 0)
        {
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['status'] = 1;
            $where['AND']['is_del'] = 0;
            //$status === NULL ? : $where['AND']['status'] = $status;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('bot_advert', '*', $where);
            return $ret;
        }
    }

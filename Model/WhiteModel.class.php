<?php
    class WhiteModel extends FLModel {

        function add ($bot_id, $from_id, $from_username)
        {
            return $this->db->insert ('white', [
                'bot_id' => $bot_id,
                'from_id' => $from_id,
                'from_username' => $from_username,
                'type' => 1,
                'created_at' => time()
            ]);
        }

        function find ($bot_id, $from_id)
        {
            return $this->db->get ('white', '*', [
                'type' => 1,
                'bot_id' => $bot_id,
                'from_id' => $from_id
            ]);
        }

        function my_find ($bot_id, $from_id, $limit = 0)
        {
            $where = array (
                'ORDER' => 'id'
            );
            $bot_id === NULL ? : $where['AND']['bot_id'] = $bot_id;
            $from_id === NULL ? : $where['AND']['from_id'] = $from_id;
            //$status === NULL ? : $where['AND']['status'] = $status;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('white', '*', $where);
            return $ret;
        }
    }

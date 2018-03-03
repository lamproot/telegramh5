<?php
    class CommandModel extends FLModel {

        function find ($chat_bot_id = NULL, $cmd = NULL, $type = 1, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $cmd === NULL ? : $where['AND']['cmd'] = $cmd;
            $type === NULL ? : $where['AND']['type'] = $type;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('chat_command', '*', $where);
            return $ret;
        }

        function findall ($chat_bot_id = NULL, $cmd = NULL, $type = 1, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $cmd === NULL ? : $where['AND']['cmd'] = $cmd;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('chat_command', '*', $where);
            return $ret;
        }


        function getcommand ($chat_id, $cmd)
        {
            return $this->db->get ('chat_command', 'content', [
                'type' => 2
            ]);
        }
    }

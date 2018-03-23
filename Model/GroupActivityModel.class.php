<?php
    class GroupActivityModel extends FLModel {

        // function find ($chat_id = NULL, $cmd = NULL, $type = 1, $limit = 0)
        // {
        //     /** 查询 */
        //     $where = array (
        //         'ORDER' => 'id'
        //     );
        //     $chat_id === NULL ? : $where['AND']['chat_id'] = $chat_id;
        //     $cmd === NULL ? : $where['AND']['cmd'] = $cmd;
        //     //$type === NULL ? : $where['AND']['type'] = $type;
        //     if ($limit != 0) {
        //         $where['LIMIT'] = $limit;
        //     }
        //     $ret = $this->db->select ('chat_command', '*', $where);
        //     return $ret;
        // }


        function getGroupActivityByChatId ($chat_bot_id)
        {
            // return $this->db->get ('group_activity', '*', [
            //     'chat_bot_id' => intval($chat_bot_id),
            //     'type' => 1,
            //     'is_del' => 0
            // ]);

            $where = array (
                'ORDER' => 'id'
            );
            $where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['type'] = 1;
            $where['AND']['is_del'] = 0;

            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('group_activity', '*', $where);
            return $ret;


        }


    }

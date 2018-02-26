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


        function getGroupActivityByChatId ($chat_id)
        {
            return $this->db->get ('group_activity', '*', [
                'chat_id' => $chat_id
            ]);
        }
    }

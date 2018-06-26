<?php
    class NewsTotalModel extends FLModel {

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


        function updateTotalByChatId ($chat_id)
        {
            $where['AND']['chat_id'] = $chat_id;
            $res = $this->db->update ('activity', [
                "total[+]" => 1
            ], $where);
        }


    }

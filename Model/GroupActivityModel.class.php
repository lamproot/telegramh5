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


        function updateTotalRateById ($id, $subtract)
        {
            $where['AND']['id'] = $id;
            $res = $this->db->update ('activity', [
                "total_rate[-]" => $subtract,
                "updated_at" => time()
            ], $where);
            $errorModel = new ErrorModel;
            $errorModel->sendError (MASTER, $res);

        }

        /*
            修改活动结束时间
        */
        function updateActivityStopedatById ($id)
        {
            $where['AND']['id'] = $id;
            $res = $this->db->update ('activity', [
                "stoped_at" => time(),
                "updated_at" => time()
            ], $where);
        }

        function getGroupActivityByChatId ($chat_bot_id, $limit = 0)
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

        function getGroupActivityById ($id)
        {
            return $this->db->get ('group_activity', '*', [
                'id' => $id
            ]);
        }


    }

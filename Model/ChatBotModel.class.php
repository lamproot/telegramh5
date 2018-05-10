<?php
    class ChatBotModel extends FLModel {

        function getcommand ($chat_id, $limit = 0)
        {
            return $this->db->get ('chat_bot', '*', [
                'chat_id' => $chat_id
            ]);
            /** 查询 */
            // $where = array (
            //     'ORDER' => 'id'
            // );
            // $chat_id === NULL ? : $where['AND']['chat_id'] = $chat_id;
            //
            // if ($limit != 0) {
            //     $where['LIMIT'] = $limit;
            // }
            // $ret = $this->db->select ('chat_bot', '*', $where);
            // return $ret;

        }

        function getChatList ($limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $where['AND']['is_del'] = 0;
            
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('chat_bot', 'tokenman_name', $where);
            return $ret;

        }

        function getById ($id)
        {
            return $this->db->get ('chat_bot', '*', [
                'id' => $id
            ]);

        }

        function getByChatId ($chat_id)
        {
            return $this->db->get ('chat_bot', '*', [
                'chat_id' => $chat_id
            ]);

        }

        function updateById ($bot_id, $master_id, $chat_id)
        {
            $where['AND']['id'] = $bot_id;
            $res = $this->db->update ('chat_bot', [
                'chat_id' => $chat_id,
                'master_id' => $master_id
            ], $where);
        }
    }

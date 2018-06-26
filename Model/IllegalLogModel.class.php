<?php
    class IllegalLogModel extends FLModel {

        function add ($chat_bot_id, $message_id, $content, $from_id, $from_username, $first_name, $last_name, $chat_id)
        {
            // $errorModel = new ErrorModel;
            // $errorModel->sendError (MASTER, "dasdas");
            $this->db->insert ('illega_log', [
                'chat_bot_id' => $chat_bot_id,
                'message_id' => $message_id,
                'created_at' => time(),
                'content' => $content,
                'from_id' => $from_id,
                'from_username' => $from_username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'chat_id' => $chat_id
            ]);
        }

        function getcount ($chat_bot_id, $from_id)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $from_id === NULL ? : $where['AND']['from_id'] = $from_id;
            $count = $this->db->count ('illega_log', $where);
            return $count;
        }
    }

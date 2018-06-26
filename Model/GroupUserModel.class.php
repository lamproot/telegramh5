<?php
    class GroupUserModel extends FLModel {
        //$chat_bot_id, $chat['id'], 1, $from_id, $first_name, $last_name, $username
        function create ($chat_bot_id, $chat_id, $type, $from_id, $first_name, $last_name, $username)
        {
            $this->db->insert ('group_user', [
                'chat_bot_id' => $chat_bot_id,
                'chat_id' => $chat_id,
                'type' => $type,
                'from_id' => $from_id,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'username' => $username,
                'created_at' => time()
            ]);
        }

        function updateByFromId ($chat_bot_id, $chat_id, $from_id, $type = 2)
        {
            $where['AND']['chat_id'] = $chat_id;
            $where['AND']['from_id'] = $from_id;
            $res = $this->db->update ('group_user', [
                'type' => $type,
                'updated_at' => time()
            ], $where);
        }
    }

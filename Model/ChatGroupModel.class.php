<?php
    class ChatGroupModel extends FLModel {

        function create ($title, $chat_bot_id, $status, $admin_id, $chat_id)
        {
            $this->db->insert ('chat_group', [
                'chat_bot_id' => $chat_bot_id,
                'title' => $title,
                'status' => $status,
                'created_at' => time(),
                'admin_id' => $admin_id,
                'chat_id' => $chat_id
            ]);
        }
    }

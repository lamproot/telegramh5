<?php
    class CodeLogModel extends FLModel {

        function add ($chat_bot_id, $message_id, $code, $content, $from_id, $from_username)
        {
            $this->db->insert ('user_code_log', [
                'chat_bot_id' => $chat_bot_id,
                'message_id' => $message_id,
                'code' => $code,
                'created_at' => time(),
                'content' => $content,
                'from_id' => $from_id,
                'from_username' => $from_username
            ]);
        }
    }

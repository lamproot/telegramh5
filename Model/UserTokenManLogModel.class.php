<?php
    class UserTokenManLogModel extends FLModel {

        function add ($chat_bot_id, $from_id, $from_username, $first_name, $last_name, $ip, $agent)
        {
            $this->db->insert ('user_tokenman_log', [
                'chat_bot_id' => $chat_bot_id,
                'created_at' => time(),
                'from_id' => $from_id,
                'from_username' => $from_username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'ip' => $ip,
                'agent' => $agent
            ]);
        }
    }

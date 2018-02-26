<?php
    class ChatBotModel extends FLModel {

        function getcommand ($chat_id)
        {
            return $this->db->get ('chat_bot', 'code_cmd', [
                'chat_id' => $chat_id
            ]);
        }
    }

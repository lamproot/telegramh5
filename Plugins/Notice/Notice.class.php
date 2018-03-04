<?php
    class Notice extends Base {
        public function new_member ($new_member, $message_id, $from, $chat, $date) {
            // $str = '@' . @$from['username'] . ' 邀请了 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . ' 玩' . "\n";
            // $str .= '欢迎 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . '  玩(ฅ>ω<*ฅ)';
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
            $chatBotModel = new ChatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $command = "/new_member";
            //创建欢迎消息
            $commandModel = new CommandModel;
            $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);

            //消息调试
            // $errorModel = new ErrorModel;
            // $errorModel->sendError (MASTER, $commandInfo[0]['content']);
            //type =  1 文字回复  2 code 码回复 3 图片文字回复
            $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
            if ($message) {
                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );
            }
        }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            // $str = '喵喵喵？ @' . $left_member['username'] . ' 被 @' . @$from['username'] . ' 移出了 ' . $chat['title'];
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
        }
    }

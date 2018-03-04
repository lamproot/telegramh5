<?php
    class Basic extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            # 步骤
            //1.查询是否以 / 开头 正则匹配
            //$search = "/^\//i"; preg_match($search,$command,$result)
            if($command[0] == "/") {
                $errorModel = new errorModel;
                $chatBotModel = new chatBotModel;
                $chatBot = $chatBotModel->getcommand($chat['id']);
                $errorModel->sendError (MASTER, "chat_id".print_r($chat['id'], true));
                $errorModel->sendError (MASTER, "chatBot".print_r($chatBot, true));
                $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

                //查询命令是否有回复
                $commandModel = new commandModel;
                $commandInfo = $commandModel->findall($chat_bot_id, $command, 1, 1);

                if ($commandInfo && $commandInfo[0] && $commandInfo[0]['type']) {

                    //type =  1 文字回复  2 code 码回复 3 图片文字回复 4 文件回复
                    if ($commandInfo[0]['type'] == 1) {
                        $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
                        if ($message) {
                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                        }
                    }

                    if ($commandInfo[0]['type'] == 3) {
                        $copyright = $commandInfo[0]['content'] ? $commandInfo[0]['content'] : "";
                        $url = $commandInfo[0]['url'] ? $commandInfo[0]['url'] : "";
                        if ($url) {
                            $this->telegram->sendPhoto ($chat['id'], $url, $copyright, $message_id);
                        }
                    }

                    if ($commandInfo[0]['type'] == 4) {
                        $copyright = $commandInfo[0]['content'] ? $commandInfo[0]['content'] : "";
                        $url = $commandInfo[0]['url'] ? $commandInfo[0]['url'] : "";
                        if ($url) {
                            $this->telegram->sendDocument ($chat['id'], $url, $copyright, $message_id);
                        }
                    }

                }
            }
        }

        public function new_member ($new_member, $message_id, $from, $chat, $date) {
            // $command = "new_member";
            // //创建欢迎消息
            // $commandModel = new commandModel;
            // $commandInfo = $commandModel->find($chat['id'], $command, 1, 1);
            //
            // //消息调试
            // // $errorModel = new ErrorModel;
            // // $errorModel->sendError (MASTER, $commandInfo[0]['content']);
            // //type =  1 文字回复  2 code 码回复 3 图片文字回复
            // $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
            // if ($message) {
            //     $this->telegram->sendMessage (
            //         $chat['id'],
            //         $message,
            //         $message_id
            //     );
            // }
            // $str = '@' . @$from['username'] . ' 邀请了 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . ' 玩' . "\n";
            // $str .= '欢迎 @' . $new_member['username'] . ' 来到 ' . $chat['title'] . '  玩(ฅ>ω<*ฅ)';
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
        }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            // $str = '喵喵喵？ @' . $left_member['username'] . ' 被 @' . @$from['username'] . ' 移出了 ' . $chat['title'];
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
            $chatBotModel = new chatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $codeModel = new codeModel;
            $codeModel->updateByFromId($chat_bot_id, @$from['id']);

        }
    }

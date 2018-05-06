<?php
    class Whoami extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/whoami') {
                $str = '你的 master_id：' . print_r($from, true) . "\n";
                $str .= '群的 chat_id：' . print_r($chat, true) . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";
                $this->telegram->sendMessage (MASTER, $str, $message_id);
            }

<<<<<<< HEAD
=======
            //激活机器人 输入激活码  查询机器人是否已激活 已激活-返回已激活 未激活返回未激活状态
            // 是否激活  is_activate  是否工作 is_work 是否工作
            if ($command == '/activatebot') {
                $str = "已激活";
                $str .= '你的 master_id：' . $from['id'] . "\n";
                $str .= '群的 chat_id：' . $chat['id'] . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";
                $this->telegram->sendMessage (MASTER, $str, $message_id);
            }

            $search = "/^\/whoami/i";
>>>>>>> d523dffd477439f51230d8652f4bb466c73aa98a


            if ($command == '/activatebot') {
                $str = '你的 master_id：' . print_r($from, true) . "\n";
                $str .= '群的 chat_id：' . $chat['id'] . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";

                $chatBotModel = new ChatBotModel;
                $chatBot = $chatBotModel->getById($chat['id']);

                if ($chatBot) {
                    $_SESSION['token'] = $chatBot['token'];
                }
                $this->telegram->sendMessage ($chat['id'], $str, $message_id);
            }

            // $search = "/^\/whoami/i";
            //
            // if(preg_match($search,$command,$result)) {
            //     $_SESSION['token'] = str_replace($result[0], "", $command);
            //
            //     $str = '你的 master_id：' . $from['id'] . "\n";
            //     $str .= '群的 chat_id：' . $chat['id'] . "\n";
            //     $str .= '这条消息的 id：' . $message_id . "\n";
            //     $this->telegram->sendMessage ($chat['id'], $str, $message_id);
            // }




        }
    }

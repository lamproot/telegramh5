<?php
    class Whoami extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            // if ($command == '/whoami') {
            //     $str = '你的 master_id：' . $from['id'] . "\n";
            //     $str .= '群的 chat_id：' . $chat['id'] . "\n";
            //     $str .= '这条消息的 id：' . $message_id . "\n";
            //     $this->telegram->sendMessage (MASTER, $str, $message_id);
            //
            //     $this->token
            // }

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

            if(preg_match($search,$command,$result)) {
                $_SESSION['token'] = str_replace($result[0], "", $command);

                $str = '你的 master_id：' . $from['id'] . "\n";
                $str .= '群的 chat_id：' . $chat['id'] . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";
                $this->telegram->sendMessage ($chat['id'], $str, $message_id);
            }

        }
    }

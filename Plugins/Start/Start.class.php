<?php
    class Start extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            // if ($command == '/start' || $command == '/help' ) {
            //     $message = "欢迎阅读使用手册 命令如下:\n/ping 连接\n/hitokoto 文字\n/bing 图片\n/whoami 我是谁\n/code Code码处理\n/me 我的-Code码";
            //     $this->telegram->sendMessage (
            //         $chat['id'],
            //         $message,
            //         $message_id
            //     );
            // }

            if ($command == '/start') {
                //记录用户使用TokenMan数据
                if ($chat['type'] == 'private') {
                    $str = "激活失败 请在机器人所在的群组激活!";
                }
            }
        }
    }

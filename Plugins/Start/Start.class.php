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
            //记录用户使用TokenMan数据
            if ($chat['type'] == 'private' && $command == '/start') {
                    //$str = "激活失败 请在机器人所在的群组激活!";
                    $chat_bot_id = $_GET['bot_id'];
                    $UserTokenManLogModel = new UserTokenManLogModel;

                    $username = isset($from['username']) ? $from['username'] : "";
                    $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                    $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                    $ip = "";
                    $agent = "";
                    $commandFind = $UserTokenManLogModel-> add ($chat_bot_id, $from['id'], $username, $first_name, $last_name, $ip, $agent);
            }
        }

        //不同环境下获取真实的IP
        function get_ip(){
            //判断服务器是否允许$_SERVER
            if(isset($_SERVER)){
                if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
                    $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
                    $realip = $_SERVER["HTTP_CLIENT_IP"];
                }else{
                    $realip = $_SERVER["REMOTE_ADDR"];
                }
            }else{
                //不允许就使用getenv获取
                if(getenv("HTTP_X_FORWARDED_FOR")){
                      $realip = getenv( "HTTP_X_FORWARDED_FOR");
                }elseif(getenv("HTTP_CLIENT_IP")) {
                      $realip = getenv("HTTP_CLIENT_IP");
                }else{
                      $realip = getenv("REMOTE_ADDR");
                }
            }

            return $realip;
        }
    }

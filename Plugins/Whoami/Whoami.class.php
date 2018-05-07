<?php
    class Whoami extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            // if ($command == '/whoami') {
            //     $str = '你的 master_id：' . print_r($from, true) . "\n";
            //     $str .= '群的 chat_id：' . print_r($chat, true) . "\n";
            //     $str .= '这条消息的 id：' . $message_id . "\n";
            //     $this->telegram->sendMessage (MASTER, $str, $message_id);
            // }


            //激活机器人 输入激活码  查询机器人是否已激活 已激活-返回已激活 未激活返回未激活状态
            // 是否激活  is_activate  是否工作 is_work 是否工作

            if ($command == '/activatebot') {
                $chatBotModel = new ChatBotModel;
                $chat_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : $chat['id'];
               
                if (isset($_GET['bot_id'])) {
                    $chatBot = $chatBotModel->getById($chat_id);
                }else{
                    $chatBot = $chatBotModel->getByChatId($chat_id);
                }

                if ($chatBot) {
                    $_SESSION['token'] = $chatBot['token'];
                }

                //私聊禁止激活
                if ($chat['type'] == 'private') {
                    $str = "激活失败 请在机器人所在的群组激活!";
                }else{
                    $str = "激活成功 \n 欢迎使用 TokenMan 智能机器人!";
                }
  
                $button = json_encode (array (
                    'inline_keyboard' => array (
                        array (array (

                            'text' => 'TokenMan AI Bot',
                            'url' => 'https://twitter.com/IamTokenMan'
                        ))
                    )
                ));    
                //$button = array();

                $this->telegram->sendMessage ($chat['id'], $str, $message_id, $button);                
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

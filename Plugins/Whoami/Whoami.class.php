<?php
    class Whoami extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/whoami') {

                $str = '你的 master_id：' . $from['id']. "\n";
                $str .= '群的 chat_id：' . $chat['id'] . "\n";
                $str .= '这条消息的 id：' . $message_id . "\n";
               
                $this->telegram->sendMessage (MASTER, $str, $message_id);
            }


            //激活机器人 输入激活码  查询机器人是否已激活 已激活-返回已激活 未激活返回未激活状态
            // 是否激活  is_activate  是否工作 is_work 是否工作

            if ($command == '/activatebot') {
                $errorModel = new ErrorModel;
               // $errorModel->sendError (MASTER, print_r($chatBot, true));exit;
                //私聊禁止激活
                if ($chat['type'] == 'private') {
                    $str = "激活失败 请在机器人所在的群组激活!";
                }else{

                    //获取机器人已激活的群

                    //获取群信息
                    $chat_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : $chat['id'];
                    $chatBotModel = new ChatBotModel;
                    $chatGroupModel = new ChatGroupModel;
                    $chatBot = $chatBotModel->getById($chat_id);

                    if ($chatBot && $chatBot['chat_id'] != "") {
                        //return;
                        $str = "TokenMan 已在使用中 如有问题请联系管理员!";
                    }else{
                        $chatBotModel->updateById($chat_id, $from['id'], $chat['id']);
                        //添加机器人管理群

                        $chatGroupModel->create(@$chat['title'], $chat_id, 1, @$chatBot['admin_id'], @$chat['id']);
                        $str = "激活成功 \n 欢迎使用 TokenMan 智能机器人!";
                    }
                    
                }
                $this->telegram->sendMessage ($chat['id'], $str, $message_id);
            }

            //$search = "/^\/whoami/i";

            // if($command == '/activatebot') {
            //     //私聊禁止激活
            //     if ($chat['type'] == 'private') {
            //         $str = "激活失败 请在机器人所在的群组激活!";
            //     }else{
            //         $str = "激活成功 \n 欢迎使用 TokenMan 智能机器人!";
            //     }
            //     //$button = array ();
            //     $this->telegram->sendMessage ($chat['id'], $str, $message_id);

            // }
        }
    }

<?php
    class Notice extends Base {
        public function new_member ($new_member, $message_id, $from, $chat, $date) {
            $chatBotModel = new ChatBotModel;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 0;
            $blank = ["bot","Bot","Token","token","Admin","admin","资询","客服","交易平台","手续费","返利","币种","邀请","等你拿","官网","搜索","名称","拉人","微信","粉丝","电报","糖果","candy"];

            $errorModel = new ErrorModel;

            $username = isset($new_member['username']) ? $new_member['username'] : "";
            $from_id = isset($new_member['id']) ? $new_member['id'] : "";
            $first_name = isset($new_member['first_name']) ? $new_member['first_name'] : "";
            $last_name = isset($new_member['last_name']) ? $new_member['last_name'] : "";

            //添加加入用户记录
            $groupUserModel = new GroupUserModel;
            $groupUserModel->create($chat_bot_id, $chat['id'], 1, $from_id, $first_name, $last_name, $username);
             // $errorModel->sendError (MASTER, print_r($new_member, true));
            $chatBotList = $chatBotModel->getChatList();
            $chatBotArray = [];
            foreach ($chatBotList as $key => $value) {
                $chatBotArray[] = $value;
            }

            //判断是否为 TokenMan机器人
            //查询所有TokenMan机器人
            if (in_array($username, $chatBotArray)) {
                # code...
            }else{
                foreach ($blank as $key => $value) {
                    $addblank = false;

                    $find = stripos(strtolower($username), $value);
                    if ($find !== false) {
                        $addblank = true;
                    }

                    $find = stripos($first_name, $value);
                    if ($find !== false) {
                        $addblank = true;
                    }

                    $find = stripos($last_name, $value);
                    if ($find !== false) {
                        $addblank = true;
                    }

                    if ($username == "" && $first_name == "" && $last_name == "") {
                        $addblank = true;
                    }

                    if ($addblank) {
                        $IllegalLogModel = new IllegalLogModel();
                        $this->telegram->kickChatMember (
                            $chat['id'],
                            $new_member['id']
                        );
                        $IllegalLogModel->add ($chat_bot_id, $message_id, "用户名含有违禁词", $new_member['id'], $username, $first_name, $last_name, $chat['id']);

                    }
                }
            }

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

            //删除入群消息
            $this->telegram->deleteMessage (
                $chat['id'],
                $message_id
            );
        }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            //获取退群处理
            // $str = '喵喵喵？ @' . $left_member['username'] . ' 被 @' . @$from['username'] . ' 移出了 ' . $chat['title'];
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
        }
    }

<?php
    class Basic extends Base {

        public function command ($command, $param, $message_id, $from, $chat, $date) {
            # 步骤
            //1.查询是否以 / 开头 正则匹配
            //$search = "/^\//i"; preg_match($search,$command,$result)
            if($command[0] == "/") {

                $errorModel = new ErrorModel;
                $chatBotModel = new ChatBotModel;
                //$chatBot = $chatBotModel->getcommand($chat['id']);
                $chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;

                //查询是否开启防止活动刷屏 规则  group_bot_config_机器人ID_群ID group_bot_config_2_-1001249040089
                //当用户指令超过两个进行禁言

                //查询命令是否有回复
                $commandModel = new CommandModel;
                $commandInfo = $commandModel->findall($chat_bot_id, $command, 1, 1);

                if ($commandInfo && $commandInfo[0] && $commandInfo[0]['type']) {

                    //type =  1 文字回复  2 code 码回复 3 图片文字回复 4 文件回复
                    if ($commandInfo[0]['type'] == 1) {
                        $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
                        if ($message) {

                            // $button = json_encode (array (
                            //     'inline_keyboard' => array (
                            //         array (array (
                            //
                            //             'text' => "Click here to send code to TokenMan",
                            //             'url' => 'http://t.me/'.$chatBot['tokenman_name']
                            //         ))
                            //     )
                            // ));
                            //查询是否有广告
                            $advertModel = new AdvertModel;
                            $advertFind = $advertModel->my_find($chat_bot_id,1);
                            if ($advertFind && $advertFind[0] && $advertFind[0]['content']) {
                                $button = json_encode (array (
                                    'inline_keyboard' => array (
                                        array (array (
                                            'text' => "asdasd"
                                        ))
                                    )
                                ));
                                $this->telegram->sendMessage (
                                    $chat['id'],
                                    $message,
                                    $message_id,
                                    $button
                                );
                            }else{
                              $this->telegram->sendMessage (
                                  $chat['id'],
                                  $message,
                                  $message_id
                              );
                            }

                        }
                    }

                    if ($commandInfo[0]['type'] == 3) {
                        $copyright = $commandInfo[0]['content'] ? $commandInfo[0]['content'] : "";
                        $url = $commandInfo[0]['url'] ? "http://file.name-technology.fun/" . $commandInfo[0]['url'] : "";
                        if ($url) {
                            $this->telegram->sendPhoto ($chat['id'], $url, $copyright, $message_id);
                        }
                    }

                    if ($commandInfo[0]['type'] == 4) {
                        $copyright = $commandInfo[0]['content'] ? $commandInfo[0]['content'] : "";
                        $url = $commandInfo[0]['url'] ? "http://file.name-technology.fun/" . $commandInfo[0]['url'] : "";
                        if ($url) {
                            $this->telegram->sendDocument ($chat['id'], $url, $copyright, $message_id);
                        }
                    }

                    if ($commandInfo[0]['type'] == 5) {
                        if (isset($commandInfo[0]['content']) && $commandInfo[0]['content']) {

                            $content = json_decode($commandInfo[0]['content'], true);
                            if ($content) {
                                $imgurl = "http://file.name-technology.fun/" . $content[0]['url'];
                                $copyright = $content[0]['note'];
                                $button = json_encode (array (
                                    'inline_keyboard' => array (
                                        array (array (

                                            'text' => '下一张',
                                            'callback_data' => 'bing_1_'.$commandInfo[0]['id']
                                        ))
                                    )
                                ));
                                $this->telegram->sendPhoto ($chat['id'], $imgurl, $copyright, $message_id, $button);
                            }

                        }
                    }
                }
            }
        }

        //$data['message']['photo'],
        public function photo($photo, $caption, $message_id, $from, $chat, $date){
            $chatBotModel = new ChatBotModel;
            $chatBotConfigModel = new ChatBotConfigModel;
            // $chatBot = $chatBotModel->getcommand($chat['id']);
            // $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";
            //$chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;
            $chatBot = $chatBotModel->getById($chat_bot_id);
            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);
            if ($find) {
                return true;
            }

            //查询是否配置-删除图片类消息
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            if ($chatBotConfig && isset($chatBotConfig['is_delete_photo']) && $chatBotConfig['is_delete_photo']) {
                $message = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
                $command = "/datacleaning";
                //查询数据清除返回文案
                $commandModel = new CommandModel;
                $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
                $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $message;

                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );

                $this->telegram->deleteMessage (
                    $chat['id'],
                    $message_id
                );
            }
        }

        public function voice($voice, $message_id, $from, $chat, $date){
            $chatBotModel = new ChatBotModel;
            $chatBotConfigModel = new ChatBotConfigModel;
            // $chatBot = $chatBotModel->getcommand($chat['id']);
            // $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";
            //$chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;
            $chatBot = $chatBotModel->getById($chat_bot_id);
            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);
            if ($find) {
                return true;
            }

            //查询是否配置-删除图片类消息
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            if ($chatBotConfig && isset($chatBotConfig['is_delete_voice']) && $chatBotConfig['is_delete_voice']) {
                $message = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
                $command = "/datacleaning";
                //查询数据清除返回文案
                $commandModel = new CommandModel;
                $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
                $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $message;

                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );

                $this->telegram->deleteMessage (
                    $chat['id'],
                    $message_id
                );
            }
        }


        public function sticker ($sticker, $message_id, $from, $chat, $date) {
            $chatBotModel = new ChatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);
            //$chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";
            $chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;
            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }

            $chatBotConfigModel = new ChatBotConfigModel;
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            if ($chatBotConfig && isset($chatBotConfig['is_delete_sticker']) && $chatBotConfig['is_delete_sticker']) {
            //if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
                $message = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";

                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );

                $this->telegram->deleteMessage (
                    $chat['id'],
                    $message_id
                );
            }
        }




        public function message ($message, $message_id, $from, $chat, $date) {
            $chatBotModel = new ChatBotModel;
            // $chatBot = $chatBotModel->getcommand($chat['id']);
            // $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";
            //$chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;
            $chatBot = $chatBotModel->getById($chat_bot_id);


            $chatBotConfigModel = new ChatBotConfigModel;
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            $errorModel = new ErrorModel;
            // $errorModel->sendError (MASTER, print_r($chatBotConfig, true));exit;

            //根据关键词自动应答
            if ($chatBotConfig && isset($chatBotConfig['is_keyword_cmd']) && $chatBotConfig['is_keyword_cmd']) {
                //keyword_cmd_config
                if ($chatBotConfig && isset($chatBotConfig['keyword_cmd_config']) && $chatBotConfig['keyword_cmd_config']) {
                    $chatBotConfig['keyword_cmd_config'] = json_decode($chatBotConfig['keyword_cmd_config'], true);
                    foreach ($chatBotConfig['keyword_cmd_config'] as $key => $value) {
                        //包含关键词
                        if ((int)$value['type'] == 1 && isset($value['keyword'])) {

                            foreach ($value['keyword'] as $kkey => $kvalue) {
                                $pos = stripos((string)$message, (string)$kvalue);
                                if ($pos !== false) {
                                    $this->telegram->sendMessage (
                                        $chat['id'],
                                        $value['content'],
                                        $message_id
                                    );
                                    return true;
                                }
                            }
                        }
                        //$errorModel->sendError (MASTER, print_r($value['keyword'], true));exit;
                        //等于关键词
                        if ((int)$value['type'] == 2 && isset($value['keyword']) && !empty($value['keyword'])) {
                            //$errorModel->sendError (MASTER, print_r($value['keyword'], true));exit;
                            foreach ($value['keyword'] as $kkey => $kvalue) {
                                if ($message == $kvalue) {
                                    $this->telegram->sendMessage (
                                        $chat['id'],
                                        $value['content'],
                                        $message_id
                                    );
                                    return true;
                                }
                            }
                        }
                    }
                }
                //包含关键词
                // [
                //     {
                //         "keyword":["111","222","3333"],
                //         "content":"回复内容111",
                //         "type":1
                //     },
                //     {
                //         "keyword":["111","222","3333"],
                //         "content":"回复内容2222",
                //         "type":2
                //     }
                // ]
                //等于关键词
            }
            //如果是管理员 或 白名单 - 不做任何限制
            $whiteModel = new WhiteModel;
            $find = $whiteModel->my_find($chat_bot_id, $from['id']);
            if ($find) {
                return true;
            }

            //判断是否开启群禁言 is_clear_all_news  禁言时长 clear_all_news_time
            //禁言期間，群内普通成员仍被允许发送以下文字内容: clear_all_news_white
            //禁言期間，群内普通成员仍被允许发送包含以下关键词的内容: clear_all_news_reg_white
            if ($chatBotConfig && isset($chatBotConfig['is_clear_all_news']) && $chatBotConfig['is_clear_all_news']) {

                //禁言期間，群内普通成员仍被允许发送以下文字内容
                if ($chatBotConfig && isset($chatBotConfig['clear_all_news_white']) && $chatBotConfig['clear_all_news_white']) {

                    $clear_all_news_white = explode(",", $chatBotConfig['clear_all_news_white']);
                    foreach ($clear_all_news_white as $key => $value) {
                        if ($message == $value) {
                            return true;
                        }
                    }
                }

                if ($chatBotConfig && isset($chatBotConfig['clear_all_news_reg_white']) && $chatBotConfig['clear_all_news_reg_white']) {

                    $clear_all_news_reg_white = explode(",", $chatBotConfig['clear_all_news_reg_white']);
                    foreach ($clear_all_news_reg_white as $key => $value) {
                        $pos = stripos($message, $value);
                        if ($pos !== false) {
                            return true;
                        }
                    }
                }

                //禁言期间进行处理
                if ($chatBotConfig && isset($chatBotConfig['clear_all_news_stop_time']) && $chatBotConfig['clear_all_news_stop_time'] && time() < $chatBotConfig['clear_all_news_stop_time']) {
                    $this->telegram->deleteMessage (
                        $chat['id'],
                        $message_id
                    );
                    return true;
                }
            }


            //判断是否清除链接 （链接白名单 TODO）
            //进行链接搜索和数据清理 警告两次禁言 禁言两次直接封禁
            if ($chatBotConfig && isset($chatBotConfig['is_delete_link']) && $chatBotConfig['is_delete_link']) {
                //链接  关键字（敏感词）过滤
                $regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
                $needle= 'http';
                $pos = strripos($message, $needle);
                if (preg_match($regex, $message) || $pos !== false) {
                    $this->clearChatMessage($chat_bot_id, $message, $message_id, $from, $chat, $chatBotConfig);
                    return;
                }
            }

            //根据敏感词封禁成员
            if ($chatBotConfig && isset($chatBotConfig['is_words_ban']) && $chatBotConfig['is_words_ban']) {

                //获取封禁关键词
                if ($chatBotConfig && isset($chatBotConfig['set_ban_words']) && $chatBotConfig['set_ban_words']) {
                    $set_ban_words = explode(",", $chatBotConfig['set_ban_words']);
                    foreach ($set_ban_words as $key => $value) {
                        $pos = stripos($message, $value);
                        if ($pos !== false) {
                            $this->clearChatMessage($chat_bot_id, $message, $message_id, $from, $chat, $chatBotConfig);
                            return;
                        }
                    }
                }
            }



            // if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
            //
            //     //链接  关键字（敏感词）过滤
            //     $regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
            //     $needle= 'http';
            //     $pos = strripos($message, $needle);
            //     if (preg_match($regex, $message) || $pos !== false) {
            //         $sendmessage = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
            //         $command = "/datacleaning";
            //         //查询数据清除返回文案
            //         $commandModel = new CommandModel;
            //         $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
            //         $sendmessage = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $sendmessage;
            //
            //         $IllegalLogModel = new IllegalLogModel;
            //
            //         //查询违规数据是否大于3次
            //         $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
            //         if ($count >= 1) {
            //             $this->telegram->kickChatMember (
            //                 $chat['id'],
            //                 $from['id']
            //             );
            //             $username = isset($from['username']) ? $from['username'] : "";
            //             $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //             $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //             $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //             $codeModel = new CodeModel;
            //             $codeModel->updateByFromId($chat_bot_id, $from['id']);
            //
            //             $this->telegram->sendMessage (
            //                 $chat['id'],
            //                 $sendmessage,
            //                 $message_id
            //             );
            //
            //             $this->telegram->deleteMessage (
            //                 $chat['id'],
            //                 $message_id
            //             );
            //
            //             return;
            //         }
            //
            //         //记录相关违规数据
            //         $username = isset($from['username']) ? $from['username'] : "";
            //         $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //         $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //         $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //         $this->telegram->sendMessage (
            //             $chat['id'],
            //             $sendmessage,
            //             $message_id
            //         );
            //
            //         $this->telegram->deleteMessage (
            //             $chat['id'],
            //             $message_id
            //         );
            //
            //     }else{
            //         $result = $this->get_tags_arr($message);
            //         $sensitiveWordsModel = new SensitiveWordsModel;
            //
            //         if ($result) {
            //
            //             $wordresult = false;
            //             foreach ($result as $key => $value) {
            //                 $sensitiveWord = $sensitiveWordsModel->find($value);
            //                 if ($sensitiveWord) {
            //                     $wordresult = true;
            //                 }
            //             }
            //
            //             if ($wordresult) {
            //                 $sendmessage = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
            //                 $command = "/datacleaning";
            //                 //查询数据清除返回文案
            //                 $commandModel = new CommandModel;
            //                 $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
            //                 $sendmessage = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $sendmessage;
            //
            //                 $IllegalLogModel = new IllegalLogModel;
            //
            //                 //查询违规数据是否大于3次
            //                 $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
            //                 if ($count >= 1) {
            //                     $this->telegram->kickChatMember (
            //                         $chat['id'],
            //                         $from['id']
            //                     );
            //                     $username = isset($from['username']) ? $from['username'] : "";
            //                     $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                     $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                     $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                     $codeModel = new CodeModel;
            //                     $codeModel->updateByFromId($chat_bot_id, $from['id']);
            //                     return;
            //                 }
            //
            //                 //记录相关违规数据
            //                 $username = isset($from['username']) ? $from['username'] : "";
            //                 $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                 $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                 $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                 $this->telegram->sendMessage (
            //                     $chat['id'],
            //                     $sendmessage,
            //                     $message_id
            //                 );
            //
            //                 $this->telegram->deleteMessage (
            //                     $chat['id'],
            //                     $message_id
            //                 );
            //             }else{
            //                 $word = $sensitiveWordsModel->find([$message]);
            //                 if ($word) {
            //                     $sendmessage = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
            //                     $command = "/datacleaning";
            //                     //查询数据清除返回文案
            //                     $commandModel = new CommandModel;
            //                     $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
            //                     $sendmessage = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $sendmessage;
            //
            //                     $IllegalLogModel = new IllegalLogModel;
            //
            //                     //查询违规数据是否大于3次
            //                     $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
            //                     if ($count >= 1) {
            //                         $this->telegram->kickChatMember (
            //                             $chat['id'],
            //                             $from['id']
            //                         );
            //                         $username = isset($from['username']) ? $from['username'] : "";
            //                         $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                         $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                         $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                         $codeModel = new CodeModel;
            //                         $codeModel->updateByFromId($chat_bot_id, $from['id']);
            //                         return;
            //                     }
            //
            //                     //记录相关违规数据
            //                     $username = isset($from['username']) ? $from['username'] : "";
            //                     $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                     $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                     $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                     $this->telegram->sendMessage (
            //                         $chat['id'],
            //                         $sendmessage,
            //                         $message_id
            //                     );
            //
            //                     $this->telegram->deleteMessage (
            //                         $chat['id'],
            //                         $message_id
            //                     );
            //                 }
            //             }
            //
            //         }else{
            //             $word = $sensitiveWordsModel->find([$message]);
            //             if ($word) {
            //                 $sendmessage = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
            //                 $command = "/datacleaning";
            //                 //查询数据清除返回文案
            //                 $commandModel = new CommandModel;
            //                 $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
            //                 $sendmessage = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $sendmessage;
            //
            //                 $IllegalLogModel = new IllegalLogModel;
            //
            //                 //查询违规数据是否大于3次
            //                 $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
            //                 if ($count >= 1) {
            //                     $this->telegram->kickChatMember (
            //                         $chat['id'],
            //                         $from['id']
            //                     );
            //                     $username = isset($from['username']) ? $from['username'] : "";
            //                     $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                     $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                     $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                     $codeModel = new CodeModel;
            //                     $codeModel->updateByFromId($chat_bot_id, $from['id']);
            //                     return;
            //                 }
            //
            //                 //记录相关违规数据
            //                 $username = isset($from['username']) ? $from['username'] : "";
            //                 $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //                 $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //                 $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name, $chat['id']);
            //
            //                 $this->telegram->sendMessage (
            //                     $chat['id'],
            //                     $sendmessage,
            //                     $message_id
            //                 );
            //
            //                 $this->telegram->deleteMessage (
            //                     $chat['id'],
            //                     $message_id
            //                 );
            //             }
            //         }
            //     }
            //
            //
            //
            // }
        }

        public function document ($document, $message_id, $from, $chat, $date) {
            $chatBotModel = new ChatBotModel;
            // $chatBot = $chatBotModel->getcommand($chat['id']);
            // $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";
            //$chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;

            $chatBot = $chatBotModel->getById($chat_bot_id);
            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }

            $chatBotConfigModel = new ChatBotConfigModel;
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            if ($chatBotConfig && isset($chatBotConfig['is_delete_document']) && $chatBotConfig['is_delete_document']) {
            //if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
                $message = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
                $command = "/datacleaning";
                //查询数据清除返回文案
                $commandModel = new CommandModel;
                $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
                $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $message;

                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );

                $this->telegram->deleteMessage (
                    $chat['id'],
                    $message_id
                );
            }
        }




        //public function new_member ($new_member, $message_id, $from, $chat, $date) {
            //查询用户名是否含有违禁词 bot Bot Token token Admin admin 这6个呗
            // $blank = ["bot","Bot","Token","token","Admin","admin"];
            //
            // $errorModel = new ErrorModel;
            // $errorModel->sendError (MASTER, "new member");
            //
            // for ($i=0; $i < count($blank); $i++) {
            //     $username = isset($new_member['username']) ? $new_member['username'] : "";
            //     $first_name = isset($new_member['first_name']) ? $new_member['first_name'] : "";
            //     $last_name = isset($new_member['last_name']) ? $new_member['last_name'] : "";
            //     $errorModel->sendError (MASTER, $new_member);
            //     $find = stripos($username, $value);
            //
            //     if ($find) {
            //         // $IllegalLogModel = new IllegalLogModel();
            //         // $this->telegram->kickChatMember (
            //         //     $chat['id'],
            //         //     $new_member['id']
            //         // );
            //         // $IllegalLogModel->add ($chat_bot_id, $message_id, "用户名是否含有违禁词", $new_member['id'], $username, $first_name, $last_name);
            //     }
            //     exit;
            // }
            // foreach ($blank as $key => $value) {
            //
            //     $username = isset($new_member['username']) ? $new_member['username'] : "";
            //     $first_name = isset($new_member['first_name']) ? $new_member['first_name'] : "";
            //     $last_name = isset($new_member['last_name']) ? $new_member['last_name'] : "";
            //     $errorModel = new ErrorModel;
            //     $errorModel->sendError (MASTER, $new_member);
            //     $find = stripos($username, $value);
            //
            //     if ($find) {
            //         // $IllegalLogModel = new IllegalLogModel();
            //         // $this->telegram->kickChatMember (
            //         //     $chat['id'],
            //         //     $new_member['id']
            //         // );
            //         // $IllegalLogModel->add ($chat_bot_id, $message_id, "用户名是否含有违禁词", $new_member['id'], $username, $first_name, $last_name);
            //     }
            //     exit;
            // }
            // $command = "new_member";
            // //查询数据清除返回文案
            // $commandModel = new CommandModel;
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
    //    }
        public function left_member ($left_member, $message_id, $from, $chat, $date) {
            // $str = '喵喵喵？ @' . $left_member['username'] . ' 被 @' . @$from['username'] . ' 移出了 ' . $chat['title'];
            // $this->telegram->sendMessage ($chat['id'], $str, $message_id, array (), '');
            // $chatBotModel = new ChatBotModel;
            // $chatBot = $chatBotModel->getcommand($chat['id']);
            // //$chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";


            $chat_bot_id = $_GET['bot_id'] ? intval($_GET['bot_id']) : 0;

            // $chatBotModel = new ChatBotModel;
            // $chatBot = $chatBotModel->getById($chat_bot_id);
            // if ($chatBot && isset($chatBot['left_member_switch']) && intval($chatBot['left_member_switch']) == 1) {
            //     //删除退群消息
            //     $this->telegram->deleteMessage (
            //         $chat['id'],
            //         $message_id
            //     );
            // }
            $chatBotConfigModel = new ChatBotConfigModel;
            $chatBotConfig = $chatBotConfigModel->getGroupBotConfig($chat_bot_id, $chat['id']);
            if ($chatBotConfig && isset($chatBotConfig['is_clear_left_member']) && $chatBotConfig['is_clear_left_member']) {
                //删除退群消息
                $this->telegram->deleteMessage (
                    $chat['id'],
                    $message_id
                );
            }

            $codeModel = new CodeModel;
            $codeModel->updateByFromId($chat_bot_id, @$from['id']);

            $groupUserModel = new GroupUserModel;
            $groupUserModel->updateByFromId($chat_bot_id, $chat['id'], @$from['id']);

        }

        function get_tags_arr($title)
        {
                require(APP_PATH.'/pscws4.class.php');
                $pscws = new PSCWS4();
                $pscws->set_dict(APP_PATH.'/scws/dict.utf8.xdb');
                $pscws->set_rule(APP_PATH.'/scws/rules.utf8.ini');
                $pscws->set_ignore(true);
                $pscws->send_text($title);
                $words = $pscws->get_tops(100);
                $tags = array();
                foreach ($words as $val) {
                    $tags[] = $val['word'];
                }
                $pscws->close();
                return $tags;
        }

        function get_keywords_str($content){
            require(APP_PATH.'/phpanalysis.class.php');
            PhpAnalysis::$loadInit = false;
            $pa = new PhpAnalysis('utf-8', 'utf-8', false);
            $pa->LoadDict();
            $pa->SetSource($content);
            $pa->StartAnalysis( false );
            $tags = $pa->GetFinallyResult();
            return $tags;
        }

        public function callback_query ($callback_data, $callback_id, $callback_from, $message_id, $from, $chat, $date) {
            $callbackExplode = explode ('_', $callback_data);
            if ($callbackExplode[0] == 'bing' && isset ($callbackExplode[1]) && isset ($callbackExplode[2])) {
                $i = $callbackExplode[1];

                //获取新闻数据
                //查询命令是否有回复
                $commandModel = new CommandModel;
                $commandInfo = $commandModel->findById($callbackExplode[2]);

                if ($commandInfo && $commandInfo['type'] == 5) {
                    $content = json_decode($commandInfo['content'], true);
                    if ($content) {
                        $imgurl = "http://file.name-technology.fun/" . $content[$i]['url'];
                        $copyright = $content[$i]['note'];
                        if ($i == 0) {
                            $button = json_encode (array (
                                'inline_keyboard' => array (
                                    array (array (
                                        'text' => '下一张',
                                        'callback_data' => 'bing_1_'.$callbackExplode[2]
                                    ))
                                )
                            ));
                        }if ($i == (count($content)-1)) {
                            $button = json_encode (array (
                                'inline_keyboard' => array (
                                    array (array (
                                        'text' => '上一张',
                                        'callback_data' => 'bing_' . ($i - 1) . '_'.$callbackExplode[2]
                                    ))
                                )
                            ));
                        } else {
                            $button = json_encode (array (
                                'inline_keyboard' => array (
                                    array (array (
                                        'text' => '上一张',
                                        'callback_data' => 'bing_' . ($i - 1) . '_'.$callbackExplode[2]
                                    )),
                                    array (array (
                                        'text' => '下一张',
                                        'callback_data' => 'bing_' . ($i + 1) . '_'.$callbackExplode[2]
                                    ))
                                )
                            ));
                        }
                        $this->telegram->sendPhoto ($chat['id'], $imgurl, $copyright, $message_id, $button);
                    }

                }

            }
        }


        function clearChatMessage($chat_bot_id, $message, $message_id, $from, $chat, $chatBotConfig)
        {
            $sendmessage = isset($chatBotConfig['del_msg_warn_content']['content']) ? ($chatBotConfig['del_msg_warn_content']['content']) : "Sorry you have no authority to  publish the contents above.To obtain the authority, please contact the administrator.";
            $command = "/datacleaning";
            //查询数据清除返回文案
            $commandModel = new CommandModel;
            $commandInfo = $commandModel->find($chat_bot_id, $command, 1, 1);
            $sendmessage = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : $sendmessage;



            //优化代码
            $IllegalLogModel = new IllegalLogModel;
            //查询违规数据是否大于3次 T出用户
            $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
            if ($count >= 3) {
                $this->telegram->kickChatMember (
                    $chat['id'],
                    $from['id']
                );
                $codeModel = new CodeModel;
                $codeModel->updateByFromId($chat_bot_id, $from['id']);
            }

            //封禁成员前先进行警告 - 发送警告消息 删除消息 两次警告无效进行封禁
            if ($chatBotConfig && isset($chatBotConfig['is_ban_warn']) && $chatBotConfig['is_ban_warn']) {
                $this->telegram->sendMessage (
                    $chat['id'],
                    $sendmessage,
                    $message_id
                );
            }

            $this->telegram->deleteMessage (
                $chat['id'],
                $message_id
            );

            //进行用户封禁
            if ($chatBotConfig && isset($chatBotConfig['set_ban_time']) && $chatBotConfig['set_ban_time']) {
                $this->telegram->restrictChatMember (
                    $chat['id'],
                    $from['id'],
                    time() + $chatBotConfig['set_ban_time'] * $count
                );

            }

            //封禁同时踢除成员 - T出成员
            if ($chatBotConfig && isset($chatBotConfig['is_ban_blank']) && $chatBotConfig['is_ban_blank']) {
                $this->telegram->kickChatMember (
                    $chat['id'],
                    $from['id']
                );
            }

            //封禁时长自动翻倍 暂时不做

            //记录相关违规数据
            $username = isset($from['username']) ? $from['username'] : "";
            $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name, $chat['id']);



        }
    }

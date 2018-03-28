<?php
    class Basic extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            # 步骤
            //1.查询是否以 / 开头 正则匹配
            //$search = "/^\//i"; preg_match($search,$command,$result)
            if($command[0] == "/") {

                $errorModel = new ErrorModel;
                $chatBotModel = new ChatBotModel;
                $chatBot = $chatBotModel->getcommand($chat['id']);
                $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

                //查询命令是否有回复
                $commandModel = new CommandModel;
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

                    if ($commandInfo[0]['type'] == 5) {
                        if (isset($commandInfo[0]['content']) && $commandInfo[0]['content']) {

                            $content = json_decode($commandInfo[0]['content'], true);
                            if ($content) {
                                $imgurl = $content[0]['url'];
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
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }

            if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
                $message = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";

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
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }
            if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
                $message = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";

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
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }

            if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {

                //链接  关键字（敏感词）过滤
                $regex = '@(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:\'".,<>?«»“”‘’]))@';
                $needle= 'http';
                $pos = strripos($message, $needle);
                if (preg_match($regex, $message) || $pos !== false) {
                    $sendmessage = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";

                    $IllegalLogModel = new IllegalLogModel;

                    //查询违规数据是否大于3次
                    $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
                    if ($count >= 3) {
                        $this->telegram->kickChatMember (
                            $chat['id'],
                            $from['id']
                        );
                        $username = isset($from['username']) ? $from['username'] : "";
                        $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                        $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                        $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name);

                        $codeModel = new CodeModel;
                        $codeModel->updateByFromId($chat_bot_id, $from['id']);

                         $this->telegram->sendMessage (
                            $chat['id'],
                            $sendmessage,
                            $message_id
                        );

                        $this->telegram->deleteMessage (
                            $chat['id'],
                            $message_id
                        );

                        return;
                    }

                    //记录相关违规数据
                    $username = isset($from['username']) ? $from['username'] : "";
                    $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                    $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                    $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name);

                    $this->telegram->sendMessage (
                        $chat['id'],
                        $sendmessage,
                        $message_id
                    );

                    $this->telegram->deleteMessage (
                        $chat['id'],
                        $message_id
                    );

                }else{
                    $result = $this->get_tags_arr($message);
                    $sensitiveWordsModel = new SensitiveWordsModel;

                    if ($result) {
                        // $errorModel = new ErrorModel;
                        // $errorModel->sendError (MASTER, print_r($result, true));
                        //
                        $word = $sensitiveWordsModel->find($result);

                        if ($word) {
                            $sendmessage = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";
                            $IllegalLogModel = new IllegalLogModel;

                            //查询违规数据是否大于3次
                            $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
                            if ($count >= 3) {
                                $this->telegram->kickChatMember (
                                    $chat['id'],
                                    $from['id']
                                );
                                $username = isset($from['username']) ? $from['username'] : "";
                                $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                                $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                                $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name);

                                $codeModel = new CodeModel;
                                $codeModel->updateByFromId($chat_bot_id, $from['id']);
                                return;
                            }

                            //记录相关违规数据
                            $username = isset($from['username']) ? $from['username'] : "";
                            $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                            $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                            $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name);

                            $this->telegram->sendMessage (
                                $chat['id'],
                                $sendmessage,
                                $message_id
                            );

                            $this->telegram->deleteMessage (
                                $chat['id'],
                                $message_id
                            );
                        }else{
                            $word = $sensitiveWordsModel->find([$message]);
                            if ($word) {
                                $sendmessage = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";
                                $IllegalLogModel = new IllegalLogModel;

                                //查询违规数据是否大于3次
                                $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
                                if ($count >= 3) {
                                    $this->telegram->kickChatMember (
                                        $chat['id'],
                                        $from['id']
                                    );
                                    $username = isset($from['username']) ? $from['username'] : "";
                                    $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                                    $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                                    $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name);

                                    $codeModel = new CodeModel;
                                    $codeModel->updateByFromId($chat_bot_id, $from['id']);
                                    return;
                                }

                                //记录相关违规数据
                                $username = isset($from['username']) ? $from['username'] : "";
                                $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                                $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                                $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name);

                                $this->telegram->sendMessage (
                                    $chat['id'],
                                    $sendmessage,
                                    $message_id
                                );

                                $this->telegram->deleteMessage (
                                    $chat['id'],
                                    $message_id
                                );
                            }
                        }

                    }else{
                        $word = $sensitiveWordsModel->find([$message]);
                        if ($word) {
                            $sendmessage = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";
                            $IllegalLogModel = new IllegalLogModel;

                            //查询违规数据是否大于3次
                            $count = $IllegalLogModel->getcount($chat_bot_id, $from['id']);
                            if ($count >= 3) {
                                $this->telegram->kickChatMember (
                                    $chat['id'],
                                    $from['id']
                                );
                                $username = isset($from['username']) ? $from['username'] : "";
                                $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                                $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                                $IllegalLogModel->add ($chat_bot_id, $message_id, "已被管理员T出群", $from['id'], $username, $first_name, $last_name);

                                $codeModel = new CodeModel;
                                $codeModel->updateByFromId($chat_bot_id, $from['id']);
                                return;
                            }

                            //记录相关违规数据
                            $username = isset($from['username']) ? $from['username'] : "";
                            $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                            $last_name = isset($from['last_name']) ? $from['last_name'] : "";
                            $IllegalLogModel->add ($chat_bot_id, $message_id, $message, $from['id'], $username, $first_name, $last_name);

                            $this->telegram->sendMessage (
                                $chat['id'],
                                $sendmessage,
                                $message_id
                            );

                            $this->telegram->deleteMessage (
                                $chat['id'],
                                $message_id
                            );
                        }
                    }
                }



            }
        }

        public function document ($document, $message_id, $from, $chat, $date) {
            $chatBotModel = new ChatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $whiteModel = new WhiteModel;

            $find = $whiteModel->my_find($chat_bot_id, $from['id']);

            if ($find) {
                return true;
            }

            if ($chatBot && isset($chatBot['is_shield']) && intval($chatBot['is_shield']) == 1) {
                $message = "Opps... error！Any ads posted in here are not allowed ，such as profiles，links，pictures etc... They will be automatically deleted. Please don't send these contents any more，or you will be taken out of the group.";

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
            // //创建欢迎消息
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
            $chatBotModel = new ChatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);
            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            $codeModel = new CodeModel;
            $codeModel->updateByFromId($chat_bot_id, @$from['id']);

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
                        $imgurl = $content[$i]['url'];
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
    }

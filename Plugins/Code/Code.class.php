<?php
    class Code extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {

            //查询chat code_cmd 默认 /code
            $chatBotModel = new ChatBotModel;
            $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;

            $chatBot = $chatBotModel->getById($chat_bot_id);
            $code_cmd = ($chatBot && isset($chatBot['code_cmd'])) ? str_replace("/", "", $chatBot['code_cmd']): "code";
            $search = "/^\/".$code_cmd."/i";

            if(preg_match($search,$command,$result)) {

                $code = str_replace($result[0], "", $command);
                //获取是否Code验证
                $codeModel = new CodeModel;
                $codeLogModel = new CodeLogModel;
                $groupActivityModel = new GroupActivityModel;

                //用户数据获取
                $username = isset($from['username']) ? $from['username'] : "";
                $first_name = isset($from['first_name']) ? $from['first_name'] : "";
                $last_name = isset($from['last_name']) ? $from['last_name'] : "";

                //查询该用户是否已激活
                $codeInfo = $codeModel->getByCode($code);

                //无Code信息
                if (!$codeInfo) {
                    return true;
                }

                //已激活 添加日志
                if ($codeInfo && (intval($codeInfo['from_id']) != 0 || intval($codeInfo['status']) == 3)) {
                    //查询是否配置已激活文案
                    $commandModel = new CommandModel;
                    $commandInfo = $commandModel->findall($chat_bot_id, '/codeactivate', 1, 1);
                    $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
                    if ($message) {
                        $this->telegram->sendMessage (
                            $chat['id'],
                            $message,
                            $message_id
                        );
                    }

                    $codeLogModel->add($chat_bot_id, $message_id, $code, "重复激活日志", @$from['id'], @$username, $first_name, $last_name);
                    return true;
                }else{
                    //查询该用户是否已激活
                    $findCode = $codeModel->getCodeByActivityId($codeInfo['activity_id'], $from['id']);
                    if ($findCode) {
                        //查询是否配置已激活文案
                        $commandModel = new CommandModel;
                        $commandInfo = $commandModel->findall($chat_bot_id, '/repeatactivate', 1, 1);
                        $message = ($commandInfo && $commandInfo[0] && isset($commandInfo[0]['content']) && !empty($commandInfo[0]['content'])) ? $commandInfo[0]['content'] : "";
                        if ($message) {
                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                        }
                        return;
                    }
                }



                //查询活动数据
                if ($codeInfo && intval($codeInfo['activity_id'])){
                    //查询活动是否结束
                    $groupActivityFind = $groupActivityModel->getGroupActivityById($codeInfo['activity_id']);

                    if ($groupActivityFind && intval($groupActivityFind['activate_type']) == 0) {

                        if ($chat['type'] == 'supergroup') {
                            $button = json_encode (array (
                                'inline_keyboard' => array (
                                    array (array (

                                        'text' => "Click here to send code to TokenMan",
                                        'url' => 'http://t.me/'.$chatBot['tokenman_name']
                                    ))
                                )
                            ));

                            $message = "Invalid！";

                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id,
                                $button
                            );
                            return;
                        }
                    }

                    if ($groupActivityFind && intval($groupActivityFind['activate_type']) == 1) {
                        if ($chat['type'] == 'private') {

                            $message = "Invalid！Please Send Code To Group";

                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                            return;
                        }
                    }

                    //判断活动时间
                    $activity_status =  -1;
                    if ($groupActivityFind && $groupActivityFind && intval($groupActivityFind['started_at']) <= time() && intval($groupActivityFind['stoped_at']) >= time()) {
                        $activity_status =  0;
                    }

                    if ($groupActivityFind && $groupActivityFind && $activity_status == -1) {

                        $commandModel = new CommandModel;
                        $commandInfo = $commandModel->findall($chat_bot_id, "/activity_end_text", 1, 1);

                        if ($commandInfo && $commandInfo[0] && $commandInfo[0]['type']) {
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

                            //type =  1 文字回复  2 code 码回复 3 图片文字回复 4 文件回复
                            if ($commandInfo[0]['type'] == 3) {
                                $copyright = $commandInfo[0]['content'] ? $commandInfo[0]['content'] : "";
                                $url = $commandInfo[0]['url'] ? "http://file.name-technology.fun/" . $commandInfo[0]['url'] : "";
                                if ($url) {
                                    $this->telegram->sendPhoto ($chat['id'], $url, $copyright, $message_id);
                                }
                            }
                        }else{
                            $message = $groupActivityFind['activity_end_text'];
                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                        }
                        return;
                    }


                    //查询code 回复数据
                    $commandModel = new CommandModel;
                    $commandFind = $commandModel->find($chat_bot_id, "/".$code_cmd, 2);

                    $message = "";

                    if ($commandFind && $commandFind[0] && $commandFind[0]['content']) {
                        $message = str_replace("{{".$code_cmd."}}", $code, $commandFind[0]['content']);
                        // $errorModel = new ErrorModel;
                        // $errorModel->sendError (MASTER, 'commandMessage'. $message);
                        $codeModel->updateByCode ($chat_bot_id, $code, 1, @$from['id'], @$username, $first_name, $last_name);

                        # 入群奖励 + 邀请奖励 $groupActivityFind['rate'] $groupActivityFind['group_rate']
                        $subtract = $groupActivityFind['rate'] + $groupActivityFind['group_rate'];
                        #修改消耗的奖励数
                        $groupActivityModel = new GroupActivityModel();
                        #如果消耗数小于0停止活动 活动已结束
                        $groupActivityModel->updateTotalRateById($groupActivityFind['id'], $subtract);
                        # 后期增加消息提醒 如 邀请人已激活 用 TokenMan 发送已激活消息
                        if ($groupActivityFind['total_rate'] <= 0) {
                            $groupActivityModel->updateActivityStopedatById($groupActivityFind['id']);
                        }

                        # 记录用户code 码相关回复数据 方便最后发放奖励
                        # 记录用户数据 $chat['id'] $message_id code 发送时间 message  $from['id'] $from['username']
                        $codeLogModel->add($chat_bot_id, $message_id, $code, @$message, @$from['id'], @$username, $first_name, $last_name);

                        $this->telegram->sendMessage (
                            $chat['id'],
                            $message,
                            $message_id
                        );
                    }

                }
            }
            //流程
            //查询该用户code信息 code uid status 已存在返回
            //无Code数据返回

            //有根据Code码获取活动信息 activity_id
            //记录用户激活操作记录

            //查询活动信息
            //查询活动是否结束 结束返回活动结束
            //继续激活操作 获取Code 码回复数据



            //查询chat code_cmd 默认 /code
            // $chatBotModel = new ChatBotModel;
            // $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;

            // $chatBot = $chatBotModel->getById($chat_bot_id);
            // //$chatBot = $chatBotModel->getcommand($chat['id']);
            // // if ($chatBot) {
            // //     $_SESSION['token'] = $chatBot['token'];
            // // }
            // $code_cmd = ($chatBot && isset($chatBot['code_cmd'])) ? str_replace("/", "", $chatBot['code_cmd']): "code";
            // $search = "/^\/".$code_cmd."/i";

            // //
            // // $errorModel = new ErrorModel;
            // // $errorModel->sendError (MASTER, 'chat_bot_id'.$chat_bot_id."--res".$code_cmd);
            // // exit;

            // if(preg_match($search,$command,$result)) {
            //     $code = str_replace($result[0], "", $command);
            //     //查询活动是否结束
            //     $groupActivityModel = new GroupActivityModel;
            //     $groupActivityFind = $groupActivityModel->getGroupActivityByChatId($chat_bot_id);
            //     // $errorModel = new ErrorModel;
            //     // $errorModel->sendError (MASTER, $groupActivityFind[0]['started_at']."groupActivityFind".print_r($groupActivityFind[0], true));


            //     //判断活动时间
            //     $activity_status =  -1;
            //     if ($groupActivityFind && $groupActivityFind[0] && intval($groupActivityFind[0]['started_at']) <= time() && intval($groupActivityFind[0]['stoped_at']) >= time()) {
            //         $activity_status =  0;
            //     }

            //     if ($groupActivityFind && $groupActivityFind[0] && $activity_status == -1) {

            //         $message = $groupActivityFind[0]['activity_end_text'];

            //         $this->telegram->sendMessage (
            //             $chat['id'],
            //             $message,
            //             $message_id
            //         );
            //         return;
            //     }


            //     //查询code 回复数据
            //     $commandModel = new CommandModel;
            //     $commandFind = $commandModel->find($chat_bot_id, "/".$code_cmd, 2);

            //     $message = "";

            //     $codeModel = new CodeModel;
            //     //查询该用户是否已激活
            //     $codeInfoByUid = $codeModel->getCodeByFromId($chat_bot_id, $from['id']);
            //     if ($codeInfoByUid) {
            //         $codeLogModel = new CodeLogModel;
            //         $username = isset($from['username']) ? $from['username'] : "";
            //         $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //         $last_name = isset($from['last_name']) ? $from['last_name'] : "";

            //         $codeLogModel->add($chat_bot_id, $message_id, $code, @$message, @$from['id'], @$username, $first_name, $last_name);
            //         return true;
            //     }

            //     $codeInfo = $codeModel->find($chat_bot_id, $code, 1);

            //     //用户码 进行用户绑定 from_id 对应
            //     //用户码记录表 chat_id-群ID  from_id-来源ID  eth-用户钱包 code-我的 parent_code-父(传播者 邀请者)
            //     //status-用户状态 默认 0 1-已申请用户码 2-已在群里确认用户码(已在群里) -1-已退出群聊
            //     //code 码规则 用户钱包地址_群ID_telegram_code_2018
            //     //查询 code AND chat_id AND status=1

            //     // $errorModel = new ErrorModel;
            //     // $errorModel->sendError (MASTER, 'from'. print_r($from, true));
            //     //查询该用户是否已激活
            //     if ($codeInfo && $codeInfo[0]) {
            //         if ($commandFind && $commandFind[0] && $commandFind[0]['content']) {
            //             $message = str_replace("{{".$code_cmd."}}", $code, $commandFind[0]['content']);
            //             // $errorModel = new ErrorModel;
            //             // $errorModel->sendError (MASTER, 'commandMessage'. $message);
            //             $codeModel = new CodeModel;

            //             $username = isset($from['username']) ? $from['username'] : "";
            //             $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //             $last_name = isset($from['last_name']) ? $from['last_name'] : "";
            //             $codeModel-> updateByCode ($chat_bot_id, $code, 1, @$from['id'], @$username, $first_name, $last_name);



            //             # 记录用户code 码相关回复数据 方便最后发放奖励
            //             # 记录用户数据 $chat['id'] $message_id code 发送时间 message  $from['id'] $from['username']
            //             $codeLogModel = new CodeLogModel;
            //             $username = isset($from['username']) ? $from['username'] : "";
            //             $first_name = isset($from['first_name']) ? $from['first_name'] : "";
            //             $last_name = isset($from['last_name']) ? $from['last_name'] : "";

            //             $codeLogModel->add($chat_bot_id, $message_id, $code, @$message, @$from['id'], @$username, $first_name, $last_name);


            //             $this->telegram->sendMessage (
            //                 $chat['id'],
            //                 $message,
            //                 $message_id
            //             );
            //         }

            //     }
            // }
        }
    }

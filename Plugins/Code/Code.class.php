<?php
    class Code extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            //查询chat code_cmd 默认 /code
            $chatBotModel = new chatBotModel;
            $chatBot = $chatBotModel->getcommand($chat['id']);

            $code_cmd = ($chatBot && isset($chatBot['code_cmd'])) ? str_replace("/", "", $chatBot['code_cmd']): "code";
            $search = "/^\/".$code_cmd."/i";

            $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

            //
            // $errorModel = new ErrorModel;
            // $errorModel->sendError (MASTER, 'chat_bot_id'.$chat_bot_id."--res".$code_cmd);
            // exit;

            if(preg_match($search,$command,$result)) {
                $code = str_replace($result[0], "", $command);
                //查询活动是否结束
                $groupActivityModel = new groupActivityModel;
                $groupActivityFind = $groupActivityModel->getGroupActivityByChatId($chat_bot_id);

                //判断活动时间
                $activity_status =  -1;
                if ($groupActivityFind && $groupActivityFind['started_at'] <= time() && $groupActivityFind['stoped_at'] >= time()) {
                    $activity_status =  0;
                }

                if ($groupActivityFind && $activity_status == -1) {
                    $message = $groupActivityFind['activity_end_text'];
                    $this->telegram->sendMessage (
                        $chat['id'],
                        $message,
                        $message_id
                    );
                    return;
                }

                //查询code 回复数据
                $commandModel = new commandModel;
                $commandFind = $commandModel->find($chat_bot_id, "/".$code_cmd, 2);

                $message = "";

                $codeModel = new codeModel;
                $codeInfo = $codeModel->find($chat_bot_id, $code, 1);

                //用户码 进行用户绑定 from_id 对应

                //用户码记录表 chat_id-群ID  from_id-来源ID  eth-用户钱包 code-我的 parent_code-父(传播者 邀请者)
                //status-用户状态 默认 0 1-已申请用户码 2-已在群里确认用户码(已在群里) -1-已退出群聊
                //code 码规则 用户钱包地址_群ID_telegram_code_2018
                //查询 code AND chat_id AND status=1
                if ($codeInfo && $codeInfo[0]) {
                    if ($commandFind && $commandFind[0] && $commandFind[0]['content']) {
                        $message = str_replace("{{".$code_cmd."}}", $code, $commandFind[0]['content']);
                        // $errorModel = new ErrorModel;
                        // $errorModel->sendError (MASTER, 'commandMessage'. $message);
                        $this->telegram->sendMessage (
                            $chat['id'],
                            $message,
                            $message_id
                        );
                    }
                    $codeModel = new codeModel;
                    $codeModel-> updateByCode ($chat_bot_id, $code, 1, @$from['id'], @$from['username']);
                }

                # 记录用户code 码相关回复数据 方便最后发放奖励
                # 记录用户数据 $chat['id'] $message_id code 发送时间 message  $from['id'] $from['username']
                $codeLogModel = new codeLogModel;
                $codeLogModel->add($chat_bot_id, $message_id, $code, @$message, @$from['id'], @$from['username']);

            }
        }
    }

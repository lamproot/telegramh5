<?php
    class Me extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/me' || $command == '/mycode' ) {

                $errorModel = new ErrorModel;
                //查询chat code_cmd 默认 /code
                $chatBotModel = new ChatBotModel;
                $chatBot = $chatBotModel->getcommand($chat['id']);

                $code_cmd = ($chatBot && isset($chatBot['code_cmd'])) ? str_replace("/", "", $chatBot['code_cmd']): "code";
                $search = "/^\/".$code_cmd."/i";

                $chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

                $codeModel = new CodeModel;
                $codeInfo = $codeModel->getCodeByFromId($chat_bot_id, $from['id']);
                $mycommand = "";

                if ($codeInfo && $codeInfo[0] && isset($codeInfo[0]['code'])) {
                    $mycommand = "/code".$codeInfo[0]['code'];
                    $errorModel = new ErrorModel;
                    $errorModel->sendError (MASTER, $mycommand);
                }else{
                    return;
                }

                if(preg_match($search,$mycommand,$result)) {
                    $code = str_replace($result[0], "", $mycommand);
                    //查询活动是否结束
                    $groupActivityModel = new GroupActivityModel;
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
                    $commandModel = new CommandModel;
                    $commandFind = $commandModel->find($chat_bot_id, "/".$code_cmd, 2);

                    $message = "";
                    if ($codeInfo && $codeInfo[0]) {
                        if ($commandFind && $commandFind[0] && $commandFind[0]['content']) {
                            $message = str_replace("{{".$code_cmd."}}", $code, $commandFind[0]['content']);
                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                        }
                    }
                }

            }

        }
    }

<?php
    class Me extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/me' || $command == '/mycode' ) {

                $errorModel = new ErrorModel;
                //查询chat code_cmd 默认 /code
                $chatBotModel = new ChatBotModel;
                $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;

                $chatBot = $chatBotModel->getById($chat_bot_id);
                // if ($chatBot) {
                //     $_SESSION['token'] = $chatBot['token'];
                // }

                $code_cmd = ($chatBot && isset($chatBot['code_cmd'])) ? str_replace("/", "", $chatBot['code_cmd']): "code";
                $search = "/^\/".$code_cmd."/i";

                //$chat_bot_id = ($chatBot && isset($chatBot['id'])) ? $chatBot['id'] : "";

                $codeModel = new CodeModel;
                $codeInfo = $codeModel->getCodeByFromId($chat_bot_id, $from['id']);
                $mycommand = "";

                if ($codeInfo && $codeInfo[0] && isset($codeInfo[0]['code'])) {
                    $mycommand = "/code".$codeInfo[0]['code'];

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
                    if ($groupActivityFind[0] && intval($groupActivityFind[0]['started_at']) <= time() && intval($groupActivityFind[0]['stoped_at']) >= time()) {
                        $activity_status =  0;
                    }

                    if ($groupActivityFind[0] && $activity_status == -1) {

                        $message = $groupActivityFind[0]['activity_end_text'];

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
                    $button = json_encode (array (
                        'inline_keyboard' => array (
                            array (array (

                                'text' => 'TokenMan AI Bot',
                                'url' => 'https://twitter.com/IamTokenMan'
                            ))
                        )
                    ));

                    if ($codeInfo && $codeInfo[0]) {
                        if ($commandFind && $commandFind[0] && $commandFind[0]['content']) {
                            $message = str_replace("{{".$code_cmd."}}", $code, $commandFind[0]['content']);
                            //$errorModel = new ErrorModel;
                            $errorModel->sendError (MASTER, $chat['id']);

                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id,
                                $button
                            );
                        }
                    }
                }

            }

        }
    }

<?php
    class Ping extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            // if ($command == '/ping') {
            //     $t1 = microtime (true);
            //     $pong_id = $this->telegram->sendMessage ($chat['id'], 'Pong!-- $message_id'.$message_id , $message_id);
            //     $t2 = microtime (true);
            //
            //     $time = round (($t2 - $t1) * 1000, 2);
            //     $this->telegram->editMessage ($chat['id'], $pong_id, 'Pong! -- chatid'.$chat['id'] . "\n" . 'Time:<code>' . $time . ' ns</code>');
            // }

            $search = "/^\/ZoobiDoobi/i";

            if (preg_match($search,$command,$result)) {
                //获取用户信息 和机器ID 信息
                //存取用户数据 from_uid from_username chat_id
                $chat_bot_id = intval(base64_decode(str_replace($result[0], "", $command)));



                $chatBotModel = new ChatBotModel;
                $chatBot = $chatBotModel->getById($chat_bot_id);
                $token = ($chatBot && isset($chatBot['token'])) ? $chatBot['token'] : "";

                if ($token) {

                    $_SESSION['token'] = $token;
                }

                if ($chat_bot_id != 0) {
                    $whiteModel = new WhiteModel;

                    $find = $whiteModel->my_find($chat_bot_id, $from['id']);
                    $message = "恭喜您获得吟唱魔法~~~";

                    if ($find) {
                        $this->telegram->sendMessage (
                            $chat['id'],
                            $message,
                            $message_id
                        );
                    }else{
                        $from['username'] = isset($from['username']) ? $from['username'] : "";

                        $insert = $whiteModel->add($chat_bot_id, $from['id'], @$from['username']);

                        if ($insert) {
                            $this->telegram->sendMessage (
                                $chat['id'],
                                $message,
                                $message_id
                            );
                        }

                    }
                }



            }


            if ($command == '/女朋友') {
                //$webdata = json_decode ($this->fetch ('http://cn.bing.com/HPImageArchive.aspx?format=js&n=1&idx=0'), true);
                //$imgurl = 'http://cn.bing.com' . $webdata['images'][0]['url'];
                //$imgurl = 'https://img.appledaily.com.tw/images/ReNews/20180102/640_244456183d3cfe96287b2cf6c0c2da41.jpg';
                //$copyright = $webdata['images'][0]['copyright'];
                $copyright = "给你推荐的女朋友是:苍老师";
                // $button = json_encode (array (
                //     'inline_keyboard' => array (
                //         array (array (
                //             'text' => '下一张',
                //             'callback_data' => 'bing 1'
                //         ))
                //     )
                // ));
                //$this->telegram->sendPhoto ($chat['id'], $imgurl, $copyright, $message_id);
                //$document = "Here you can find our official animated LoMoStar video:https://www.youtube.com/watch?v=k3SBw97F-ng";
                // $document = "Here you can see a https://www.youtube.com/watch?v=I51S0BRmMOg nd our official animated LoMoStar vide https://www.youtube.com/watch?v=k3SBw97F-ng";
                // $this->telegram->sendMessage ($chat['id'], $document, $message_id);
                //$document = "Here you can see a https://www.youtube.com/watch?v=I51S0BRmMOg nd our official animated LoMoStar vide https://www.youtube.com/watch?v=k3SBw97F-ng";
                $this->telegram->sendDocument ($chat['id'], "http://048dc25f.ngrok.io/Uploads/file/command_file/5a968f2b28fcd.pdf", $copyright, $message_id);

            }
        }
    }

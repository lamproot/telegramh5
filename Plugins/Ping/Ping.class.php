<?php
    class Ping extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/ping') {
                $t1 = microtime (true);
                $pong_id = $this->telegram->sendMessage ($chat['id'], 'Pong!-- $message_id'.$message_id , $message_id);
                $t2 = microtime (true);

                $time = round (($t2 - $t1) * 1000, 2);
                $this->telegram->editMessage ($chat['id'], $pong_id, 'Pong! -- chatid'.$chat['id'] . "\n" . 'Time:<code>' . $time . ' ns</code>');
            }

            if ($command == '/女朋友') {
                //$webdata = json_decode ($this->fetch ('http://cn.bing.com/HPImageArchive.aspx?format=js&n=1&idx=0'), true);
                //$imgurl = 'http://cn.bing.com' . $webdata['images'][0]['url'];
                $imgurl = 'https://img.appledaily.com.tw/images/ReNews/20180102/640_244456183d3cfe96287b2cf6c0c2da41.jpg';
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
                $this->telegram->sendPhoto ($chat['id'], $imgurl, $copyright, $message_id);
            }
        }
    }

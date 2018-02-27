<?php
    class Me extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            if ($command == '/me' || $command == '/my' ) {
                $code = "xxxxxxxxXXXXXXX";
                $message = "Your code: {$code}. SUCCESS & DONE! For every friend you invite from now on, you will get 188 SSS from our Airdrop event within a month
你的驗證碼：{$code}，校驗成功！每邀请一个好友，你将在 1 個月内收到 188 SSS 的空投獎勵。
Your share link （你的分享鏈接）：http://www.sss.run/?code={$code}";
                $this->telegram->sendMessage (
                    $chat['id'],
                    $message,
                    $message_id
                );
            }
        }
    }

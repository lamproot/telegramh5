<?php
    class Currency extends Base {
        public function command ($command, $param, $message_id, $from, $chat, $date) {
            // if ($command == '/start' || $command == '/help' ) {
            //     $message = "欢迎阅读使用手册 命令如下:\n/ping 连接\n/hitokoto 文字\n/bing 图片\n/whoami 我是谁\n/code Code码处理\n/me 我的-Code码";
            //     $this->telegram->sendMessage (
            //         $chat['id'],
            //         $message,
            //         $message_id
            //     );
            // }

            $search = "/^\//i";

            if ($command != "/start") {
                $chat_bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;
                // $errorModel = new ErrorModel;
                // $errorModel->sendError (MASTER,$search);exit;
                if(preg_match($search,$command,$result)) {

                    $currency = strtoupper(str_replace($result[0], "", $command));

                    $convert_array = array("AUD", "BRL", "CAD", "CHF", "CLP", "CNY", "CZK", "DKK", "EUR", "GBP", "HKD", "HUF", "IDR", "ILS", "INR", "JPY", "KRW", "MXN", "MYR", "NOK", "NZD", "PHP", "PKR", "PLN", "RUB", "SEK", "SGD", "THB", "TRY", "TWD", "ZAR" );
                    $convert = "USD";

                    if (strstr($currency, "_")) {
                        $commandList = explode("_", $currency);
                        if ($commandList && isset($commandList[0])) {
                            $currencyName = $commandList[0];
                        }

                        if ($commandList && isset($commandList[1])) {
                            $convert = $commandList[1];
                        }

                    }else{
                        $currencyName = $currency;
                    }


                    $errorModel = new ErrorModel;
                    
                   //获取数据
                    $BotCurrencyModel = new BotCurrencyModel;
                    $BotCurrency = $BotCurrencyModel->getByChatBotId($chat_bot_id, strtolower($currencyName));

                    if (!$BotCurrency) {
                        $msg = "Invalid！";
                        $this->telegram->sendMessage (
                            $chat['id'],
                            $msg,
                            $message_id
                        );
                        exit;
                    }
                    

                    if ($BotCurrency) {
                        //对接第三方API - Lbank
                        if ($BotCurrency['exchange'] == 2) {
                            $content = file_get_contents($BotCurrency['api_url']);
                            if ($content && $BotCurrency['api_content']) {
                                $content_arr = json_decode($content, true);
                                $data = $content_arr['ticker'];

                                $BotCurrency['api_content'] = str_replace('{currency}', $BotCurrency['currency'],$BotCurrency['api_content']);
                                foreach ($data as $key => $value) {
                                    $BotCurrency['api_content'] = str_replace('{'.$key.'}', number_format($value,8), $BotCurrency['api_content']);
                                }
                                $msg = $BotCurrency['api_content'];
                                $this->telegram->sendMessage (
                                    $chat['id'],
                                    $msg,
                                    $message_id
                                );
                            }

                        }else{
                            //查询币的数据
                            $array = json_decode (file_get_contents (__DIR__ . '/currency.json'), true);
                            $result = array_filter($array['data'], function($t) use ($currencyName) { return $t['symbol'] == $currencyName; });
                            // symbol
                            // 名称：Tron name
                            // 排名：10 rank
                            // 市值：¥292亿 market_cap  USD=292亿
                            // 综合价格：¥0.4446 price USD=0.4446
                            // 成交量：¥237,831万 USD=volume_24h
                            // 流通量：6,574,811万 circulating_supply
                            // 涨幅(24H)：1.06%  percent_change_24h
                            // 涨幅(7D)：-12.56% percent_change_7d
                            // percent_change_1h


                            if ($result) {
                                
                                $chatBotModel = new chatBotModel;
                                $chatBot = $chatBotModel->getById($chat_bot_id);

                                if ($chatBot && isset($chatBot['is_currency']) && intval($chatBot['is_currency']) != 1) {
                                    return;
                                }

                                $result = array_values($result);
                                if ($result && isset($result[0]) && isset($result[0]['symbol']) && isset($result[0]['id'])) {
                                    $id = $result[0]['id'];
                                    $api = "https://api.coinmarketcap.com/v2/ticker/$id/?convert=".$convert;

                                    $apiresult = file_get_contents($api);
                                    $apiresultJson = $apiresult ? json_decode($apiresult, true) : [];


                                    if ($apiresultJson && isset($apiresultJson['data']) && isset($apiresultJson['data']['quotes']) && isset($apiresultJson['data']['quotes'][$convert])) {
                                        // if (condition) {
                                        //     // code...
                                        // }
                                        $name = isset($apiresultJson['data']['name']) ? $apiresultJson['data']['name'] : "";
                                        $rank = isset($apiresultJson['data']['rank']) ? $apiresultJson['data']['rank'] : "";



                                        $percent_change_24h = isset($apiresultJson['data']['quotes'][$convert]['percent_change_24h']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_24h'] . "%" : "";
                                        $percent_change_7d = isset($apiresultJson['data']['quotes'][$convert]['percent_change_7d']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_7d'] . "%" : "";
                                        $percent_change_1h = isset($apiresultJson['data']['quotes'][$convert]['percent_change_1h']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_1h'] . "%" : "";

                                        
                                        
                                        $button_text = "Click here to know more about the currency market";
                                        if ($convert == 'CNY') {
                                            $button_text = "私信TokenMan了解更多币种行情";
                                            $circulating_supply = isset($apiresultJson['data']['circulating_supply']) ? $this->bqwhitscn($apiresultJson['data']['circulating_supply']) : "";
                                            $market_cap = isset($apiresultJson['data']['quotes'][$convert]['market_cap']) ? $convert . " " . $this->bqwhitscn($apiresultJson['data']['quotes'][$convert]['market_cap'])  : "";
                                            $price = isset($apiresultJson['data']['quotes'][$convert]['price']) ?  $convert . " " . number_format($apiresultJson['data']['quotes'][$convert]['price'],4) : "";
                                            $volume_24h = isset($apiresultJson['data']['quotes'][$convert]['volume_24h']) ?  $convert . " " . $this->bqwhitscn($apiresultJson['data']['quotes'][$convert]['volume_24h']) : "";

                                            $msg = " 名称：{$name} \n排名：{$rank} \n市值：{$market_cap} \n综合价格：{$price} \n成交量：{$volume_24h} \n流通量：{$circulating_supply} \n涨幅(1H)：{$percent_change_1h} \n涨幅(24H)：{$percent_change_24h}  \n涨幅(7D)：{$percent_change_7d}"."";
                                        }else{

                                            $circulating_supply = isset($apiresultJson['data']['circulating_supply']) ? number_format($apiresultJson['data']['circulating_supply']) : "";
                                            $market_cap = isset($apiresultJson['data']['quotes'][$convert]['market_cap']) ? $convert . " " . number_format($apiresultJson['data']['quotes'][$convert]['market_cap'])  : "";
                                            $price = isset($apiresultJson['data']['quotes'][$convert]['price']) ?  $convert . " " . number_format($apiresultJson['data']['quotes'][$convert]['price'],4) : "";
                                            $volume_24h = isset($apiresultJson['data']['quotes'][$convert]['volume_24h']) ?  $convert . " " . number_format($apiresultJson['data']['quotes'][$convert]['volume_24h']) : "";

                                            $msg = "Name：{$name} \nRank：{$rank} \nMarket Cap：{$market_cap} \nPrice：{$price} \nVolume：{$volume_24h} \nCirculating Supply：{$circulating_supply} \nPercent Change(1H)：{$percent_change_1h} \nPercent Change(24H)：{$percent_change_24h}  \nPercent Change(7D)：{$percent_change_7d}"."";
                                        }

                                        // $errorModel = new ErrorModel;
                                        // $errorModel->sendError (MASTER, $msg);exit;

                                        $button = json_encode (array (
                                            'inline_keyboard' => array (
                                                array (array (

                                                    'text' => $button_text,
                                                    'url' => 'http://t.me/TokenManBot'
                                                ))
                                            )
                                        ));

                                        //获取是否支持其他币查询
                                        $BotCurrencyModel = new BotCurrencyModel;

                                        $bot_id = $_GET['bot_id'] ? $_GET['bot_id'] : 1;
                                        $currencyName = strtolower($currencyName);
                                        $BotCurrency = $BotCurrencyModel->getByChatBotId($bot_id, $currencyName);
                                            
                                        if (!$BotCurrency) {
                                            $msg = "Invalid！";
                                        }

                                        $message = $msg;
                                        $this->telegram->sendMessage (
                                            $chat['id'],
                                            $message,
                                            $message_id,
                                            $button
                                        );

                                        // $errorModel = new ErrorModel;
                                        // $errorModel->sendError (MASTER, $msg ."\n" ."私信TokenMan了解更多币种行情 \n Send a private message to TokenMan know more about the currency market.");
                                    }
                                }
                            }
                        }


                    }

                   


                    


                }
            }

            
            //获取
            // if ($command == '/marketlist') {
            //     //记录用户使用TokenMan数据
            //     if ($chat['type'] == 'private') {
            //         //$str = "激活失败 请在机器人所在的群组激活!";
            //     }
            // }
        }


        function bqwhitscn($hits) {
            $b=1000;
            $c=10000;
            $d=100000000;

            if ($hits<$b){
                return $hits;
            }else if ($hits>=$b && $hits<$c){
                return floor($hits/$b).'千';
            }else if ($hits>=$c && $hits<$d){
                return floor($hits/$c).'万';
            }else {
                return floor($hits/$d).'亿';
            }
        }
    }

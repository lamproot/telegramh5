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

                            $circulating_supply = isset($apiresultJson['data']['circulating_supply']) ? $this->bqwhitscn($apiresultJson['data']['circulating_supply']) : "";

                            $market_cap = isset($apiresultJson['data']['quotes'][$convert]['market_cap']) ? $convert . " " . $this->bqwhitscn($apiresultJson['data']['quotes'][$convert]['market_cap'])  : "";
                            $price = isset($apiresultJson['data']['quotes'][$convert]['price']) ?  $convert . " " . number_format($apiresultJson['data']['quotes'][$convert]['price'],4) : "";
                            $volume_24h = isset($apiresultJson['data']['quotes'][$convert]['volume_24h']) ?  $convert . " " . $this->bqwhitscn($apiresultJson['data']['quotes'][$convert]['volume_24h']) : "";

                            $percent_change_24h = isset($apiresultJson['data']['quotes'][$convert]['percent_change_24h']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_24h'] . "%" : "";
                            $percent_change_7d = isset($apiresultJson['data']['quotes'][$convert]['percent_change_7d']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_7d'] . "%" : "";
                            $percent_change_1h = isset($apiresultJson['data']['quotes'][$convert]['percent_change_1h']) ? $apiresultJson['data']['quotes'][$convert]['percent_change_1h'] . "%" : "";

                            if ($convert == 'CNY') {
                                $msg = "名称：{$name} \n 排名：{$rank} \n 市值：{$market_cap} \n 综合价格：{$price} \n 成交量：{$volume_24h} \n 流通量：{$circulating_supply} \n 涨幅(1H)：{$percent_change_1h} \n 涨幅(24H)：{$percent_change_24h}  \n 涨幅(7D)：{$percent_change_7d}";
                            }else{
                                $msg = "Name：{$name} \n Rank：{$rank} \n Market Cap：{$market_cap} \n Price：{$price} \n Volume：{$volume_24h} \n Circulating Supply：{$circulating_supply} \n Percent Change(1H)：{$percent_change_1h} \n Percent Change(24H)：{$percent_change_24h}  \n Percent Change(7D)：{$percent_change_7d}";
                            }

                            $errorModel = new ErrorModel;
                            $errorModel->sendError (MASTER, $msg);
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

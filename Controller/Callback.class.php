<?php
    class Callback extends FLController {

        function run () {

            // $blankip = ["149.154.167.206","117.139.208.26"];
            // $ip = $this->get_ip();
            // if (inarray($ip,$blankip)) {
            //     echo "";exit;
            // }

            /** 初始化 */
            $errorModel = new ErrorModel;
            $pluginModel = new PluginModel;
            $optionModel = new OptionModel;
            $telegramModel = new TelegramModel;

            $GLOBALS['statistics']['message_total'] = $optionModel->getvalue ('message_total');
            $GLOBALS['statistics']['send_total'] = $optionModel->getvalue ('send_total');
            $GLOBALS['statistics']['error_total'] = $optionModel->getvalue ('error_total');
		    /** 分析消息 */
		    $GLOBALS['statistics']['message_total']++;
		    $this->parseMessage ();

		    /** 操作 */
	        if (isset ($this->func) && $this->func == 'callback_query') {
	            $callbackData = explode (' ', $this->param[0]);
	            if ($callbackData[0] == 'Tbo_Operations') {
	                if ($callbackData[1] == 'fastLogin' && FASTLOGIN) { // 快速登录
	                    if ($callbackData[2] == 'allow') {
        	                $optionModel->update ('fastlogin_ip', $callbackData[3]);
        	                $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已允许授权');
        	            } else if ($callbackData[2] == 'noallow') {
        	                $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已拒绝授权');
        	            }
	                } else if ($callbackData[1] == 'logout') { // 远程登出
	                    session_id ($callbackData[2]);
	                    session_start ();

	                    $_SESSION['logined'] = false;
	                    $telegramModel->editMessage ($this->param[5]['id'], $this->param[3], '已登出');
	                }
	            }
	        }

		    /** 引入处理 */
		    if (isset ($this->func)) {
		        $pluginList = $pluginModel->getinfo (NULL, 1);
                // $errorModel->sendError (MASTER, print_r($pluginList, true));
                // $errorModel->sendError (MASTER, "func".print_r($this->func, true));

		        if (!empty ($pluginList)) {
    		        foreach ($pluginList as $pluginList_d) {
    		            $pluginName = $pluginList_d['pcn'];

    		            $GLOBALS['cuPlugin'] = $pluginName;

    	                require_once APP_PATH . '/Plugins/' . $pluginName . '/' . $pluginName . '.class.php';
                        $object[] = $objectNew = new $pluginName ($this->data);;
    	                if (method_exists ($objectNew, 'init'))
    	                    call_user_func_array (array ($objectNew, 'init'), $this->initParam);
    		        }

    		        foreach ($object as $object_d) {
                        //$errorModel->sendError (MASTER, "func".$this->func);
    		            if (method_exists ($object_d, $this->func)) {
    		                $GLOBALS['cuPlugin'] = get_class ($object_d);
    		                call_user_func_array (array ($object_d, $this->func), $this->param);
                            //$errorModel->sendError (MASTER, "object_d". print_r($object_d, true)."3333");
    		            }
    		        }

    		        if (isset ($cuPlugin))
    		            unset ($cuPlugin);
		        }
		        if ($this->func == 'inline_query') {
		            $telegramModel->sendInline ($this->param[2], 0);
		        }
		    }


		    /** 统计 */
		    $optionModel->update ('message_total', $GLOBALS['statistics']['message_total']);
	        $optionModel->update ('send_total', $GLOBALS['statistics']['send_total']);
	        $optionModel->update ('error_total', $GLOBALS['statistics']['error_total']);
        }

        private function parseMessage ()
        {
            $data = json_decode (file_get_contents ("php://input"), true);
            $errorModel = new ErrorModel;
            //$errorModel->sendError (MASTER, print_r($data, true));

// exit;
		    if (isset ($data['message'])) {
                //更新消息处理数
                // if ($data['message']['chat'] && isset($data['message']['chat']['id'])) {
                //     $newsTotalModel = new NewsTotalModel();
                //     $newsTotalModel->updateTotalByChatId($data['message']['chat']['id']);
                // }

		        if (isset ($data['message']['text'])) {
		            if ($data['message']['text'][0] == '/') {


		            	//查询该用户是否1分钟之内发了超过30次 将用户禁言


		                $data['message']['text'] = str_replace ("\n", " ", $data['message']['text']);
		                $messageExplode = explode (' ', $data['message']['text']);
		                $commandExplode = explode ('@', $messageExplode[0]);
		                // if (isset ($commandExplode[1]) && $commandExplode[1] != BOTNAME)
		                //     exit ();

		                $func = 'command';
		                $param = [
		                    $commandExplode[0],
		                    array (),
		                    $data['message']['message_id'],
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                $initParam = [
		                    $func,
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                if (isset ($messageExplode[1])) {
		                    $param[1] = array_slice ($messageExplode, 1);
		                }
		            } else {
		                $func = 'message';
		                $param = [
		                    $data['message']['text'],
		                    $data['message']['message_id'],
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		                $initParam = [
		                    $func,
		                    $data['message']['from'],
		                    $data['message']['chat'],
		                    $data['message']['date'],
		                ];
		            }
		        } else if (isset ($data['message']['new_chat_member'])) {
		            $func = 'new_member';
		            $param = [
		                $data['message']['new_chat_member'],
		                $data['message']['message_id'],
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		            $initParam = [
		                $func,
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		        } else if (isset ($data['message']['left_chat_member'])) {
		            $func = 'left_member';
		            $param = [
		                $data['message']['left_chat_member'],
		                $data['message']['message_id'],
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		            $initParam = [
		                $func,
		                $data['message']['from'],
		                $data['message']['chat'],
		                $data['message']['date'],
		            ];
		        } else if (isset ($data['message']['sticker'])) {
                    $func = 'sticker';
                    $param = [
                        $data['message']['sticker'],
                        $data['message']['message_id'],
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                    $initParam = [
                        $func,
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                } else if (isset ($data['message']['photo'])) {
                    $caption = '';
                    if (isset ($data['message']['caption'])) {
                        $caption = $data['message']['caption'];
                    }

                    $func = 'photo';
                    $param = [
                        $data['message']['photo'],
                        $caption,
                        $data['message']['message_id'],
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                    $initParam = [
                        $func,
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                } else if (isset ($data['message']['document'])) {
                    $func = 'document';
                    $param = [
                        $data['message']['document'],
                        $data['message']['message_id'],
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                    $initParam = [
                        $func,
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                } else if (isset ($data['message']['voice'])) {
                    $func = 'voice';
                    $param = [
                        $data['message']['voice'],
                        $data['message']['message_id'],
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                    $initParam = [
                        $func,
                        $data['message']['from'],
                        $data['message']['chat'],
                        $data['message']['date'],
                    ];
                }
		    } else if (isset ($data['callback_query'])) {
		        if (isset ($data['callback_query']['data'])) {
    		        $func = 'callback_query';
    		        $param = [
    		            $data['callback_query']['data'],
    		            $data['callback_query']['id'],
    		            $data['callback_query']['from'],
    		            $data['callback_query']['message']['message_id'],
    		            $data['callback_query']['message']['from'],
    		            $data['callback_query']['message']['chat'],
    		            $data['callback_query']['message']['date']
    		        ];
    		        $initParam = [
    		            $func,
    		            $data['callback_query']['from'],
    		            $data['callback_query']['message']['chat'],
    		            $data['callback_query']['message']['date']
    		        ];
		        } else if (isset ($data['callback_query']['game_short_name'])) {
		            $func = 'callback_game';
    		        $param = [
    		            $data['callback_query']['game_short_name'],
    		            $data['callback_query']['id'],
    		            $data['callback_query']['from']
    		        ];
    		        $initParam = [
    		            $func,
    		            $data['callback_query']['from'],
    		            $data['callback_query']['from'],
    		            time ()
    		        ];
		        }
		    } else if (isset ($data['inline_query'])) {
		        $func = 'inline_query';
		        $param = [
		            $data['inline_query']['query'],
		            $data['inline_query']['offset'],
		            $data['inline_query']['id'],
		            $data['inline_query']['from']
		        ];
		        $initParam = [
		            $func,
		            $data['inline_query']['from'],
		            $data['inline_query']['from'],
		            time ()
		        ];
		    }

		    $this->data = $data;
		    if (isset ($func)) {
                $this->func = $func;
                $this->param = $param;
                $this->initParam = $initParam;
		    }
        }

        /**
		 * 返回16位md5值
		 *
		 * @param string $str 字符串
		 * @return string $str 返回16位的字符串
		 */
		function short_md5($str) {
		    return substr(md5($str), 8, 16);
		}

		//不同环境下获取真实的IP
		function get_ip(){
			    //判断服务器是否允许$_SERVER
			    if(isset($_SERVER)){
			        if(isset($_SERVER[HTTP_X_FORWARDED_FOR])){
			            $realip = $_SERVER[HTTP_X_FORWARDED_FOR];
			        }elseif(isset($_SERVER[HTTP_CLIENT_IP])) {
			            $realip = $_SERVER[HTTP_CLIENT_IP];
			        }else{
			            $realip = $_SERVER[REMOTE_ADDR];
			        }
			    }else{
			        //不允许就使用getenv获取
			        if(getenv("HTTP_X_FORWARDED_FOR")){
			              $realip = getenv( "HTTP_X_FORWARDED_FOR");
			        }elseif(getenv("HTTP_CLIENT_IP")) {
			              $realip = getenv("HTTP_CLIENT_IP");
			        }else{
			              $realip = getenv("REMOTE_ADDR");
			        }
			    }

			    return $realip;
			}
    }

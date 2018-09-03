<?php if (!defined('THINK_PATH')) exit();//判断是否加载thinkphp,如果否则退出
/*********文件描述*********
 * @last update 2014-06-12
 * @alter
 * @version 1.0.0
 *
 * 功能简介：商户后台首页控制器类
 * @author
 * @copyright
 * @time 2014-06-12
 * @version 1.0.0
 */
 use afs\Request\V20180112 as Afs;
	class ApiAction extends CommonAction {

		/**
		 * 构造方法-实例化MODEL
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
		public function __construct()
		{
			parent::__construct();
			$this -> model = D('Api');
		}

	    /**
		 * 首页
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function code()
	    {

			//echo ($result);exit;
			// wallet
			// code
			// token
			// chat_bot_id
			//获取推广用户CODE是否存在 code and chat_bot_id
			//进行code加密获取 md5(wallet_(chat_bot_id)_telegram)
			//查询是否已生成codes  生成 跳转数据界面  未生成 生成数据 并跳转数据界面
			if (empty($_POST['wallet']) || !isset($_POST['wallet'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}
			// if (strlen($_POST['wallet']) != 42) {
			// 	echo json_encode(array("code" => 101, "success" => false));exit;
			// }

			if (empty($_POST['code']) || !isset($_POST['code'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			if (empty($_POST['chat_bot_id']) || !isset($_POST['chat_bot_id'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			if (empty($_POST['activity_id']) || !isset($_POST['activity_id'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			if ($_POST['googleauth'] != "false") {
				if (empty($_POST['googleauth']) || !isset($_POST['googleauth'])) {
					echo json_encode(array("code" => 101, "success" => false));exit;
				}

		    	$googledata['secret'] = "6LfwBVkUAAAAAFbYHUxRn-6Nn2TUdIdP_lvQ2nOX";
				$googledata['response'] = $_POST['googleauth'];
				$googledata['remoteip'] = $_SERVER['REMOTE_ADDR'];

				$result = $this->fetch("https://www.google.com/recaptcha/api/siteverify", $googledata);
				if ($result && $result['success'] == true) {
					# code...
				}else{
					echo json_encode(array("code" => 103, "success" => false));exit;
				}
			}
			// chat_bot_id eth code parent_code status ctrated_at
			$params = array(

				'table_name' => 'codes',

				'where' => "activity_id = {$_POST['activity_id']} AND eth = '{$_POST['wallet']}'"

			);

	    	$codes = $this -> model -> my_find($params);

			if (!$codes) {
				//生成Code
				$code = $data['code'] = $this->short_md5(md5($_POST['activity_id']."_".$_POST['wallet']."_telegram"));
				$data['eth'] = $_POST['wallet'];
				$data['chat_bot_id'] = $_POST['chat_bot_id'];
				$data['parent_code'] = $_POST['code'];
				$data['created_at'] = time();
				$data['status'] = 1;
				$data['activity_id'] = $_POST['activity_id'];

				$data['ip'] = $this->get_ip();
				$data['agent'] = $_SERVER['HTTP_USER_AGENT'];
				$code_params = array(

					'table_name' => 'codes',

					'data' => $data
				);

				$codeAdd = $this -> model -> my_add($code_params);
				if ($codeAdd) {
					echo json_encode(array("code" => 0, "success" => true, "usercode" => $code));exit;
				}else{
					echo json_encode(array("code" => 101, "success" => false));exit;
				}
			}else{
				echo json_encode(array("code" => 0, "success" => true, "usercode" => $codes['code']));exit;
			}
	    }

	    /**
		 * 拟稿人活动
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function activity()
	    {
			if (empty($_POST['telegram_uid']) || !isset($_POST['telegram_uid'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			$params = array(

				'table_name' => 'drafters',

				'where' => "telegram_uid = {$_POST['telegram_uid']} AND activity_id = '{$_POST['activity_id']}'"

			);

	    	$codes = $this -> model -> my_find($params);

			if (!$codes) {
				//$data['uid'] = $_POST['uid'];
				$data['created_at'] = time();
				$data['activity_id'] = $_POST['activity_id'];
				$data['eth'] = $_POST['wallet'];
				$data['chat_bot_id'] = intval($_POST['chat_bot_id']);
				//$data['content'] = json_encode($_POST['message_url']);
				$data['telegram_uid'] =  $_POST['telegram_uid'];
				$code = $data['code'] = $this->short_md5(md5($_POST['activity_id']."_".$_POST['wallet']."_".$_POST['telegram_uid']."_telegram"));

				$drafters_params = array(

					'table_name' => 'drafters',

					'data' => $data
				);

				$draftersAdd = $this -> model -> my_add($drafters_params);
				if ($draftersAdd) {
					echo json_encode(array("code" => 0, "success" => true, "usercode" => $code));exit;
				}else{
					echo json_encode(array("code" => 101, "success" => false, "message" => "添加失败"));exit;
				}
			}else{
				echo json_encode(array("code" => 0, "success" => true, "usercode" => $codes['code']));exit;
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
			        if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
			        }elseif(isset($_SERVER["HTTP_CLIENT_IP"])) {
			            $realip = $_SERVER["HTTP_CLIENT_IP"];
			        }else{
			            $realip = $_SERVER["REMOTE_ADDR"];
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

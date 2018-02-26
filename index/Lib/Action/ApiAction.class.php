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
			$this -> model = D('Index');
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

			// wallet
			// code
			// token
			// chat_id
			//获取推广用户CODE是否存在 code and chat_id
			//进行code加密获取 md5(wallet_(chat_id)_telegram)
			//查询是否已生成codes  生成 跳转数据界面  未生成 生成数据 并跳转数据界面
			if (empty($_POST['wallet']) || !isset($_POST['wallet'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			if (empty($_POST['code']) || !isset($_POST['code'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}

			if (empty($_POST['chat_id']) || !isset($_POST['chat_id'])) {
				echo json_encode(array("code" => 101, "success" => false));exit;
			}


			// chat_id eth code parent_code status ctrated_at
			$params = array(

				'table_name' => 'telegram.codes',

				'where' => "chat_id = {$_POST['chat_id']} AND eth = '{$_POST['wallet']}'"

			);

	    	$codes = $this -> model -> my_find($params);

			if (!$codes) {
				//生成Code
				$code = $data['code'] = $this->short_md5(md5($_POST['chat_id']."_".$_POST['wallet']."_telegram"));
				$data['eth'] = $_POST['wallet'];
				$data['chat_id'] = $_POST['chat_id'];
				$data['parent_code'] = $_POST['code'];
				$data['created_at'] = time();
				$data['status'] = 1;

				$params = array(

					'table_name' => 'telegram.codes',

					'data' => $data
				);

				$codeAdd = $this -> model -> my_add($params);
				if ($codeAdd) {
					echo json_encode(array("code" => 0, "success" => true, "code" => $code));exit;
				}else{
					echo json_encode(array("code" => 101, "success" => false));exit;
				}
			}else{
				echo json_encode(array("code" => 0, "success" => true, "code" => $codes['code']));exit;
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
	}

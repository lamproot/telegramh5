<?php if (!defined('THINK_PATH')) exit();//判断是否加载thinkphp,如果否则退出
/*********文件描述*********
 * @last
 * @alter
 * @version 1.0.0
 *
 * 功能简介:
 * @author
 * @copyright
 * @time
 * @version 1.0.0
 */
	class IndexAction extends CommonAction {

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
		 * code 邀请活动页面
		 *
		 */
	    public function code()
	    {
			//INSERT INTO `codes` VALUES  ('1', '-1001249040089', '520439802', '0xf6BC0AAc1fdFAf2CCea054F5978350DC9eFc6E83', 'bf3c1eac97f80c7e', '6666', '3', '0', '1518599007', '')
			//$this->short_md5(md5($_POST['chat_bot_id']."_".$_POST['wallet']."_telegram"));
			// for ($i=520439802; $i <= 520440802; $i++) {
			// 	$data['parent_code'] = $this->short_md5(md5("-1001249040089_"."0xf6BC0AAc1fdFAf2CCea054F5".md5(rand(520439802, 520440802))."_telegram"));
			// 	$data['uid'] = $i;
			// 	$data['wallet'] = "0xf6BC0AAc1fdFAf2CCea054F5".md5($i);
			// 	$data['code'] = $this->short_md5(md5("-1001249040089_".$data['wallet']."_telegram"));
			// 	//$result[] = $data;
            //
			// 	echo "INSERT INTO `codes` (`chat_bot_id`,`from_id`,`eth`,`code`,`parent_code`,`status`,`created_at`,`updated_at`,`from_username`)  VALUES  ('-1001249040089', '{$data['uid']}', '{$data['wallet']}', '{$data['code']}', '{$data['parent_code']}', '3', '0', '".time()."', '');";
			// 	echo "<br>";
			// }
			// exit;
			// echo json_encode($result);exit;
			// exit;
			$code = $_REQUEST['_URL_'][2] ?  $_REQUEST['_URL_'][2] : $code;

			//获取code信息
			$params = array(

				'table_name' => 'codes',

				'where' => "code = '{$code}'"

			);

	    	$codes = $this -> model -> my_find($params);
			// echo $code;exit;
			if (!$codes) {
				$this -> _back('code数据获取失败');
			}
			//获取机器人信息
			$params = array(

				'table_name' => 'chat_bot',

					'where' => "id = {$codes['chat_bot_id']}"

			);

	    	$chat_bot = $this -> model -> my_find($params);
			if (!$chat_bot) {
				$this -> _back('chat数据获取失败2');
			}

			//获取活动信息
			$params = array(

				'table_name' => 'group_activity',

				'where' => "chat_bot_id = {$codes['chat_bot_id']} AND type = 1 AND is_del = 0"

			);

	    	$group_activity = $this -> model -> my_find($params);
			if (!$group_activity) {
				$this -> _back('activity数据获取失败');
			}

			//获取活动主题
			$params = array(

				'table_name' => 'activity_theme',

				'where' => "id = {$group_activity['theme_id']}"

			);

	    	$activity_theme = $this -> model -> my_find($params);

	    	$group_activity['bglogo'] = "";
	    	if ($activity_theme) {
	    		$group_activity['bglogo'] = $activity_theme['bglogo'] ? $activity_theme['bglogo'] : __PUBLIC__."/img/code1.png";
	    	}

			$this -> assign('codes', $codes);
			$this -> assign('chat_bot', $chat_bot);
			$this -> assign('group_activity', $group_activity);
			$this -> assign('code', $code);

			if ($_GET['lang'] == 'en') {
				$this -> display('en_code');
			}else{
				$this -> display();
			}
	    }


		/**
		* 首页：
		*
		*/
	   public function dashboard()
	   {

		   $code = $_REQUEST['_URL_'][2] ?  $_REQUEST['_URL_'][2] : $code;
		   //获取code信息
		   $params = array(

			   'table_name' => 'codes',

			   'where' => "code = '{$code}'"

		   );

		   $codes = $this -> model -> my_find($params);
		   if (!$codes) {
			   $this -> _back('code数据获取失败');
		   }

		   //获取机器人信息
		   $params = array(

			   'table_name' => 'chat_bot',

			   'where' => "id = {$codes['chat_bot_id']}"

		   );

		   $chat_bot = $this -> model -> my_find($params);
		   if (!$chat_bot) {
			   $this -> _back('chat数据获取失败');
		   }

		   //获取活动信息
		   $params = array(

			   'table_name' => 'group_activity',

			   'where' => "chat_bot_id = {$codes['chat_bot_id']} and type = 1  AND is_del = 0"

		   );

		   $group_activity = $this -> model -> my_find($params);
		   if (!$group_activity) {
			   $this -> _back('activity数据获取失败');
		   }

		   //获取成功邀请人数
		   $params = array(

			   'table_name' => 'codes',

			   'where' => "parent_code = '{$code}' AND status = 3"

		   );

		   $code_count = $this -> model -> get_count($params);
		   $code_rate = $code_count * $group_activity['rate'];
		   if ($codes['status'] == 3) {
		   		$code_rate = $code_rate + $group_activity['group_rate'];
		   }
		   //判断活动时间
		   $activity_status =  -1;
		   if ($group_activity['started_at'] <= time() && $group_activity['stoped_at'] >= time()) {
			   $activity_status =  0;
		   }

		   //获取活动主题
			$params = array(

				'table_name' => 'activity_theme',

				'where' => "id = {$group_activity['theme_id']}"

			);

	    	$activity_theme = $this -> model -> my_find($params);
	    	$group_activity['dashboard_logo'] = "";
	    	if ($activity_theme) {
	    		$group_activity['dashboard_logo'] = $activity_theme['dashboard'] ? $activity_theme['dashboard'] : __PUBLIC__."/img/dashboard1.png";
	    	}

			$this -> assign('activity_status', $activity_status);
			$this -> assign('codes', $codes);
			$this -> assign('chat_bot', $chat_bot);
			$this -> assign('group_activity', $group_activity);
			$this -> assign('code', $code);
			$this -> assign('code_count', $code_count);
			$this -> assign('code_rate', $code_rate);

			if ($_GET['lang'] == 'en') {
			   $this -> display('en_dashboard');
			}else{
			   $this -> display();
			}
	   }



	    /**
		 * 首页：
		 *
		 */
	    public function activity()
	    {

			$id = $_REQUEST['_URL_'][2] ?  $_REQUEST['_URL_'][2] : "";

			//获取活动信息
			$params = array(

				'table_name' => 'group_activity',

				'where' => "id = {$id} and is_del = 0"

			);

	    	$group_activity = $this -> model -> my_find($params);
			if (!$group_activity) {
				$this -> _back('activity数据获取失败');
			}
			//获取活动主题
 			$params = array(

 				'table_name' => 'activity_theme',

 				'where' => "id = {$group_activity['theme_id']}"

 			);

 	    	$activity_theme = $this -> model -> my_find($params);
 	    	$group_activity['bglogo'] = "";
 	    	if ($activity_theme) {
 	    		$group_activity['bglogo'] = $activity_theme['bglogo'] ? $activity_theme['bglogo'] : __PUBLIC__."/img/activity_bg.png";
 	    	}

			$this -> assign('group_activity', $group_activity);

			if ($_GET['lang'] == 'en') {
				$this -> display('en_activity');
			}else{
				$this -> display();
			}
	    }


		/**
		 * 首页：
		 *
		 */
	    public function drafters()
	    {

			$code = $_REQUEST['_URL_'][2] ?  $_REQUEST['_URL_'][2] : "";

			//获取活动信息
			$params = array(

				'table_name' => 'drafters',

				'where' => "code = '{$code}' and is_del = 0"

			);

	    	$drafters = $this -> model -> my_find($params);
			if (!$drafters) {
				$this -> _back('drafters数据获取失败');
			}

			//获取活动信息
			$params = array(

				'table_name' => 'group_activity',

				'where' => "id = {$drafters['activity_id']}"

			);

	    	$group_activity = $this -> model -> my_find($params);

			//获取活动主题
 			$params = array(

 				'table_name' => 'activity_theme',

 				'where' => "id = {$group_activity['theme_id']}"

 			);

 	    	$activity_theme = $this -> model -> my_find($params);
 	    	$group_activity['dashboard_logo'] = "";
 	    	if ($activity_theme) {
 	    		$group_activity['dashboard_logo'] = $activity_theme['dashboard'] ? $activity_theme['dashboard'] : __PUBLIC__."/img/activity_bg.png";
 	    	}

			$this -> assign('drafters', $drafters);
			$this -> assign('chat_bot', $chat_bot);
			$this -> assign('group_activity', $group_activity);
			$this -> assign('code', $code);
			if ($_GET['lang'] == 'en') {
				$this -> display('en_activity');
			}else{
				$this -> display();
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

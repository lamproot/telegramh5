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
		 * 首页：
		 *
		 */
	    public function code()
	    {
			//INSERT INTO `codes` VALUES  ('1', '-1001249040089', '520439802', '0xf6BC0AAc1fdFAf2CCea054F5978350DC9eFc6E83', 'bf3c1eac97f80c7e', '6666', '3', '0', '1518599007', '')
			for ($i=520439802; $i < 520440802; $i++) {
				echo $i;echo "<br>";
			}
			exit;

			$code = $_REQUEST['_URL_'][2] ?  $_REQUEST['_URL_'][2] : $code;
			//获取code信息
			$params = array(

				'table_name' => 'codes',

				'where' => "code = '{$code}'"

			);

	    	$codes = $this -> model -> my_find($params);

			// echo json_encode($codes);
			// echo $code;exit;
			if (!$codes) {
				$this -> _back('code数据获取失败');
			}

			//获取机器人信息
			$params = array(

				'table_name' => 'chat_bot',

				'where' => "chat_id = {$codes['chat_id']}"

			);

	    	$chat_bot = $this -> model -> my_find($params);
			if (!$chat_bot) {
				$this -> _back('chat数据获取失败');
			}

			//获取活动信息
			$params = array(

				'table_name' => 'group_activity',

				'where' => "chat_id = {$codes['chat_id']}"

			);

	    	$group_activity = $this -> model -> my_find($params);
			if (!$group_activity) {
				$this -> _back('activity数据获取失败');
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

			   'where' => "chat_id = {$codes['chat_id']}"

		   );

		   $chat_bot = $this -> model -> my_find($params);
		   if (!$chat_bot) {
			   $this -> _back('chat数据获取失败');
		   }

		   //获取活动信息
		   $params = array(

			   'table_name' => 'group_activity',

			   'where' => "chat_id = {$codes['chat_id']}"

		   );

		   $group_activity = $this -> model -> my_find($params);
		   if (!$group_activity) {
			   $this -> _back('activity数据获取失败');
		   }

		   //获取成功邀请人数
		   $params = array(

			   'table_name' => 'codes',

			   'where' => "parent_code = '{$code}'"

		   );

		   $code_count = $this -> model -> get_count($params);
		   $code_rate = $code_count * $group_activity['rate'];

		   //判断活动时间
		   $activity_status =  -1;
		   if ($group_activity['started_at'] <= time() && $group_activity['stoped_at'] >= time()) {
			   $activity_status =  0;
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
	}

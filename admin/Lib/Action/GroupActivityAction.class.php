<?php if (!defined('THINK_PATH')) exit();//判断是否加载thinkphp,如果否则退出
/*********文件描述*********
 * @last update
 * @alter
 * @version 1.0.0
 *
 * 功能简介：
 * @author
 * @copyright
 * @time
 * @version 1.0.0
 */
	class GroupActivityAction extends CommonAction {

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

			$this -> model = D('Common');
		}

	    /**
		 * 展示型首页
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function index()
	    {
			$chat_bot_id = $_GET['chat_bot_id'];
			$where = "is_del = 0";

			if ($_GET['chat_bot_id']) {
	        	$where = $where . " AND chat_bot_id = {$chat_bot_id}";
			}

	    	$params = array(

	    		'table_name' => 'group_activity',

	    		'order' => 'id desc',

	    		'where' => $where
	    	);


	    	$result = $this -> model -> order_select($params);

	    	// foreach ($result['result'] as $key => $value) {
	    	// 	$result['result'][$key]['content'] = json_decode($result['result'][$key]['content'], true);
	    	// }

	    	$this -> assign('result', $result);

	    	$this -> display();
	    }



	    /**
		 * 新增
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function add()
	    {

			$form_key = htmlspecialchars($_POST['form_key']);

	    	if ($form_key == 'yes')
	    	{
				$data = $_POST;
				$data['started_at'] = strtotime($data['started_at']);
				$data['stoped_at'] = strtotime($data['stoped_at']);
				$data['created_at'] = time();
				$logo = $this -> _upload_pic('group_activity');

				if ($logo['status'] == 1)
				{
					$data['url'] = $logo['msg'];
					$data['logo'] = "http://".$_SERVER['HTTP_HOST'] ."/Uploads/images/group_activity/".$data['url'];
				}

	    		$params = array(

	    			'table_name' => 'group_activity',

	    			'data' => $data
	    		);

	    		$chat_bot_add = $this -> model -> my_add($params);

	    		if ($chat_bot_add)
	    		{

	    			if ($data['type'] == 1) {
	    				//添加机器人code
						$botcode_data['from_id'] = 1;
						$botcode_data['from_username'] = "机器人账号";
						$botcode_data['eth'] = "0000000000000000";
						$botcode_data['code'] = $this->short_md5(md5($chat_bot_add."_".$botcode_data['eth']."_telegram"));
						$botcode_data['status'] = 3;
						$botcode_data['created_at'] = time();
						$botcode_data['updated_at'] = time();
						$botcode_data['chat_bot_id'] = $data['chat_bot_id'];
						$botcode_data['activity_id'] = $chat_bot_add;

						$botcode_params = array(

				    		'table_name' => 'codes',

				    		'data' => $botcode_data
				    	);

				    	$botcode_add = $this -> model -> my_add($botcode_params);
	    			}

					$this -> _alert('添加成功');
					redirect(__APP__.'/GroupActivity/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
	    		}
	    		else
	    		{
	    			$this -> _back('保存失败 请稍后重试');
	    		}
	    	}

	    	$group_params = array(

	    		'table_name' => 'group_activity',

	    		'order' => 'id desc',

	    		'where' => "chat_bot_id = {$chat_bot_id}"
	    	);

	    	$result = $this -> model -> my_find($group_params);

			$params = array(

	    		'table_name' => 'chat_bot',

	    		'order' => 'id desc',

	    		'where' => ""
	    	);


	    	$chatBotList = $this -> model -> easy_select($params);
	    	$this -> assign('result', $result);
	    	$this -> assign('chatBotList', $chatBotList);

	    	$this -> display();
	    }

	    /**
		 * 编辑
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function edit()
	    {
	    	$id = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

	    	if (!$id)
	    	{
	    		$this -> _back('错误的参数');
	    	}

			$form_key = htmlspecialchars($_POST['form_key']);

	    	if ($form_key == 'yes')
	    	{
				$data = $_POST;

				$data['started_at'] = strtotime($data['started_at']);
				$data['stoped_at'] = strtotime($data['stoped_at']);
				$logo = $this -> _upload_pic('group_activity');

				if ($logo['status'] == 1)
				{
					$data['url'] = $logo['msg'];
					$data['logo'] = "http://".$_SERVER['HTTP_HOST'] ."/Uploads/images/group_activity/".$data['url'];
				}

	    		$params = array(

	    			'table_name' => 'group_activity',

	    			'where' => "id = {$_POST['id']}",

	    			'data' => $data
	    		);

	    		$chat_bot_save = $this -> model -> my_save($params);

	    		if ($chat_bot_save)
	    		{
					$this -> _alert('保存成功');
					// $params = array(
					//
			    	// 	'table_name' => 'codes',
					//
			    	// 	'order' => 'id desc',
					//
			    	// 	'where' => "activity_id = {$_POST['id']} and from_id = 1  and status = 3"
			    	// );
					// $botCode = $this -> model -> my_find($params);
					//
					// if (!$botCode) {
					// 	//添加机器人code
					// 	$botcode_data['from_id'] = 1;
					// 	$botcode_data['from_username'] = "机器人账号";
					// 	$botcode_data['eth'] = "0000000000000000";
					// 	$botcode_data['code'] = $this->short_md5(md5($_POST['id']."_".$botcode_data['eth']."_telegram"));
					// 	$botcode_data['status'] = 3;
					// 	$botcode_data['created_at'] = time();
					// 	$botcode_data['updated_at'] = time();
					// 	$botcode_data['chat_bot_id'] = $data['chat_bot_id'];
					// 	$botcode_data['activity_id'] = $_POST['id'];
					//
					// 	$botcode_params = array(
					//
					// 		'table_name' => 'codes',
					//
					// 		'data' => $botcode_data
					// 	);
					//
					// 	$botcode_add = $this -> model -> my_add($botcode_params);
					// }

					redirect(__APP__.'/GroupActivity/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
	    		}
	    		else
	    		{
	    			$this -> _back('保存失败 请稍后重试');
	    		}
	    	}

	    	$group_params = array(

	    		'table_name' => 'group_activity',

	    		'order' => 'id desc',

	    		'where' => "id = {$id}"
	    	);

	    	$result = $this -> model -> my_find($group_params);

			$params = array(

	    		'table_name' => 'chat_bot',

	    		'order' => 'id desc',

	    		'where' => ""
	    	);
	    	$chatBotList = $this -> model -> easy_select($params);

	    	$params = array(

	    		'table_name' => 'codes',

	    		'order' => 'id desc',

	    		'where' => "activity_id = {$_GET['id']} and from_id = 1  and status = 3"
	    	);


	    	$botCode = $this -> model -> my_find($params);

	    	$this -> assign('result', $result);
	    	$this -> assign('chatBotList', $chatBotList);
	    	$this -> assign('botCode', $botCode);



	    	$this -> display();
	    }

	    /**
		 * 删除
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function delete()
	    {
	    	$id = isset($_GET['id']) ? intval($_GET['id']) : $this -> _back('错误的参数');

	    	$data['is_del'] = 1;

	    	$data['updated_at'] = time();

	    	$params = array(

	    		'table_name' => 'group_activity',

	    		'where' => "id = {$id} AND is_del = 0",

	    		'data' => $data
	    	);

	    	$group_activity_save = $this -> model -> my_save($params);

	    	if ($group_activity_save)
	    	{
				redirect(__APP__.'/GroupActivity/index', 0);
	    	}
	    	else
	    	{
	    		$this -> _back('删除失败 请稍后重试');
	    	}
	    }

	    /**
		 * 启用/禁用
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function status()
	    {
	    	$id = isset($_GET['id']) ? intval($_GET['id']) : $this -> _back('错误的参数');

	    	$type = isset($_GET['type']) ? intval($_GET['type']) : $this -> _back('错误的参数');

	    	$data['updated_at'] = time();

	    	if ($type == 0)
	    	{
	    		//禁用
	    		$data['status'] = 1;
	    	}
	    	else
	    	{
	    		//启用
	    		$data['status'] = 0;
	    	}

	    	$params = array(

	    		'table_name' => 'chat_bot',

	    		'where' => "id = {$id} AND is_del = 0 AND status = {$type}",

	    		'data' => $data
	    	);

	    	$chat_bot_save = $this -> model -> my_save($params);

	    	if ($chat_bot_save)
	    	{
	    		redirect(__APP__.'/ChatBot/index', 0);
	    	}
	    	else
	    	{
	    		$this -> _back('标注失败 请稍后重试');
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

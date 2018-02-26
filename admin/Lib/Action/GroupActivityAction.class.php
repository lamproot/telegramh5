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
			$chat_id = $_GET['chat_id'];

			$form_key = htmlspecialchars($_POST['form_key']);

	    	if ($form_key == 'yes')
	    	{
				$data = $_POST;

				$data['started_at'] = strtotime($data['started_at']." 00:00:00");
				$data['stoped_at'] = strtotime($data['stoped_at']." 23:59:59");

	    		$params = array(

	    			'table_name' => 'telegram.group_activity',

	    			'where' => "chat_id = {$_POST['chat_id']}",

	    			'data' => $data
	    		);

	    		$chat_bot_save = $this -> model -> my_save($params);

	    		if ($chat_bot_save)
	    		{
	    			redirect(__APP__.'/GroupActivity/index?chat_id='.$_POST['chat_id'], 0);
	    		}
	    		else
	    		{
	    			$this -> _back('保存失败 请稍后重试');
	    		}
	    	}

	    	$group_params = array(

	    		'table_name' => 'telegram.group_activity',

	    		'order' => 'id desc',

	    		'where' => "chat_id = {$chat_id}"
	    	);

	    	$result = $this -> model -> my_find($group_params);

			if (!$result) {
				# 新增
				$data['chat_id'] = $chat_id;
				$data['created_at'] = time();

				$params = array(

		    		'table_name' => 'telegram.group_activity',

		    		'data' => $data
		    	);

		    	$my_add = $this -> model -> my_add($params);

				if ($my_add) {
			    	$result = $this -> model -> my_find($group_params);
				}
			}

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
	    		$data['token'] = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : $this -> _back('请填写token');
				$data['name'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "";

	    		$data['created_at'] = time();

	    		$data['updated_at'] = time();

	    		$data['is_del'] = 0;

	    		$params = array(

	    			'table_name' => 'telegram.chat_bot',

	    			'data' => $data
	    		);

	    		$chat_bot_add = $this -> model -> my_add($params);

	    		if ($chat_bot_add)
	    		{

	    			redirect(__APP__.'/ChatBot/index', 0);
	    		}
	    		else
	    		{
	    			$this -> _back('创建失败 请稍后重试');
	    		}
	    	}

	    	$this -> assign('result', $result);

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

	    	$params = array(

	    		'table_name' => 'telegram.chat_bot',

	    		'where' => "id = {$id} AND is_del = 0"
	    	);

	    	$result = $this -> model -> my_find($params);

	    	if (!$result)
	    	{
	    		$this -> _back('没有符合的记录');
	    	}

	    	$form_key = htmlspecialchars($_POST['form_key']);

	    	if ($form_key == 'yes')
	    	{
				$data['token'] = isset($_POST['token']) ? htmlspecialchars($_POST['token']) : $this -> _back('请填写token');
				$data['chat_id'] = isset($_POST['chat_id']) ? htmlspecialchars($_POST['chat_id']) : $this -> _back('请填写chat_id');
				$data['master_id'] = isset($_POST['master_id']) ? htmlspecialchars($_POST['master_id']) : $this -> _back('请填写master_id');
				$data['code_cmd'] = isset($_POST['code_cmd']) ? htmlspecialchars($_POST['code_cmd']) : $this -> _back('请填写code_cmd');
				$data['name'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "";

	    		$data['updated_at'] = time();

	    		$params = array(

	    			'table_name' => 'telegram.chat_bot',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$chat_bot_save = $this -> model -> my_save($params);

	    		if ($chat_bot_save)
	    		{
	    			redirect(__APP__.'/ChatBot/index', 0);
	    		}
	    		else
	    		{
	    			$this -> _back('保存失败 请稍后重试');
	    		}
	    	}

	    	$this -> assign('result', $result);

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

	    		'table_name' => 'telegram.chat_bot',

	    		'where' => "id = {$id} AND is_del = 0",

	    		'data' => $data
	    	);

	    	$chat_bot_save = $this -> model -> my_save($params);

	    	if ($chat_bot_save)
	    	{
	    		redirect(__APP__.'/ChatBot/index', 0);
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

	    		'table_name' => 'telegram.chat_bot',

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
	}

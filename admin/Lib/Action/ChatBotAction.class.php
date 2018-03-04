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
	class ChatBotAction extends CommonAction {

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
	    	$params = array(

	    		'table_name' => 'chat_bot',

	    		'order' => 'id desc',

	    		'where' => "is_del = 0"
	    	);

	    	$result = $this -> model -> order_select($params);

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

				//查询token 是否存在
				$params = array(

	    			'table_name' => 'chat_bot',

	    			'where' => "token = '{$data['token']}'"
	    		);

	    		$chat_bot_find = $this -> model -> my_find($params);

				if ($chat_bot_find) {
					$this -> _back($data['token'].'已存在 请重新添加');
				}

				$data['name'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : "";
				$data['remark'] = isset($_POST['remark']) ? htmlspecialchars($_POST['remark']) : "";
	    		$data['created_at'] = time();
	    		$data['updated_at'] = time();

	    		$data['is_del'] = 0;

	    		$params = array(

	    			'table_name' => 'chat_bot',

	    			'data' => $data
	    		);

	    		$chat_bot_add = $this -> model -> my_add($params);

	    		if ($chat_bot_add)
	    		{
					//添加默认初始化命令
					$default = [
						[
							"cmd" => "/new_member",
							"type" => 1,
							"content" => "欢迎你加入 命令如下: /ping 连接 /hitokoto 文字 /bing 图片 /whoami 我是谁 /code Code码处理 /me 我的-Code码"
						],
						[
							"cmd" => "/start",
							"type" => 1,
							"content" => "欢迎阅读使用手册 命令如下: /ping 连接 /hitokoto 文字 /bing 图片 /whoami 我是谁 /code Code码处理 /me 我的-Code码"
						],
						[
							"cmd" => "/code",
							"type" => 2,
							"content" => "Your code: {{code}}. SUCCESS & DONE! For every friend you invite from now on, you will get 188 SSS from our Airdrop event within a month 你的驗證碼：{{code}}，校驗成功！每邀请一个好友，你将在 1 個月内收到 188 SSS 的空投獎勵。 Your share link （你的分享鏈接）：http://m.name-technology.fun/Index/code/{{code}}"
						]
					];

					foreach ($default as $key => $value) {
						$data['cmd'] = $value['cmd'];
						$data['chat_bot_id'] = $chat_bot_add;
						$data['content'] = $value['content'];
						$data['type'] = $value['type'];
						$data['created_at'] = time();
						$data['updated_at'] = time();
						$data['is_del'] = 0;
						$params = array(

							'table_name' => 'chat_command',

							'data' => $data
						);

						$this -> model -> my_add($params);
					}

					//初始化回调
					$url = 'https://api.telegram.org/bot' . $data['token'] . '/setWebhook';
					$param = ["url" => "https://m.name-technology.fun/callback.php/Callback/run?t=".time()];
					$ret = $this->fetch ($url, $param);
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

	    		'table_name' => 'chat_bot',

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
				$data['remark'] = isset($_POST['remark']) ? htmlspecialchars($_POST['remark']) : "";

	    		$data['updated_at'] = time();

	    		$params = array(

	    			'table_name' => 'chat_bot',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$chat_bot_save = $this -> model -> my_save($params);

	    		if ($chat_bot_save)
	    		{
					//初始化回调
					$url = 'https://api.telegram.org/bot' . $data['token'] . '/setWebhook';
					$param = ["url" => "https://m.name-technology.fun/callback.php/Callback/run?t=".time()];
					$ret = $this->fetch ($url, $param);
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

	    		'table_name' => 'chat_bot',

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
	}

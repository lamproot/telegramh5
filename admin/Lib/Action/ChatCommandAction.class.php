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
	class ChatCommandAction extends CommonAction {

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

	    	$params = array(

	    		'table_name' => 'chat_command',

	    		'order' => 'id desc',

	    		'where' => "is_del = 0 AND chat_bot_id = {$chat_bot_id}"
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
	    		$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
				$data['chat_bot_id'] = isset($_POST['chat_bot_id']) ? htmlspecialchars($_POST['chat_bot_id']) : $this -> _back('请填写chat_bot_id');
				$data['content'] = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : $this -> _back('请填写content');
				$data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : 1;

				if (intval($data['type']) === 3) {
					//文件上传处理
					$logo = $this -> _upload_pic('command');

					if ($logo['status'] == 1)
					{
						$data['url'] = $logo['msg'];
					}
					elseif ($logo['status'] == 0)
					{
						$this -> _back($logo['msg']);
					}

					$data['url'] = "/Uploads/images/command/".$data['url'];
				}

				if (intval($data['type']) === 4) {
					//文件上传处理
					$logo = $this -> _upload_file('command_file');

					if ($logo['status'] == 1)
					{
						$data['url'] = $logo['msg'];
					}
					elseif ($logo['status'] == 0)
					{
						$this -> _back($logo['msg']);
					}

					$data['url'] = "/Uploads/file/command_file/".$data['url'];
				}


	    		$data['created_at'] = time();
	    		$data['updated_at'] = time();
	    		$data['is_del'] = 0;
	    		$params = array(

	    			'table_name' => 'chat_command',

	    			'data' => $data
	    		);

	    		$chat_command_add = $this -> model -> my_add($params);

	    		if ($chat_command_add)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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
		 * 新增
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function add_more()
	    {
	    	$form_key = htmlspecialchars($_POST['form_key']);
	    	if ($form_key == 'yes')
	    	{
	    		$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
				$data['chat_bot_id'] = isset($_POST['chat_bot_id']) ? htmlspecialchars($_POST['chat_bot_id']) : $this -> _back('请填写chat_bot_id');
				$content = isset($_POST['content']) ? ($_POST['content']) : $this -> _back('请填写content');
				$data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : 1;

				if (intval($data['type']) === 5) {
					//文件上传处理
					$logo = $this -> _upload_pic_all('command');

					foreach ($logo as $key => $value) {
						if ($value['file']['status'] == 1)
						{
							$data['content'][$key]['url'] = "/Uploads/images/command/".$value['file']['msg'];

							$data['content'][$key]['note'] = isset($content[$key]) ? $content[$key] : "";
						}
						elseif ($value['file']['status'] == 0)
						{
							$this -> _back($value['file']['msg']);
						}
					}
				}
				$data['content'] = json_encode($data['content']);
	    		$data['created_at'] = time();
	    		$data['updated_at'] = time();
	    		$data['is_del'] = 0;
	    		$params = array(

	    			'table_name' => 'chat_command',

	    			'data' => $data
	    		);

	    		$chat_command_add = $this -> model -> my_add($params);

	    		if ($chat_command_add)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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

	    		'table_name' => 'chat_command',

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
				$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
				$data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : $this -> _back('请填写type');
				$data['content'] = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : $this -> _back('请填写content');
	    		$data['updated_at'] = time();

				$data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : 1;


				if (intval($data['type']) === 3) {
					//文件上传处理
					$logo = $this -> _upload_pic('command');

					if ($logo['status'] == 1)
					{
						$data['url'] = $logo['msg'];
						$data['url'] = "/Uploads/images/command/".$data['url'];
					}
				}

				if (intval($data['type']) === 4) {
					//文件上传处理
					$logo = $this -> _upload_file('command_file');

					if ($logo['status'] == 1)
					{
						$data['url'] = $logo['msg'];
						$data['url'] = "/Uploads/file/command_file/".$data['url'];
					}
				}


	    		$params = array(

	    			'table_name' => 'chat_command',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$chat_command_save = $this -> model -> my_save($params);

	    		if ($chat_command_save)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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
		 * 编辑
		 *
		 * 参数描述：
		 *
		 *
		 *
		 * 返回值：
		 *
		 */
	    public function edit_more()
	    {
	    	$id = isset($_POST['id']) ? intval($_POST['id']) : intval($_GET['id']);

	    	if (!$id)
	    	{
	    		$this -> _back('错误的参数');
	    	}

	    	$params = array(

	    		'table_name' => 'chat_command',

	    		'where' => "id = {$id} AND is_del = 0"
	    	);

	    	$result = $this -> model -> my_find($params);

			$result['content'] = json_decode($result['content'], true);

	    	if (!$result)
	    	{
	    		$this -> _back('没有符合的记录');
	    	}

	    	$form_key = htmlspecialchars($_POST['form_key']);

	    	if ($form_key == 'yes')
	    	{
				$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
				$data['chat_bot_id'] = isset($_POST['chat_bot_id']) ? htmlspecialchars($_POST['chat_bot_id']) : $this -> _back('请填写chat_bot_id');
				$content = isset($_POST['content']) ? ($_POST['content']) : [];
				$editcontent = isset($_POST['editcontent']) ? ($_POST['editcontent']) : [];
				$editurl = isset($_POST['editurl']) ? ($_POST['editurl']) : [];
				$data['type'] = isset($_POST['type']) ? htmlspecialchars($_POST['type']) : 1;

				foreach ($editcontent as $key => $value) {
					$data['content'][$key]['note'] = $value;
					$data['content'][$key]['url'] = $editurl[$key];
				}


				if (intval($data['type']) === 5) {
					//文件上传处理
					$logo = $this -> _upload_pic_all('command');
					$keys = array_keys($logo);
					if (isset($logo['status']) && $logo['status'] == 0) {
						# code...
					}else{
						foreach ($keys as $kkey => $kvalue) {
							if (isset($logo[$kvalue])) {
								if ($kvalue == 'file') {
									foreach ($logo[$kvalue] as $key => $value) {
										if ($value['status'] == 1)
										{
											$file['url'] = "/Uploads/images/command/".$value['msg'];

											$file['note'] = isset($content[$key]) ? $content[$key] : "";
											$data['content'][] = $file;
										}
										elseif ($value['status'] == 0)
										{
											$this -> _back($value['msg']);
										}
									}
								}else{
									$k = str_replace("file_", "", $kvalue);

									if ($logo[$kvalue][0]['status'] == 1)
									{
										$data['content'][$k]['url'] = "/Uploads/images/command/".$logo[$kvalue][0]['msg'];
									}
									elseif ($logo[$kvalue][0]['status'] == 0)
									{
										$this -> _back($logo[$kvalue][0]['msg']);
									}
								}
							}
						}
					}


				}
				$data['content'] = json_encode($data['content']);
	    		$data['updated_at'] = time();

	    		$params = array(

	    			'table_name' => 'chat_command',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$chat_command_save = $this -> model -> my_save($params);

	    		if ($chat_command_save)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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
			$chat_bot_id = $_GET['chat_bot_id'];

	    	$data['is_del'] = 1;
	    	$data['updated_at'] = time();

	    	$params = array(

	    		'table_name' => 'chat_command',

	    		'where' => "id = {$id} AND is_del = 0",

	    		'data' => $data
	    	);

	    	$chat_command_save = $this -> model -> my_save($params);

	    	if ($chat_command_save)
	    	{
	    		redirect(__APP__.'/ChatCommand/index?chat_bot_id='.$chat_bot_id, 0);
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

	    		'table_name' => 'chat_command',

	    		'where' => "id = {$id} AND is_del = 0 AND status = {$type}",

	    		'data' => $data
	    	);

	    	$chat_command_save = $this -> model -> my_save($params);

	    	if ($chat_command_save)
	    	{
	    		redirect(__APP__.'/ChatCommand/index', 0);
	    	}
	    	else
	    	{
	    		$this -> _back('标注失败 请稍后重试');
	    	}
	    }
	}

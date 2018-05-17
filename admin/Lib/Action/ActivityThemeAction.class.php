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
	class ActivityThemeAction extends CommonAction {

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

	    		'table_name' => 'activity_theme',

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
	    		$data['name'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : $this -> _back('请填写name');
			
	    		//文件上传处理
				$logo = $this -> _upload_pic_all('activity');

				if ($logo['file']) {

					if ($logo['file'][0]['status'] == 1)
					{
						$data['url'] = $logo['file'][0]['msg'];
						$data['bglogo'] = "http://m.name-technology.fun:8030"."/Uploads/images/activity/".$data['url'];
					}
					elseif ($logo['file'][0]['status'] == 0)
					{
						$this -> _back($logo['file'][0]['msg']);
					}
				}

				if ($logo['file_dashboard']) {
					if ($logo['file_dashboard'][0]['status'] == 1)
					{
						$data['dashboard_url'] = $logo['file_dashboard'][0]['msg'];
						$data['dashboard'] = "http://m.name-technology.fun:8030"."/Uploads/images/activity/".$data['dashboard_url'];
				
					}
					elseif ($logo['file_dashboard'][0]['status'] == 0)
					{
						$this -> _back($logo['file_dashboard'][0]['msg']);
					}
				}
				
				$data['created_at'] = time();
	    		$data['updated_at'] = time();
	    		$data['is_del'] = 0;
	    		$params = array(

	    			'table_name' => 'activity_theme',

	    			'data' => $data
	    		);

	    		$activity_theme_add = $this -> model -> my_add($params);

	    		if ($activity_theme_add)
	    		{
	    			redirect(__APP__.'/ActivityTheme/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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

	    		'table_name' => 'activity_theme',

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
				$data['name'] = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : $this -> _back('请填写name');
			
				//文件上传处理
				$logo = $this -> _upload_pic_all('activity');

				if ($logo['file']) {

					if ($logo['file'][0]['status'] == 1)
					{
						$data['url'] = $logo['file'][0]['msg'];
						$data['bglogo'] = "http://m.name-technology.fun:8030"."/Uploads/images/activity/".$data['url'];
					}
					elseif ($logo['file'][0]['status'] == 0)
					{
						$this -> _back($logo['file'][0]['msg']);
					}
				}

				if ($logo['file_dashboard']) {
					if ($logo['file_dashboard'][0]['status'] == 1)
					{
						$data['dashboard_url'] = $logo['file_dashboard'][0]['msg'];
						$data['dashboard'] = "http://m.name-technology.fun:8030"."/Uploads/images/activity/".$data['dashboard_url'];
				
					}
					elseif ($logo['file_dashboard'][0]['status'] == 0)
					{
						$this -> _back($logo['file_dashboard'][0]['msg']);
					}
				}
				$data['updated_at'] = time();

	    		$params = array(

	    			'table_name' => 'activity_theme',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$activity_theme_save = $this -> model -> my_save($params);

	    		if ($activity_theme_save)
	    		{
	    			redirect(__APP__.'/ActivityTheme/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
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

	    		'table_name' => 'activity_theme',

	    		'where' => "id = {$id} AND is_del = 0",

	    		'data' => $data
	    	);

	    	$activity_theme_save = $this -> model -> my_save($params);

	    	if ($activity_theme_save)
	    	{
	    		redirect(__APP__.'/ActivityTheme/index?chat_bot_id='.$chat_bot_id, 0);
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

	    		'table_name' => 'activity_theme',

	    		'where' => "id = {$id} AND is_del = 0 AND status = {$type}",

	    		'data' => $data
	    	);

	    	$activity_theme_save = $this -> model -> my_save($params);

	    	if ($activity_theme_save)
	    	{
	    		redirect(__APP__.'/ActivityTheme/index', 0);
	    	}
	    	else
	    	{
	    		$this -> _back('标注失败 请稍后重试');
	    	}
	    }
	}

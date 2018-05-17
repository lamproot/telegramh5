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
	class ChatPushAction extends CommonAction {

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

	    		'table_name' => 'chat_push',

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
	    public function push()
	    {
	    	$form_key = htmlspecialchars($_POST['form_key']);
	    	if ($form_key == 'yes')
	    	{
	    		//$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
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

					$data['url'] = ""http://m.name-technology.fun:8030"."/Uploads/images/command/".$data['url'];
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

					$data['url'] = ""http://m.name-technology.fun:8030"."/Uploads/file/command_file/".$data['url'];
				}


				if ($data['chat_bot_id'] && $data['chat_bot_id'] != "") {
					$params = array(

						'table_name' => 'chat_bot',

						'where' => "id = {$data['chat_bot_id']}"
					);

					$chatBot = $this -> model -> my_find($params);

					if ($chatBot && isset($chatBot['token'])) {
						$_SESSION['admin']['token'] = $chatBot['token'];
						if ($data['type'] == 1) {
							$result = $this->sendMessage($chatBot['chat_id'], $data['content']);
						}

						if ($data['type'] == 3) {
							$result = $this->sendPhoto($chatBot['chat_id'], $data['url'], $data['content']);
						}

						if ($data['type'] == 4) {
							$result = $this->sendDocument($chatBot['chat_id'],$data['url'], $data['content']);
						}

						//添加推送数据

			    		$data['created_at'] = time();
			    		$data['updated_at'] = time();
			    		$data['is_del'] = 0;
			    		$params = array(

			    			'table_name' => 'chat_push',

			    			'data' => $data
			    		);

			    		$chat_push_add = $this -> model -> my_add($params);

						if ($chat_push_add) {
							redirect(__APP__.'/ChatPush/index?chat_bot_id='.$_POST['chat_bot_id'], 0);
						}else{
							$this -> _back("推送数据添加失败");
						}
					}else{
						$this -> _back("token数据获取失败");
					}
				}
	    	}

	    	$this -> assign('result', $result);

	    	$this -> display();
	    }


	}

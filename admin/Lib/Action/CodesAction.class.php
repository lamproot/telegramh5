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
	class CodesAction extends CommonAction {

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

			$start = $_GET['start'] ? strtotime($_GET['start']) : "";

	        $stop = $_GET['stop'] ? strtotime($_GET['stop']) + 24 * 60 * 60 : "" ;

	        $where = "chat_id = {$chat_id} AND status = 3";

	        if($start && $stop){

	            $where = $where." AND created_at >= {$start} AND created_at <= {$stop}";

	        }

	    	$params = array(

	    		'table_name' => 'telegram.codes',

	    		'order' => 'id desc',

	    		'where' => $where
	    	);

	    	$result = $this -> model -> order_select($params);

			foreach ($result['result'] as $key => $value) {

				$params = array(

		    		'table_name' => 'telegram.codes',

		    		'order' => 'id desc',

		    		'where' => "parent_code = '{$value['code']}'"
		    	);

		    	$result['result'][$key]['invited']  = $this -> model -> get_count($params);
			}

	    	$this -> assign('result', $result);

	    	$this -> display();
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
	    public function user()
	    {

			$chat_id = $_GET['chat_id'];
			$parent_code = $_GET['parent_code'];

	    	$params = array(

	    		'table_name' => 'telegram.codes',

	    		'order' => 'id desc',

	    		'where' => "chat_id = {$chat_id} AND parent_code = '{$parent_code}' AND status = 3"
	    	);

	    	$result = $this -> model -> order_select($params);

			// foreach ($result['result'] as $key => $value) {
            //
			// 	$params = array(
            //
		    // 		'table_name' => 'telegram.codes',
            //
		    // 		'order' => 'id desc',
            //
		    // 		'where' => "parent_code = '{$value['code']}'"
		    // 	);
            //
		    // 	$result['result'][$key]['invited']  = $this -> model -> get_count($params);
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
	    		$data['cmd'] = isset($_POST['cmd']) ? htmlspecialchars($_POST['cmd']) : $this -> _back('请填写cmd');
				$data['chat_id'] = isset($_POST['chat_id']) ? htmlspecialchars($_POST['chat_id']) : $this -> _back('请填写chat_id');
				$data['content'] = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : $this -> _back('请填写content');
				$data['type'] = 1;
	    		$data['created_at'] = time();
	    		$data['updated_at'] = time();
	    		$data['is_del'] = 0;

	    		$params = array(

	    			'table_name' => 'telegram.codes',

	    			'data' => $data
	    		);

	    		$codes_add = $this -> model -> my_add($params);

	    		if ($codes_add)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_id='.$_POST['chat_id'], 0);
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

	    		'table_name' => 'telegram.codes',

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

	    		$params = array(

	    			'table_name' => 'telegram.codes',

	    			'where' => "id = {$id}",

	    			'data' => $data
	    		);

	    		$codes_save = $this -> model -> my_save($params);

	    		if ($codes_save)
	    		{
	    			redirect(__APP__.'/ChatCommand/index?chat_id='.$_POST['chat_id'], 0);
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
			$chat_id = $_GET['chat_id'];

	    	$data['is_del'] = 1;
	    	$data['updated_at'] = time();

	    	$params = array(

	    		'table_name' => 'telegram.codes',

	    		'where' => "id = {$id} AND is_del = 0",

	    		'data' => $data
	    	);

	    	$codes_save = $this -> model -> my_save($params);

	    	if ($codes_save)
	    	{
	    		redirect(__APP__.'/ChatCommand/index?chat_id='.$chat_id, 0);
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

	    		'table_name' => 'telegram.codes',

	    		'where' => "id = {$id} AND is_del = 0 AND status = {$type}",

	    		'data' => $data
	    	);

	    	$codes_save = $this -> model -> my_save($params);

	    	if ($codes_save)
	    	{
	    		redirect(__APP__.'/ChatCommand/index', 0);
	    	}
	    	else
	    	{
	    		$this -> _back('标注失败 请稍后重试');
	    	}
	    }
	}

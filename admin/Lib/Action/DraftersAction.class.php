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
	class DraftersAction extends CommonAction {

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

			$activity_id = $_GET['activity_id'];

	        $where = "activity_id = {$activity_id}";

	    	$params = array(

	    		'table_name' => 'drafters',

	    		'order' => 'id desc',

	    		'where' => $where
	    	);


	    	$result = $this -> model -> order_select($params);


	    	foreach ($result['result'] as $key => $value) {
	    		$result['result'][$key]['content'] = json_decode($result['result'][$key]['content'], true);
	    	}

	    	$this -> assign('result', $result);

	    	$this -> display();
	    }
	}

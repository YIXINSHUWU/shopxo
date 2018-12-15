<?php
namespace app\admin\controller;

use app\service\ResourcesService;
use app\service\GoodsService;
use app\service\RegionService;
use app\service\BrandService;

/**
 * 商品管理
 * @author   Devil
 * @blog     http://gong.gg/
 * @version  0.0.1
 * @datetime 2016-12-01T21:51:08+0800
 */
class Goods extends Common
{
	/**
	 * 构造方法
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-03T12:39:08+0800
	 */
	public function __construct()
	{
		// 调用父类前置方法
		parent::__construct();

		// 登录校验
		$this->Is_Login();

		// 权限校验
		$this->Is_Power();
	}

	/**
     * [Index 商品列表]
     * @author   Devil
     * @blog     http://gong.gg/
     * @version  0.0.1
     * @datetime 2016-12-06T21:31:53+0800
     */
	public function Index()
	{
		// 参数
		$params = input();

		// 条件
		$where = GoodsService::GetAdminIndexWhere($params);

		// 总数
		$total = GoodsService::GoodsTotal($where);

		// 分页
		$number = MyC('admin_page_number');
		$page_params = array(
				'number'	=>	$number,
				'total'		=>	$total,
				'where'		=>	$params,
				'page'		=>	isset($params['page']) ? intval($params['page']) : 1,
				'url'		=>	url('admin/goods/index'),
			);
		$page = new \base\Page($page_params);

		// 获取数据列表
		$data_params = [
			'where'			=> $where,
			'm'				=> $page->GetPageStarNumber(),
			'n'				=> $number,
			'is_category'	=> 1,
		];
		$data = GoodsService::GoodsList($data_params);

		// 是否上下架
		$this->assign('common_goods_is_shelves_list', lang('common_goods_is_shelves_list'));

		// 是否首页推荐
		$this->assign('common_is_text_list', lang('common_is_text_list'));

		$this->assign('params', $params);
		$this->assign('page_html', $page->GetPageHtml());
		$this->assign('data', $data);
		return $this->fetch();
	}

	/**
	 * [SaveInfo 商品添加/编辑页面]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-14T21:37:02+0800
	 */
	public function SaveInfo()
	{
		// 参数
		$params = input();

		// 商品信息
		if(!empty($params['id']))
		{
			$data_params = [
				'where'				=> ['id'=>$params['id']],
				'm'					=> 0,
				'n'					=> 1,
				'is_photo'			=> 1,
				'is_content_app'	=> 1,
				'is_category'		=> 1,
			];
			$data = GoodsService::GoodsList($data_params);
			if(empty($data[0]))
			{
				return $this->error('商品信息不存在', url('admin/goods/index'));
			}
			$this->assign('data', $data[0]);

			// 获取商品编辑规格
			$specifications = GoodsService::GoodsEditSpecifications($data[0]['id']);
			$this->assign('specifications', $specifications);
		}

		// 地区信息
		$this->assign('region_province_list', RegionService::RegionItems(['pid'=>0]));

		// 商品分类
		$this->assign('category_list', GoodsService::GoodsCategory());

		// 品牌分类
		$this->assign('brand_list', BrandService::CategoryBrand());

		// 参数
		$this->assign('params', $params);

		return $this->fetch();
	}

	/**
	 * [Save 商品添加/编辑]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-14T21:37:02+0800
	 */
	public function Save()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error(lang('common_unauthorized_access'));
		}

		// 开始操作
		$params = input('post.');
		$params['admin'] = $this->admin;
		$ret = GoodsService::GoodsSave($params);
		return json($ret);
	}

	/**
	 * [Delete 商品删除]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2016-12-15T11:03:30+0800
	 */
	public function Delete()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error(lang('common_unauthorized_access'));
		}

		// 开始操作
		$params = input('post.');
		$params['admin'] = $this->admin;
		$ret = GoodsService::GoodsDelete($params);
		return json($ret);
	}

	/**
	 * [StatusShelves 上下架状态更新]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2017-01-12T22:23:06+0800
	 */
	public function StatusShelves()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error(lang('common_unauthorized_access'));
		}

		// 开始操作
		$params = input('post.');
		$params['admin'] = $this->admin;
		$params['field'] = 'is_shelves';
		$ret = GoodsService::GoodsStatusUpdate($params);
		return json($ret);
	}

	/**
	 * [StatusHomeRecommended 是否首页推荐状态更新]
	 * @author   Devil
	 * @blog     http://gong.gg/
	 * @version  0.0.1
	 * @datetime 2017-01-12T22:23:06+0800
	 */
	public function StatusHomeRecommended()
	{
		// 是否ajax
		if(!IS_AJAX)
		{
			return $this->error(lang('common_unauthorized_access'));
		}

		// 开始操作
		$params = input('post.');
		$params['admin'] = $this->admin;
		$params['field'] = 'is_home_recommended';
		$ret = GoodsService::GoodsStatusUpdate($params);
		return json($ret);
	}
}
?>
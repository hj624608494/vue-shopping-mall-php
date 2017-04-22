<?php
namespace app\index\controller;
use think\Session ;
use think\Request;

use app\index\model\Goods;
class Goodscontroller
{
	/*图片上传*/
	public function doUploadImg(){
		if(isset($_FILES['imgFile'])){
			$imgUrl=uploadImg('goods'); /*上传图片并返回图片路径 */  
			echo CURD_result(200,'上传成功',$imgUrl);
		}
	}
    /*删除图片示例*/
    public function delFile(){
        return delFiles('deleteUploadImg','goods');
    }
	// public function index(){
	// 	return view();
	// }
	// public function test(){
		
	// 	// $goods=Goods::get();        //  <<<<--<<<<---<<<<---<<<---关联查询
	// 	// $goods->sale;    //->price;

	// 	// dump($goods);
	// }

	/*查询*/
	/*默认查询   前台（上架）  */
    public function getGoods()
    {
    	echo getGoodsByConditions('defaultNum','');   //前后台公用
    }
	/*查询   后台（上架）   */
    public function getGoodsStatusUp()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	return getGoodsByConditions('defaultNum','true');
    }
	/*查询   后台（下架）   */
    public function getGoodsStatusDown()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	return getGoodsByConditions('StatusDown','false');
    }
    /*查询   后台（全部）   */
    public function getGoodsList()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	return getGoodsByConditions('all','全部');
    }
    /*查询   前台（分类）*/
    public function getGoodsByClass()
    {
		$good=new Goods();
//		$request = Request::instance();
//		$data=$request->param();
//		$cid=$data['cid'];
    	return getGoodsByConditions('ByClass',$_POST['cid']/*$cid*/);
    }
    /*查询   后台（分类）*/
    public function getGoodsByIdOnlyFindProduce()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$good=new Goods();
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		// dump($id) ;
    	return getGoodsByConditions('ByIdOnlyFindProduce',$id);
    }
     public function getGoodsByKeyWord()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	$good=new Goods();
		$request = Request::instance();
		$data=$request->param();
		$keyword=$data['keyword'];
		return getGoodsByConditions('getGoodsByKeyWord',$keyword);
    }

   /*商品详情*/ 
    public function getGoodsById()
    {
		$good=new Goods();
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		$re= getGoodsByConditions('ById',$id);
		// dump($re);
		return $re;
    }
   /*商品详情  根据选择不同的属性组合,筛选库存*/ 
    public function getGoodsByAttr()
    {
		$good=new Goods();
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		$arr=$data['arr'];

		$goodList=$good->where('id',$data['id'])/*->order('createtime desc')*/->find();
		// dump(json_decode($goodList->number;));
		$attr=$goodList->number;  //json格式的数据
		$attr=json_decode($attr);

		$getnumber=0;

		foreach ($attr as $key => $value) {
			$n=0;
			foreach ($value->attrValues as $key2 => $value2) {
				foreach ($arr as $key3 => $value3) {
					if($value2==$value3){
						$n++;
						if($n==count($value->attrValues))
						{
							$getnumber=$value->number;
							// dump('查询成功'.);
						}
					}
				}
			}
		}
	    return CURD_result(200,'库存数量','库存：'.$getnumber.'（件）');
    }
    
    /*添加*/
    public function addGoods()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$good=new Goods();
    	$request = Request::instance();
    	// $imgUrl='';
		$data=$request->param();
		if(isset($data['attr'])){
			if($data['attr']!=''){
				$data2['attr']=json_encode($data['attr']);
			}
		}
		else{
			return CURD_result(2004,'属性不为空','');
		}
		if(isset($data['number'])){
			if($data['number']!=''){
				$data2['number']=json_encode($data['number']);
			}
		}
		else{
			return CURD_result(2004,'数量不为空','');
		}
		if(isset($data['images'])){
			if($data['images']!=''){
				$data2['images']=json_encode($data['images']);
			}
		}
		else{
			return CURD_result(2004,'图片不为空','');
		}
		$data2['createtime']=date('Y-m-d H:i:s');

//		$data['number']=json_encode($data['number']);    //将数组json格式化
//		$data['attr']=json_encode($data['attr']);        //将数组json格式化
//		$data['images']=json_encode($data['images']);    //将数组json格式化
		
		$goodList=$good->save($data2);
		if($goodList){
	    	return CURD_result(200,'插入成功',$goodList);
		}
		else{
	    	return CURD_result(2004,'操作失败','');
		}
    }
    /* 修改 */
    public function modifyGoods()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$good=new Goods();
    	$request = Request::instance();
    	// $imgUrl='';
		$data=$request->param();
		$id=$data['id'];
		$data2=array();		
		
		$data['number']=json_encode($data['number']);    //将数组json格式化
		$data['attr']=json_encode($data['attr']);
		$data['images']=json_encode($data['images']);

		// if($data['cid']!=''){
		// 	$data2['cid']=$data['cid'];
		// }
		if(isset($data['name'])){
			if($data['name']!=''){
				$data2['name']=$data['name'];
			}
		}
		if(isset($data['introduce'])){
			if($data['introduce']!=''){
			$data2['introduce']=$data['introduce'];
			}
		}
		if(isset($data['price'])){
			if($data['price']!=''){
			$data2['price']=$data['price'];
			}
		}
		if(isset($data['attr'])){
			if($data['attr']!=''){
			$data2['attr']=$data['attr'];
			}
		}
		if(isset($data['number'])){
			if($data['number']!=''){
			$data2['number']=$data['number'];
			}
		}
		if(isset($data['images'])){
			if($data['images']!=''){
			$data2['images']=$data['images'];
			}
		}
		if(isset($data['content'])){
			if($data['content']!=''){
			$data2['content']=$data['content'];
			}
		}
		if(isset($data['produce'])){
			if($data['produce']!=''){
				$data2['produce']=$data['produce'];
			}
		}
		if(isset($data['status'])){
			if($data['status']!=''){
			$data2['status']=$data['status'];
			}
		}
		if(isset($data['createtime'])){
			if($data['createtime']!=''){
			$data2['createtime']=$data['createtime'];
			}
		}
		if(isset($data['cid'])){
			if($data['cid']!=''){
			$data2['cid']=$data['cid'];
			}
		}

		$res=$good->where("id",$id)->update($data2);	    
		if($res){
	    	return CURD_result(200,'修改成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败','');
		}
    }
	/* 修改商品是否推荐*/
	public function modifyGoodsProduce(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$good=new Goods();
		$goodList='';

		$request = Request::instance();
		$parameter=$request->param();
		$goodList=$good->where('id',$parameter['id'])->update(['status'=>'false']);
		if($goodList){
			return CURD_result(200,'修改成功',$goodList);
		}
		else{
			return CURD_result(2004,'操作失败','');
		}
	}
    /*删除*/
    public function delGoods()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$good=new Goods();
    	$request = Request::instance();
		$data=$request->param();
		
		$res=$good->where('id',$data['id'])->select();
		// dump($res);
		// $addressList=$address->delete(11);	    
		if($res==1||$res>1){
			delFiles('hasRelationWithDatabase','goods');
			$res=db('goods')->where('id',$data['id'])->delete();
			db('sale')->where('good_id',$res['id'])->delete();
    		return CURD_result(200,'删除成功',$res);
		}
		else{
	    	return CURD_result(2004,'没有找到这件商品,操作失败',$res);
		}	
    }
}
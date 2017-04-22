<?php
namespace app\index\controller;

use think\Session ;
use app\index\model\Slider;
use app\common\Common;
use think\Db;
use think\Request;
class Slide
{
		/*图片上传*/
	public function doUploadImg(){
		if(isset($_FILES['imgFile'])){
			$imgUrl=uploadImg('slider'); /*上传图片并返回图片路径 */
			echo $imgUrl;
//			return CURD_result(200,'插入成功',$imgUrl);
		}
	}
    /*删除图片示例*/
    public function delFile(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
        
        return delFiles('deleteUploadImg','slider');
    }

	// public function index(){
	// 	return view();
	// }
		/*查询*/
    public function getSlider()           /*首页顶部banner*/
    {
		return getsliderByConditions('首页顶部幻灯片','');
    }

    public function getSliderCenter()           /*首页中间banner*/
    {
		return getsliderByConditions('首页中部幻灯片','');
    }

    public function getSliderBottom()           /*首页底部banner*/
    {
		return getsliderByConditions('首页底部幻灯片','');
    }
		/*查询  ById*/
    public function getSliderById()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$slider=new Slider();
		$request = Request::instance();
		$data=$request->param();
		$sliderList=$slider->where('id',$data['id'])->select();  
		if($sliderList){
	    	return CURD_result(200,'success',$sliderList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$sliderList);
		}
    }
    	/*查询 全部*/
    public function getSliderList()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$slider=new Slider();
		$sliderList=$slider->select();
		if($sliderList){
	    	return CURD_result(200,'success',$sliderList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$sliderList);
		}
    }
    /*添加*/
    public function addSlider()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	$slider=new Slider();
		$request = Request::instance();
		$data=$request->param();
		$data['createtime']=date('Y-m-d H:i:s');

    	// $imgUrl='';
		$sliderList=$slider->save($data);
		if($sliderList){
	    	return CURD_result(200,'插入成功',$sliderList);
		}
		else{
	    	return CURD_result(2004,'操作失败',$sliderList);
		}
    }
    /*修改*/
    public function modifySlider()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

    	$slider=new Slider();
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		$data2=array();

		if($data['type']!=''){
			$data2['type']=$data['type'];
		}
		if($data['image']!=''){
			// 1.删除图片
			$List=null;
			$List=$slider->where('id',$data['id'])/*->order('createtime desc')*/->find();
			$images=$List->image;        //json格式的数据
			if($data['image']!=$images){
				$domain=picDomain();         //picDomain()详见第27行
				$arr=explode($domain,$images);
				array_shift($arr);           //删除第一个元素
				if (file_exists(ROOT .$arr[0])) {
					$img=ROOT .$arr[0];
					$falg=unlink($img);
				}
			}

			// 2.设置图片到$data2 
			$data2['image']=$data['image'];
		}
		if($data['text']!=''){
			$data2['text']=$data['text'];
		}
		if($data['link']!=''){
			$data2['link']=$data['link'];
		}
		if($data['sort']!=''){
			$data2['sort']=$data['sort'];
		}

		$res=$slider->where("id",$id)->update($data2);	    
		if($res){
	    	return CURD_result(200,'修改成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败','');
		}	
    }

    /*删除*/
    public function delSlider()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

    	$slider=new Slider();
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		delFiles('hasRelationWithDatabase','slider');
		$res=db('slider')->where('id',$data['id'])->delete();
		if($res>1||$res==1){
	    	return CURD_result(200,'删除成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败',$res);
		}
    }
}
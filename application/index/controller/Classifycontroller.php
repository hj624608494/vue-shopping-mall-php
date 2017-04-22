<?php
namespace app\index\controller;

use think\Session ;
use app\index\model\Classify;
use app\common\Common;
use think\Db;
use think\Request;
class Classifycontroller
{
	public function doUploadImg(){
		if(isset($_FILES['imgFile'])){
			$imgUrl=uploadImg('classify'); /*上传图片并返回图片路径 */  
			return CURD_result(200,'上传成功',$imgUrl);
		}
	}
    /*删除图片示例*/
    public function delFile(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
        return delFiles('deleteUploadImg','classify');
    }
	// public function index(){
	// 	return view();
	// }
	/*查询*/
    public function getClassify()
    {
		$classify=new Classify();
		$classifyList=$classify->limit(8)->order('sort asc')->select();
		if($classifyList){
	    	return CURD_result(200,'',$classifyList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$classifyList);
		}
    }
	/*查询ById*/
    public function getClassifyById()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$request = Request::instance();
		$data=$request->param();
		$classifyList=$classify->limit(8)->order('sort desc')->where('id',$data['id'])->select();
		if($classifyList){
	    	return CURD_result(200,'',$classifyList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$classifyList);
		}
    }
	/*查询全部*/
    public function getClassifyList()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$classifyList=$classify->order('sort asc')->select();
		if($classifyList){
	    	return CURD_result(200,'',$classifyList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$classifyList);
		}
    }
	/*关键字查询*/
    public function getClassifyByKeyWord()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$request = Request::instance();
		$data=$request->param();

		$classifyList=$classify->where('text',$data['keyword'])->order('sort desc')->select();
		if($classifyList){
	    	return CURD_result(200,'',$classifyList);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$classifyList);
		}
    }
    /*添加*/
    public function addClassify()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$request = Request::instance();
		$data=$request->param();
	$data['createtime']=date('Y-m-d H:i:s');
		// $data['image']='aa.jpg';  //  《《《《
		$classifyList=$classify->save($data);	    
		if($classifyList){
	    	return CURD_result(200,'插入成功',$classifyList);
		}
		else{
	    	return CURD_result(2004,'操作失败',$classifyList);
		}
    }


    /*修改*/
    public function modifyClassify()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$request = Request::instance();
		$data=$request->param();

		$id=$data['id'];
		$data2=array();

		if($data['image']!=''){
			// 1.删除图片
			$List=null;
			$List=$classify->where('id',$data['id'])/*->order('createtime desc')*/->find();

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
		if($data['sort']!=''){
			$data2['sort']=$data['sort'];
		}
//		if($data['link']!=''){
			$data2['link']=$data['link'];
//		}
		$res=$classify->where("id",$id)->update($data2);
		if($res){
	    	return CURD_result(200,'修改成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败','');
		}
    }
    /*删除*/
    public function delClassify()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$classify=new Classify();
		$request = Request::instance();
		$data=$request->param();
		delFiles('hasRelationWithDatabase','classify');
		
		$res=db('classify')->where('id',$data['id'])->delete();
		// dump($res);
		// $addressList=$address->delete(11);	    
		if($res>1||$res==1){
	    	return CURD_result(200,'删除成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败',$res);
		}	
    }
}
<?php
namespace app\index\controller;

use think\Session ;
use app\index\model\Address;
use app\common\Common;
use think\Db;
use think\Request;
class Addresscontroller
{
	// public function index(){
	// 	return view();
	// }
	/*查询*/
    public function getAddress()
    {
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

		$address=new Address();
		$request = Request::instance();
		$parameter=$request->param();
			// $uid=$data["uid"];

		$addressList=$address->where("uid",$parameter['uid'])->order('createtime desc')->select();
		if($addressList){
			return CURD_result(200,'查询成功',$addressList);
		}
		else{
			return CURD_result(2004,'未找到您的地址','');
		}
    }

	/*查询全部*/
    public function getAddressList()
    {
    	// //判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

		$address=new Address();
		$request = Request::instance();
		$parameter=$request->param();
		
		$addressList=$address->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		// $addressList=db('address')->field('id,username,createtime')->select();
		if($addressList){
			if($parameter['currentPage']==1)
			{
				$acount=$address->count();
				return CURD_result_for_acount(200,'yes',$acount,$addressList);
			}
			else{
	    		return CURD_result(200,'yes',$addressList);
			}
			return CURD_result(200,'查询成功',$addressList);
		}
		else{
			return CURD_result(2004,'未找到地址列表','');
		}
    }
    /*添加*/
    public function addAddress()
    {
    	// //判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

		$address=new Address();
		$request = Request::instance();
		$data=$request->param();

		$data['createtime']=date('Y-m-d H:i:s');
		$addressList=$address->save($data);	    
		if($addressList){
	    	return CURD_result(200,'插入成功',$addressList);
		}
		else{
	    	return CURD_result(2004,'操作失败',$addressList);
		}	
    }
        /*修改*/
    public function modifyAddress()
    {
    	// //判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

		$address=new Address();
		$request = Request::instance();
		$data=$request->param();
		$uid=$data["uid"];

		$addressList=$address->where("uid",$uid)->update($data);	    
		if($addressList){
	    	return CURD_result(200,'修改成功',$addressList);
		}
		else{
	    	return CURD_result(2004,'操作失败',$addressList);
		}	
    }

        /*删除*/
    public function delAddress()
    {
    	// //判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }

		$address=new Address();
		$request = Request::instance();
		$data=$request->param();
		
		$res=db('address')->where('id',$data['id'])->where('uid',$data['uid'])->delete();
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
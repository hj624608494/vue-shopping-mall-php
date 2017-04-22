<?php
namespace app\index\controller;

use think\Session ;
use app\index\model\Manager;
use app\common\Common;
use think\Db;
use think\Request;
class Managercontroller
{
	// public function index(){
	// 	return view();
	// }
	/*登录*/
	public function doLogin(){
		$manager=new Manager();

        $request = Request::instance();
		$data=$request->param();

        $username;
        $password;
        if(isset($data['username'])){
            $username=$data['username'];
        }
        else{
            return CURD_result(2004,'登录信息不为空','');
        }
        if(isset($data['password'])){
            $password=$data['password'];
        }
        else{
            return CURD_result(2004,'登录信息不为空','');
        }

		if($manager!=''&&$password!=''){
			$data['password']=md5($password);
			$managerdata=db('manager')/*->field('id,username,createtime')*/->where('username',$username)->find();
			if($managerdata){
				if($managerdata["password"]==$data['password']){
					$managerdata["password"]='';
					SESSION::set('managerdata',$managerdata);
					return CURD_result(200,'登录成功',$managerdata);
				}
				else{
					return CURD_result(2004,'密码不正确，请您重新输入','');
				}
			}
			else{
				return CURD_result(2004,'未找到该用户,请您先注册','');
			}
		}
		else{
			return CURD_result(2004,'登录信息不为空','');
		}

	}
		/*用户列表*/
	public function managerList(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$manager=new Manager();		

/*		$request = Request::instance();
		$parameter=$request->param();
		$managerdata='';
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$managerdata=$manager->field('id,username,createtime')->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}
		else{
			$managerdata=$manager->field('id,username,createtime')->limit(0,20)->order('createtime desc')->select();
		}*/
		 $managerdata=db('manager')->field('id,username,createtime')->select();
		if($managerdata){
/*			if($parameter['currentPage']==1)
			{
				$acount=$manager->count();*/
				return CURD_result(200,'yes',$managerdata);
/*			}
			else{
				return CURD_result(200,'未找到数据','');
			}*/
		}
		else{
			return CURD_result(200,'未找到数据','');
		}
	}
	/*关键字查找*/
	public function managerFindInKeyWord(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$manager=new Manager();

        $request = Request::instance();
		$data=$request->param();
		// dump($data);
		$managerdata=db('manager')->field('id,username,createtime')->where("username",'like','%'.$data['keyword'].'%')->select();
		if($managerdata){
			return CURD_result(200,'查找成功',$managerdata);
		}
		else{
			return CURD_result(200,'未找到数据','');
		}
	}
	/*注册*/
	public function doRegister(){

        $manager=new Manager();

        $request = Request::instance();
		$data=$request->param();
		$username=$data['username'];
		$password=$data['password'];

		// dump($password);
		if($manager!=''&&$password!=''){			
			$exits=$manager->where('username',$username)->find();
			if($exits){
				return CURD_result(2004,'用户名已存在','');
			}
			else{
				$data['password']=md5($password);
				$data['createtime']=date('Y-m-d H:i:s');
				$managerId=$manager->insert($data);
				if($managerId){
					return CURD_result(200,'注册成功',$managerId);
				}
				else{
					return CURD_result(200,'注册失败','');
				}
			}
		}
		else{
			return CURD_result(2004,'注册信息不为空','');
		}

	}
	/*注销*/
	public function doExit(){
		// if(Session::has("managerdata")){
		// 	Session::delete("managerdata");
			return CURD_result(200,'注销成功','');
		// }
		// else{
		// 	return CURD_result(2004,'您尚未登录服务器','');
		// }
	}
	/*删除*/
	public function doDelete(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		return del('delManager');
	}
}
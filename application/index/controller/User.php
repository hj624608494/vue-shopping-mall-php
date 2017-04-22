<?php
namespace app\index\controller;

use think\Request;
use think\Session ;
use app\index\model\Users;
use app\common\Common;
use think\Db;
// session_start();
class User
{
    public function index(){
        return view();
    }
    /*登录*/
    public function doLogin(){
        $user=new Users();

        $request = Request::instance();
        $data=$request->param();   //post();
        $email='';
        $password='';
        if(isset($data['email'])){
            $email=$data['email'];
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

        if($email!=''&&$password!=''){
            $data['password']=md5($password);
            $userdata=Db('users')/*->field('id,email,nickname,createtime')*/->where('email',$email)->find(); 
            if($userdata){
                if($userdata["password"]==$data['password']){
                    $userdata['password']='';
                    SESSION::set('userdata',$userdata);
                    return CURD_result(200,'登录成功',$userdata);
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
    public function userList(){
        //判断是否存在Session
        // if(!managerIsLogin()){
        //  return CURD_result(200,'请先登录哦！','');
        // }
        $user=new Users();
        $request=Request::instance();
        $parameter=$request->param();

        $userdata=db('users')->field('id,email,nickname,createtime')->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->select();

        if($userdata){
            if($parameter['currentPage']==1)
            {
                $acount=db('users')->count();
                return CURD_result_for_acount(200,'yes',$acount,$userdata);
            }
            else{
                return CURD_result(200,'yes',$userdata);
            }
        }
        else{
            return CURD_result(2004,'未找到用户','');
        }

    }
    /*关键字查找*/
    public function userFindInKeyWord(){
        //判断是否存在Session
        // if(!managerIsLogin()){
        //  return CURD_result(200,'请先登录哦！','');
        // }
        $user=new Users();

        $request = Request::instance();
        $data=$request->param();
        // dump($data);
        $userdata=db('users')->field('id,email,nickname,createtime')->where("email",'like','%'.$data['keyword'].'%')->select();
        if($userdata){
            return CURD_result(200,'查找成功',$userdata);
        }
        else{
            return CURD_result(2004,'未找到用户','');
        }
    }
    /*注册*/
    public function doRegister(){
        $user=new Users();
        $request = Request::instance();
        
        $data=$request->param();

        $email='';
        $password='';
        $code='';
        
        if(isset($data['email'])){
            $email=$data['email'];
        }
        else{
            return CURD_result(2004,'email不为空','');
        }
        if(isset($data['password'])){
            $password=$data['password'];
        }
        else{
            return CURD_result(2004,'密码不为空','');
        }       
        if(isset($data['code'])){
            $code=$data['code'];
        }
        else{
            return CURD_result(2004,'验证码不为空','');
        }
        $data1['email']=$email;
        $data1['password']=$password;
        /* +------------------------------------------+
         * |           邮箱验证码Session              |
         * +------------------------------------------+ */
        // if(Session::has("verifycode",'think')){

        //     if(SESSION::get('verifycode','think')==$code){
                
                if($email!=''&&$password!=''){          
                    $exits=$user->where('email',$email)->find();
                    if($exits){
                        return CURD_result(2004,'用户名已存在，您可以直接登录。您如果忘记密码，可以通过邮箱找回哟（O(∩_∩)O）','');
                    }
                    else{
                        $data1["password"]=md5($password);
                        $data1['createtime']=date('Y-m-d H:i:s');
                        $userId=$user->insert($data1);
                        if($userId){
                            return CURD_result(200,'注册成功',$userId);
                        }
                        else{
                            return 'error';
                        }
                        dump($user->getLastSql());
                    }
                }
                else{
                    return CURD_result(2004,'注册信息不为空','');
                }
        //     }
        //     else{
        //         return CURD_result(2004,'验证码不正确','');
        //     }
        // }
        // else{
        //     return CURD_result(2004,'请先获取验证码','');
        // }

    }
    
    /*注销*/
    public function doExit(){

        // if(Session::has("userdata")){
        //     Session::delete("userdata");
            return CURD_result(200,'注销成功','');
        // }
        // else{
        //     return CURD_result(2004,'您尚未登陆','');
        // }
    }
    /*删除*/
    public function doDelete(){
        //判断是否存在Session
        // if(!managerIsLogin()){
        //  return CURD_result(200,'请先登录哦！','');
        // }
        return del('delUser');
    }
    /*修改密码*/
    public function doModifyPwd(){
        //判断是否存在Session
        // if(!userIsLogin()){
        //  return CURD_result(200,'请先登录哦！','');
        // }

        $user=new Users();

        $request = Request::instance();
        $data=$request->param();
        $oldPwd=$data['oldPwd'];
        $newPwd=$data['newPwd'];

        if($oldPwd!=''&&$newPwd!=''){
            // $userdata=Session::get("userdata");
            // $id=$userdata["id"];
            
            $request = Request::instance();
            $data=$request->param();
            $id=$data['id'];
            
            $pwd=Users::where('id',$id)->value("password");
            if($pwd==md5($oldPwd)){
                $res=$user->save(["password"=>md5($newPwd)],['id'=>$id]);
                if($res){
                    return CURD_result(200,'修改成功',$res);
                }
                else{
                    return CURD_result(2004,'修改失败','');
                }
            }
            else{
                return CURD_result(2004,'原始密码不正确','');
            }
        }
        else{
            return CURD_result(2004,'密码不为空','');
        }
    }
    // public function findPwd(){
    //     return view();
    // }
    /*忘记密码*/
    public function doForgetPwd(){
        //判断是否存在Session
        // if(!userIsLogin()){
        //  return CURD_result(200,'请先登录哦！','');
        // }
        $user=new Users();

        $request = Request::instance();
        $data=$request->param();

        $email='';
        $password='';
        $code='';
        $rePwd='';
        if(isset($data['email'])){
            $email=$data['email'];
        }
        else{
            return CURD_result(2004,'email不为空','');
        }
        if(isset($data['password'])){
            $password=$data['password'];
        }
        else{
            return CURD_result(2004,'密码不为空','');
        }       
        if(isset($data['code'])){
            $code=$data['code'];
        }
        else{
            return CURD_result(2004,'验证码不为空','');
        }   
        if(isset($data['rePassword'])){
            $rePassword=$data['rePassword'];
        }
        else{
            return CURD_result(2004,'重复密码不为空','');
        }


        // if(Session::has("verifyCode")){
        //     if(SESSION::get('verifyCode')==$code){
                if($pwd!=''&&$rePwd!=''&&$pwd==$rePwd){
                    $id=Users::where('email',$email)->value("id");
                    // dump($id);
                    // exit();
                    if($id){
                        $res=$user->save(["password"=>md5($pwd)],['id'=>$id]);
                        if($res){
                            return CURD_result(200,'修改成功',$res);
                        }
                        else{
                            return CURD_result(2004,'修改失败','');
                        }
                        SESSION::delete('verifyCode');//删除验证码 session
                    }
                    else{
                        return CURD_result(2004,'未找到邮箱，您是否注册过呢 (?_?)','');
                    }
                }
                else if($pwd==''||$reRwd==''){
                    return CURD_result(2004,'密码不为空','');
                }
                else{
                    return CURD_result(2004,'两次密码不一致，请重新输入','');
                }
        //     }
        //     else{
        //         return CURD_result(2004,'验证码不正确','');
        //     }
        // }
        // else{
        //     return CURD_result(2004,'请先获取验证码','');
        // }

    }

//     public function gettest(){
//         print_r(Session::get('verifycode'));
//     }

    public function countRegister(){
        $dd=db('users')->whereTime('createtime', 'w')->count();
        return CURD_result(200,'统计上本周注册结果',$dd);
    }
    public function email(){
        $request = Request::instance();
        $data=$request->param();
        $email=$data['email'];
        if($email==''){
            return CURD_result(2004,'邮箱不为空','');
        }
        else if(filter_var($email, FILTER_VALIDATE_EMAIL)===false){
            return CURD_result(2004,'邮箱格式不正确','');
        }
        else{
            // $exist=Users::get(['email'=>$email]);
            // if($exist){
                if(Session::has('verifycode')){
                    Session::delete('verifycode');
                }
                $verifycode=rand(100000,999999);             
                Session::set('verifycode', strval($verifycode));      // 存储邮件的session

                // dump(Session::get('verifycode'));

                $setAddress=$email;
                $subject="蜗居社区验证码——邮箱验证：";
                $body="本次验证码为：".$verifycode."，请您及时查收";
                $AltBody="验证有效时间是24分钟";

                return sendEmail($setAddress,$subject,$body,$AltBody);
            // }
            // else{
            //  return CURD_result(2004,'未找到邮箱，您是否注册过呢 (?_?)','');
            // }
        }
    }
}
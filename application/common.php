<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\Session ;
use think\Request;
use think\Db;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use app\index\model\Goods;
use app\index\model\Order;
use app\index\model\Users;
use app\index\model\Manager;
use app\index\model\Slider;
use app\index\model\Classify;

/*配置图片域名       <<<<====<<<<        */           
function picDomain(){
	$ip='http://127.0.0.1/wochuang/public';
	return $ip;
}
/* 邮件 */
function sendEmail($setAddress,$subject,$body,$AltBody){
	$mail = new PHPMailer(true);
	 try {
     /*____________邮箱配置信息_____________*/
        $host='smtp.qq.com';
        $userName='949776404@qq.com';
        $pwd='ydhiliukqotlbefi';
        $setFrom="949776404@qq.com";
        $addReplyTo="949776404@qq.com";
        $who="青";
    /*_______________________________________*/
	    // 服务器设置
	    $mail->SMTPDebug = false;                                     // 开启Debug
	    $mail->isSMTP();                                          // 使用SMTP
	    $mail->Host = $host;  //'smtp.mxhichina.com';                      // 服务器地址
	    $mail->SMTPAuth = true;                                   // 开启SMTP验证
	    $mail->Username = $userName;                              // SMTP 用户名（你要使用的邮件发送账号）
	    $mail->Password =$pwd ;//'xxxxxx';                        // SMTP 密码
	    $mail->SMTPSecure = 'tls';                                // 开启TLS 可选
	    $mail->Port = 587;                                        // 端口
	    // 收件人
	    $mail->setFrom($setFrom,$who);                            // 来自
	    // $mail->addAddress('23275429@qq.com', 'George.Haung');  // 添加一个收件人
	    $mail->addAddress($setAddress);                           // 可以只传邮箱地址
	    $mail->addReplyTo($addReplyTo,$who);                      // 回复地址
	    // $mail->addCC('cc@example.com');
	    // $mail->addBCC('bcc@example.com');
	    // 附件
	    // $mail->addAttachment('/var/tmp/file.tar.gz');         // 添加附件
	    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // 可以设定名字
	    // 内容
	    $mail->isHTML(true);                                     // 设置邮件格式为HTML
	    $mail->Subject =$subject;                             //邮件标题
	    $mail->Body    =$body;                                   //邮件内容
	    // $mail->AltBody =$AltBody;                             //当邮件不支持html时备用显示，可以省略
	    $mail->send();
	    return CURD_result(200,'邮件发送成功，请您及时查收','');
	 } catch (Exception $e) {
    	return CURD_result(2004,'邮件发送失败，请您从新获取','');
	 }
}

/*二维码*/
function qrcode($url,$size=8){
    Vendor('Phpqrcode.phpqrcode');
    QRcode::png($url,false,QR_ECLEVEL_L,$size,2,false,0xFFFFFF,0x000000);
}
/*用户是否登录*/
function userIsLogin(){
	$flag=true;
	if(SESSION::has('userdata')){
		return $flag;
	}
	else{
		$flag=false;
		return $flag;
	}
}
/*管理员是否登录*/
function managerIsLogin(){
	$flag=true;
	if(SESSION::has('managerdata')){
		return $flag;
	}
	else{
		$flag=false;
		return $flag;
	}
}

/* CURD 操作返回前台信息 */
function CURD_result($code,$msg,$data){
	$arr= [
		'code'=>$code,
		'msg'=>$msg,
		'data'=>$data
		];
	return json_encode($arr);
}
/* CURD 返回信息操作(此项用于返回数据总记录数,) */
function CURD_result_for_acount($code,$msg,$account,$data){
	$arr= [
		'code'=>$code,
		'msg'=>$msg,
		'acount'=>$account,
		'data'=>$data
		];
	return  json_encode($arr);
}
/*文件删除*/
function delFiles($str,$table){
	/* 1.上传图片的时候
	`	  (1) 修改数据的时候,删除图片
		  (2) 其他情况,直接删除图片
	   2. 当删除数据的时候           */
	if($str=='deleteUploadImg'){
		$request = Request::instance();
		$data=$request->param();
		/*如果是更新,把数据库的图片数组重新处理*/
		if(isset($data['id'])&&isset($data['imgUrl'])){
			if($table=='goods'){
				$good=new Goods();
				$goodList=$good->where('id',$data['id'])->find();
				$images=$goodList->images;  //json格式的数据
				$images=json_decode($images);
				$domain=picDomain();       //picDomain()详见第27行
				foreach ($images as $key => $value) {
					if($data['imgUrl']==$value){
						array_splice($images,$key,1);				
						$arr=explode($domain,$value);  
						array_shift($arr);     //删除第一个空元素
						if (file_exists(ROOT .$arr[0])) { 
							$value=ROOT .$arr[0];
							$falg=unlink($value);
						}
					}
				}
				// if(json_encode($images)=='[]'){   //当$images为空时
				// 	$images=null;
				// 	$goodList=$good->where('id',$data['id'])->update(['images'=>$images]);
				// }else{
					$goodList=$good->where('id',$data['id'])->update(['images'=>json_encode($images)]);
				// }
				if ($goodList) { 
		    		return CURD_result(200,'图片删除成功','');
				} else { 
		    		return CURD_result(2004,'图片不存在',''); 
				}
			}
			else if($table=='slider'||$table=='classify'){
				$List=null;
				if($table=='slider'){
					$slider=new slider();
					$List=$slider->where('id',$data['id'])/*->order('createtime desc')*/->find();
				}
				if($table=='classify'){
					$classify=new Classify();
					$List=$classify->where('id',$data['id'])/*->order('createtime desc')*/->find();
				}
				$images=$List->image;        //json格式的数据

				$domain=picDomain();         //picDomain()详见第27行

				$arr=explode($domain,$images);  
				array_shift($arr);           //删除第一个元素
				if (file_exists(ROOT .$arr[0])) { 
					$img=ROOT .$arr[0];
					$falg=unlink($img);
				} 
				$images='';	
				if($table=='slider'){
					$slider->where('id',$data['id'])->update(['image'=>$images]);
				}
				else if($table=='classify'){
					$classify->where('id',$data['id'])->update(['image'=>$images]);
				}
			}
		}
		else if(isset($data['imgUrl'])){
			/*  1.删除文件   */
			$img=$data['imgUrl'];
			$domain=picDomain();       //picDomain()详见第27行
			$arr=explode($domain,$img);  
			if (file_exists(ROOT .$arr[1])) { 
				$img=ROOT .$arr[1];
				$falg=unlink($img);
	    		return CURD_result(200,'图片删除成功','');
			} else { 
	    		return CURD_result(2004,'图片不存在',''); 
			}
		}
		else{
	    	return CURD_result(2004,'请先选择图片','');
		}
	}
	else if($str=='hasRelationWithDatabase'){
		
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		$images=null;
		if($table=='goods'){
			$good=new Goods();
			$goodList=$good->where('id',$data['id'])/*->order('createtime desc')*/->find();
			$images=$goodList->images;  //json格式的数据
			$images=json_decode($images);
			// dump($images);
			$domain=picDomain();       //picDomain()详见第27行

			foreach ($images as $key => $value) {
				// dump($value);
				$arr=explode($domain,$value);  
				array_shift($arr);     //删除第一个元素
				// dump($arr);
				if (file_exists(ROOT .$arr[0])) { 
					$value=ROOT .$arr[0];
					$falg=unlink($value);
				}
			}
		}
		else if($table=='slider'||$table=='classify'){
			$List=null;
			if($table=='slider'){
				$slider=new slider();
				$List=$slider->where('id',$data['id'])/*->order('createtime desc')*/->find();
			}
			if($table=='classify'){
				$classify=new Classify();
				$List=$classify->where('id',$data['id'])/*->order('createtime desc')*/->find();
			}
			$images=$List->image;  //json格式的数据
			if($images!=''){
				$domain=picDomain();         //picDomain()详见第27行
				$arr=explode($domain,$images);
				array_shift($arr);           //删除第一个元素
				if (file_exists(ROOT .$arr[0])) {
					$img=ROOT .$arr[0];
					$falg=unlink($img);
				}
			}
		}
	}
}

/* 图片上传处理   获取ajax上传文件*/
function uploadImg($imgType){/*  $imgType：每张表都有些图片上传，自定义图片目录能更好管理， */
	$picDomain=picDomain();
    $file = $_FILES['imgFile'];
	$dir="";
    if(isset($file))
	{
		$ret = array();
		$uploadDir = date("Ymd").'/';
		$dir = ROOT .'/uploads/images/'.$imgType.'/'.$uploadDir;
		file_exists($dir) || (mkdir($dir,0777,true) && chmod($dir,0777));

		if(!is_array($file["name"])) //single file
		{
			$fileName = time().uniqid().'.'.pathinfo($file["name"])['extension'];
			move_uploaded_file($file["tmp_name"],$dir.$fileName);
			$ret['file'] = DS.$uploadDir.$fileName;
		}
		// echo json_encode($ret);
		/*返回路径*/
		return $picDomain.'/uploads/images/'.$imgType.'/'.$uploadDir.$fileName;
//		echo  $picDomain.'/uploads/images/'.$imgType.'/'.$uploadDir.$fileName;
		// dump($picDomain.'/uploads/images/'.$imgType.'/'.$uploadDir.$fileName);
	}
	// return $dir;
}

/* 图片上传处理  获取表单上传文件 */ 
/*	public function formupload(){

	    // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('image');

	    dump($file);
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	    if($info){
	        // 成功上传后 获取上传信息
	        // 输出 jpg
	        echo $info->getExtension();
	        // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	        echo $info->getSaveName();
	        // 输出 42a79759f284b767dfcb2a0197904287.jpg
	        echo $info->getFilename(); 
	    }else{
	        // 上传失败获取错误信息
	        echo $file->getError();
	    }
	}*/

function object2array($object) {
    $object =  json_decode( json_encode( $object),true);
    return  $object;
}
/*slider 查询*/
function getsliderByConditions($con,$data){

	$slider=new Slider();
	$sliderList='';
	if($con=='首页顶部幻灯片'){
		$sliderList=$slider->limit(5)->where('type','首页顶部幻灯片')->select();  
	}
	else if($con=='首页中部幻灯片'){
		$sliderList=$slider->limit(1)->where('type','首页中部幻灯片')->find(); 
	}
	else if($con=='首页底部幻灯片'){
		$sliderList=$slider->limit(1)->where('type','首页底部幻灯片')->find(); 
	}

	if($sliderList){
    	return CURD_result(200,'success',$sliderList);
	}
	else{
    	return CURD_result(2004,'未找到数据',$sliderList);
	}
}

/*商品查询*/
function getGoodsByConditions($con,$data){
	$good=new Goods();
	$goodList='';

	$request = Request::instance();
	$parameter=$request->param();

	if($con=="defaultNum"){   //getGoods()首页默认显示8条
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('produce','true')->where('status','true')->limit(((int)$parameter['currentPage']-1)*(int)$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}else{
			$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('produce','true')->where('status','true')->limit(8)->order('createtime desc')->select();
		}
	}
	else if($con=="ById"){
		$goodList=$good->where('id',$data)->order('createtime desc')->find();    //find 的结果集是对象
		// $goodList->attr=json_decode($goodList->attr);
		// dump($goodList);
	}
	else if($con=="ByAttr"){
		$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('id',$data)->order('createtime desc')->find();    //find 的结果集是对象
	}
	else if($con=="StatusUp"){  //上架
		$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('status',$data)->order('createtime desc')->select();
	}
	else if($con=="StatusDown"){ //下架
		$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('status',$data)->order('createtime desc')->select();
	}
	else if($con=="all"){ //全部  （分页）
		$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
	}
	else if($con=="ByClass"){ //分类
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('cid',$data)->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}else{
			$goodList=$good->field('id,name,introduce,price,attr,number,images,createtime,status,cid,produce')->where('cid',$data)->order('createtime desc')->select();
		}
	}
	else if($con=="ByIdOnlyFindProduce"){ //推荐
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$goodList=$good->where('id',$data)->where('produce','true')->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}else{
			$goodList=$good->where('id',$data)->where('produce','true')->order('createtime desc')->select();
		}
	}
	else if($con=="getGoodsByKeyWord"){   //关键字
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$goodList=$good->where('name','like',$data)->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}else{
			$goodList=$good->where('name','like',$data)->order('createtime desc')->select();
		}
	}
	

	if($goodList){
		$j=0;
		foreach($goodList as $i){      //针对select 返回来的array
			$goodList[$j]["attr"]  =json_decode($i['attr']);
			$goodList[$j]["number"]=json_decode($i['number']);
			if ($con=="defaultNum") {
				$goodList[$j]["images"]=json_decode($i['images'])[0];
			}else{
				$goodList[$j]["images"]=json_decode($i['images']);
			}
			$j++;
		}
		if(is_object($goodList)){      //针对find 返回来的object
			$goodList->attr  =json_decode($goodList->attr);
			$goodList->images=json_decode($goodList->images);
			$goodList->number=json_decode($goodList->number);
		}

		if(isset($parameter['currentPage'])==1)
		{
			$acount=$good->count();
			return CURD_result_for_acount(200,'yes',$acount,$goodList);
		}
		// if($con=="ById")
		// {
		// 	$acount=$good->count();
		// 	return CURD_result_for_acount(200,'yes',$acount,$goodList);
		// }
		else{
    		return CURD_result(200,'yes',$goodList);
		}
	}
	else{
    	return CURD_result(2004,'未找到数据',$goodList);
	}
}

/*订单查询*/
function getOrderByConditions($con,$data){
	$order=new Order();
	$orderList='';
	$request = Request::instance();
	$parameter=$request->param();
	$table=Db::table('think_order');
	// $table=Db::
	// 		view('think_order','id,order_sn,order_status,pay_status,attr,pay_type,createtime,good_price,good_number,order_total,address_id,good_id,user_id,attr,expressName,expressNumber')->
	// 		view('think_address','name userName,phone,address,province,city,area,street','think_address.id=think_order.address_id')->
	// 		view('think_goods','name,images','think_goods.id=think_order.good_id')->
	// 		view('think_users','email','think_users.id=think_order.user_id');

	if($con=="defaultNum"){   //getOrder()    前台
		$orderList=$table->where('user_id',$parameter['user_id'])->order('createtime desc')->select();
	}
	else if($con=="all"){          //getOrder()    后台
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$orderList=$table->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order(['order_status'=>'desc','pay_status'=>'asc','createtime'=>'desc'])->select();
		}
		else{
			$orderList=$table->order('createtime desc')->limit(0,20)->select();
		}
	}
	else if($con=="ById"){   //后台

		$orderList=$table->where('id',$parameter['id'])->order('createtime desc')->select();
		// $orderList=$order->where('id',$data)->find();
	}
	else if($con=="userGetById"){    //
		$orderList=$table->where('user_id',$parameter['user_id'])->where('id',$parameter['id'])->order('createtime desc')->select();
		// $orderList=$order->where('id',$data)->find();
	}
	else if($con=="weitPay"){	
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$orderList=$table->where('pay_status','待支付')->where('user_id',$parameter['user_id'])->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}
		else{
			$orderList=$table->where('pay_status','待支付')->where('user_id',$parameter['user_id'])->order('createtime desc')->select();
		}	
	}
	else if($con=="weitSend"){	
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$orderList=$table->where('order_status','待发货')->where('pay_status','已支付')->where('user_id',$parameter['user_id'])->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}
		else{
			$orderList=$table->where('order_status','待发货')->where('pay_status','已支付')->where('user_id',$parameter['user_id'])->order('createtime desc')->select();
		}	
	}
	else if($con=="Receive"){	
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
			$orderList=$table->where('order_status','已发货')->where('user_id',$parameter['user_id'])->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->order('createtime desc')->select();
		}
		else{
			$orderList=$table->where('order_status','已发货')->where('user_id',$parameter['user_id'])->order('createtime desc')->select();
		}	
	}

	if($orderList){
		$j=0;
		foreach($orderList as $i){      //针对select 返回来的array
			$orderList[$j]["attr"]  =json_decode($i['attr']);
			$orderList[$j]["images"]  =json_decode($i['images']);
			$j++;
		}
		if(is_object($orderList)){      //针对find 返回来的object
			$orderList->attr  =json_decode($orderList->attr);
			$orderList->images  =json_decode($orderList->images);
		}
		if(isset($parameter['currentPage'])==1)
		{
			$acount=$order->count();
			return CURD_result_for_acount(200,'yes',$acount,$orderList);
		}
		else{
    		return CURD_result(200,'yes',$orderList);
		}
	}
	else{
    	return CURD_result(2004,'未找到数据',$orderList);
	}
}
/*判断订单是否付款*/


/*删除信息*/
function del($delObj){		
	// if(Session::has("managerdata")){
		$request = Request::instance();
		$data=$request->param();
		$id=$data['id'];
		if($delObj=='delUser'){
			$user=new Users();
			$user = $user->find($id);
			if($user)
			{
				$userId=$user->delete();
				return CURD_result(200,'删除成功',$userId);
			} 
			else{
				return CURD_result(2004,'未找到该用户，请刷新数据试试','');
			}
		}
		if($delObj=='delManager'){
			$manager=new Manager();
			$manager = $manager->find($id);
			if($manager)
			{
				if($manager['username']=='admin'){
					return CURD_result(2004,'超级管理员不能删除！','');
				}else{
					$managerId=$manager->delete();
					if($managerId){
						return CURD_result(200,'删除成功',$managerId);
					}
					else{
						return CURD_result(2004,'删除失败','');
					}
				}
			} 
			else{
				return CURD_result(2004,'未找到该用户，请刷新数据','');
			}
		}
	// }
	// else{
	// 	return CURD_result(2004,'请先登录','');
	// }
}

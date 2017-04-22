<?php
namespace app\index\controller;
use think\Session ;
use think\Request;
use think\Db;

use app\index\model\Order;
use app\index\model\Goods;
use app\index\model\Address;
class Ordercontroller
{
	// public function index(){
	// 	return view();
	// }

	/*查询订单列表   前台*/
    // public function getOrder()
    // {
    // 	return getOrderByConditions('defaultNum','');
    // }
    	/*查询订单列表 后台*/
    public function getMSOrderList()
    {    	
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	return getOrderByConditions('all','');
    }
   /*订单详情(后台)*/ 
    public function getOrderById()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		return getOrderByConditions('ById',''/*$id*/);
    }
   /*订单详情*/ 
    public function userGetOrderById()
    {
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		return getOrderByConditions('userGetById','');
    }

	/*待支付*/  
	public function weitPay(){ 
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		return getOrderByConditions('weitPay','');
	}  
    /*待发货*/
    public function weitSend(){  
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// } 
		return getOrderByConditions('weitSend','');
    }
    /*已发货*/
    public function Receive(){
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		return getOrderByConditions('Receive','');
    }

    /*添加*/
    public function addOrder()
    {
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$order=new Order();
		$goods=new Goods();
		$address=new Address();

    	$request = Request::instance();
		$data=$request->param();

/*		$data = array(
		  	"user_id" => "143",
		  	"good_price" => "77",
		  	"good_number" => "1",
		  	"attr" => array(
		    	"颜色" => "黑色",
		    	"尺寸" => "37"
		  	),
		  	"order_total" => "77",
		  	"address_id" => "132",
		  	"good_id" => "186"
		);
		$user_id=$data["user_id"];*/
		$find_good='';
		$data2=array();
		if(isset($data["user_id"])){
			$data2['user_id']=$data["user_id"];
		}else{
			return CURD_result(2004,'请先登录',"");
		}
		if(isset($data["good_id"])){
			$data2['good_id']=$data["good_id"];
		}else{
			return CURD_result(2004,'商品编号不为空',"");
		}
		if(isset($data["attr"])){
			$data2['attr']=json_encode($data["attr"]);
		}else{
			return CURD_result(2004,'属性不为空O',"");
		}
		if(isset($data["address_id"])){
			$data2['address_id']=$data["address_id"];
		}else{
			return CURD_result(2004,'属性不为空O',"");
		}
		if(isset($data["good_price"])){
			$data2['good_price']=$data["good_price"];
		}else{
			return CURD_result(2004,'价格不为空O！',"");
		}
		if(isset($data["good_number"])){
			$data2['good_number']=$data["good_number"];
		}else{
			return CURD_result(2004,'价格不为空O！',"");
		}
		if(isset($data["order_total"])){
			if(isset($data["getSaleById"])){
				// $find_good=$sale->where('id',$data2['good_id'])->find();
				$find_good=db::table('think_goods')->field('name,number,images,think_sale.price,think_sale.good_id')->join('think_sale','think_sale.good_id= think_goods.id')->where('think_sale.id',$data2['good_id'])->find();
			}
			else{
				$find_good=$goods->where('id',$data2['good_id'])->find();
			}
//			$find_good=$goods->where('id',$data2['good_id'])->find();
			if(((float)$find_good['price']*(float)$data['good_number'])==(float)$data["order_total"]){
				$data2['order_total']=$data["order_total"];

				$data2['name']=$find_good['name'];
				$data2['images']=$find_good['images'];
			}
			else{
				return CURD_result(2004,'操了,系统炸了!总金额算不对了...',"");
			}
		}else{
			return CURD_result(2004,'总价不为空哦',"");
		}
		
		// $find_good=$goods->where('id',$data2['good_id'])->find();
		$find_address=$address->where('id',$data2['address_id'])->where('uid',$data2['user_id'])->find();
		
		if(!$find_good){
			return CURD_result(2004,'这件买完了商品哦','');
		}
		if(!$find_address){
			return CURD_result(2004,'您的收获地址不存在哦','');
		}
		// $userdata=Session::get("userdata");/*用户Id*/
		// $user_id=$userdata["id"];
		
		$good_id=$data2["good_id"];                 /*商品的信息*/

		$address_id=$data2["address_id"];           /*地址信息*/
		$data2['createtime']=date('Y-m-d H:i:s');   /*时间戳*/
		$data2["order_sn"]=date("YmdHis").rand(1000,9999);/*编号*/
		$data2["order_status"]='待发货'; 
		$data2["pay_status"]='待支付'; 
		$data2["pay_type"]=''; 

		$data2['userName']=$find_address['name'];
		$data2['phone']=$find_address['phone'];
		$data2['address']=$find_address['address'];
		$data2['province']=$find_address['province'];
		$data2['city']=$find_address['city'];
		$data2['area']=$find_address['area'];
		$data2['street']=$find_address['street'];

		if($data2["order_total"]!=''&&$good_id!=''&&$address_id!=''){
			// $good=new Goods();
			$num='';
			if(isset($data["getSaleById"])){
				$num=$goods->where("id",$find_good['good_id'])->find();
			}
			else{
				$num=$goods->where("id",$good_id)->find();
			}

			/*1. 库存判断*/
			$arr=$data['attr'];

			$attr=json_decode($num->number);  //json格式的数据
			$attr=object2array($attr);
			$bool='';
//			dump($attr);
			foreach ($attr as $key1 => $value1) {
				$num = 0;
				foreach ($arr as $key => $value) {
					$b = in_array($value, $value1['attrValues']);
					if ($b) {
						$num++;
					}
				}
				if ($num == count($arr)) {
					$bool = $value1['number']-$data2['good_number'];
					$attr[$key1]['number'] =(string)$bool;
				}
			}
			/*2. 当库存量存在*/
			if($bool>=0){
				if(isset($data["getSaleById"])){
					$goods->where("id",$find_good['good_id'])->update(["number"=>json_encode($attr)]);
				}
				else{
					$goods->where("id",$good_id)->update(["number"=>json_encode($attr)]);
				}
				$orderList=$order->save($data2);
				if($orderList){
			    	return CURD_result(200,'下单成功',$order->id); //插入成功,并返回对应id
				}
				else{
			    	return CURD_result(2004,'下单失败',"");
				}
			}
			else{
		    	return CURD_result(2004,'库存不够了哦,亲,您也可以看看本站其他商品O！',"");
			}		
		}else{
			return CURD_result(2004,'添加的信息不能为空','');
		}
    }
    /*用户取消订单*/
    /*
 	1. 待支付    待发货   》直接删除

	2. 已支付    待发货   》申请取消
 	   已支付    已发货   》申请取消
	   已支付    已收货   》申请取消
    */
    
    public function cancelOrder(){
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$order=new Order();

    	$request = Request::instance();
		$data=$request->param();
		$user_id=$data["user_id"];
		$res = $order->find(["user_id"=>$user_id,"id"=>$data['id']]);
		// dump($res['order_status']);
		if($res)
		{
			if($res['pay_status']=='待支付'&&$res['order_status']=='待发货'){
				$orderId=$order->where('id',$data["id"])->delete();
				return CURD_result(200,'订单取消成功',$orderId);
			}
			if($res['pay_status']=='已支付'&&($res['order_status']=='待发货'||$res['order_status']=='已发货'||$res['order_status']=='已收货')){
				$orderId=$order->where('id',$data["id"])->update(["order_status"=>'申请取消']);
				return CURD_result(200,'正在申请取消订单,请您放心等待',$orderId);
			}
		} 
		else{
			return CURD_result(2004,'未找到该数据，请刷新数据试试','');
		}
    }
    /*后台 修改订单状态
    待支付    待发货   》不能执行任何操作
	已支付    待发货   》已发货
	已支付    已发货   》不能执行任何操作
	已支付    已收货   》不能执行任何操作
	已支付    申请取消 》已退款/已取消
    */
    public function modifyOrderStatus(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$order=new Order();

    	$request = Request::instance();
		$data=$request->param();
		$res = $order->find(["id"=>$data['id']]);

		if($res)
		{
			// if($res['pay_status']=='待支付'&&($res['order_status']=='待发货'||$res['order_status']=='已发货'||$res['order_status']=='已收货')){
			// 	return CURD_result(2004,'当前的订单状态，您现在不能做任何修改哦。','');
			// }
			if($res['pay_status']=='已支付'&&$res['order_status']=='待发货')
			{
				if(!$data['expressNumber']||!$data['expressName']){
					return CURD_result(2004,'请您将物流信息填写完整!','');
				}

				if($data['order_status']=='已发货'){
					$orderId=$order->where('id',$data["id"])->update(["order_status"=>$data['order_status'],'expressNumber'=>$data['expressNumber'],'expressName'=>$data['expressName']]);
					return CURD_result(200,'发货成功',$orderId);
				}
				else{
					return CURD_result(2004,'您当前只能修改"发货状态"为：已发货','');
				}
			}
			else if($res['pay_status']=='已支付'&&$res['order_status']=='申请取消')
			{
				if($res['pay_status']=='已退款'&&$data['order_status']=='申请取消'){
					$orderId=$order->where('id',$data["id"])->update(["order_status"=>$data['order_status']]);
					return CURD_result(200,'订单取消成功，请您及时退款哦',$orderId);
				}
				else{
					return CURD_result(2004,'您当前只能修改"发货状态"为：已取消，只能修改"支付状态"为：已退款','');
				}
			}
			else{
				return CURD_result(2004,'当前的订单状态，您现在不能做任何修改哦。','');
			}
		}
		else{
			return CURD_result(2004,'未找到该数据，请刷新数据试试','');
		}
    }
    /*付款成功————修改支付状态*/
    public function paySuccess(){
    	//判断是否存在Session
    	// if(!userIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	$order=new Order();

    	if(isset($_POST['order_id'])&&isset($_POST['channel'])&&isset($_POST['pay_id'])){
    		$data2['id']=$_POST['order_id']; 
    		if($_POST['channel']=='alipay_qr'){
    			$data2['pay_type']='支付宝';
    		}
    		else if($_POST['channel']=='wx_pub_qr'){
    			$data2['pay_type']='微信';
    		}
    		$data2['pay_id']=$_POST['pay_id'];
    	}
    	else{
    		return CURD_result(2004,'订单信息不为空','');
    	}
    	$res=$order->find(['id'=>$data2['id']]);

    	if($res){
    		if($res['pay_status']=='待支付'){
    	// dump($data2);
    			$res2=$order->where('id',$data2['id'])->update(["pay_status"=>'已支付','pay_type'=>$data2['pay_type'],'pay_id'=>$data2['pay_id']]);
    			if($res2){
    				return CURD_result(200,'支付成功',$res2);
    			}
    		}
    		else{
    			return CURD_result(2004,'该订单已支付','');
    		}
    	}
    	else{
    		return CURD_result(2004,'未找到该订单','');
    	}
    }
	public function countPayType(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$order=new Order();
		$arr=[];
		$countAliPay=$order->where('pay_type','支付宝')->count();
		$countWx=$order->where('pay_type','微信')->count();
		if($countAliPay){
			$arr['zhifubao']=$countAliPay;
		}
		if($countWx){
			$arr['weixin']=$countWx;
		}
//		dump($arr);
		echo  CURD_result(200,'支付方式统计结果',$arr);
	}
	public function countOrderStatu(){
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$order=new Order();
		$arr=[];
		$countOrderStatusWeit=$order->where('order_status','待发货')->count();
		$countOrderStatusHad=$order->where('order_status','已发货')->count();
		if($countOrderStatusWeit){
			$arr['weitSend']=$countOrderStatusWeit;
		}
		if($countOrderStatusHad){
			$arr['hadSend']=$countOrderStatusHad;
		}
		return  CURD_result(200,'订单状态统计结果',$arr);
	}
}

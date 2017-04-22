<?php
namespace app\index\controller;

use think\Session ;
use app\index\model\Sale;
use app\index\model\Goods;
use app\common\Common;
use think\Db;
use think\Request;
class Salecontroller
{
	public function index(){
		return view();
	}
	/*关联查询测试*/

    // public function test()
    // {
    	// $sale=Sale::all();       //    ok           //关联查询
    	// for($i=0;$i<count($sale);$i++)
    	// {
    	// 	$sale[$i]->goods;
    	// }
    	// 
    	// $sale=new Sale();             //ok
    	// $res=$sale->order("createtime desc")->select();
    	// for($i=0;$i<count($res);$i++)
    	// {
    	// 	$res[$i]->goods;
    	// }
    	// dump($res);
    	// return $res;
    // }
	/*查询*/
    public function getSale()
    {
		$sale=Db::table('think_goods')->field('think_sale.id,name,attr,images,think_sale.price,number,think_sale.good_id,think_sale.createtime')->join('think_sale','think_sale.good_id= think_goods.id')->limit(6)->order('think_sale.createtime desc')->select();  
		if($sale){
			$j=0;
			foreach($sale as $i){      //针对select 返回来的array
				$sale[$j]["attr"]  =json_decode($i['attr']);
				$sale[$j]["number"]=json_decode($i['number']);
				$sale[$j]["images"]=json_decode($i['images']);
				$j++;
			}
			if(is_object($sale)){  
				$sale->attr  =json_decode($sale->attr);
				$sale->images=json_decode($sale->images);
				$sale->number=json_decode($sale->number);
			}
	    	return CURD_result(200,'',$sale);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$sale);
		}
    }
	/*查询*/
    public function getSaleById()
    {	
    	$request = Request::instance();
		$data=$request->param();
//		dump($data);
		$sale=Db::table('think_goods')->field('think_sale.id,name,attr,images,think_sale.price,number,content,think_sale.good_id,think_sale.createtime')->join('think_sale','think_sale.good_id= think_goods.id')->where('think_sale.id',$data['id'])->order('think_sale.createtime desc')->find();
		if($sale){
			// $j=0;
			// foreach($sale as $i){      //针对select 返回来的array
			// 	$sale[$j]["attr"]  =json_decode($i['attr']);
			// 	$sale[$j]["number"]=json_decode($i['number']);
			// 	$sale[$j]["images"]=json_decode($i['images']);
			// 	$j++;
			// }
				$sale["attr"]  =json_decode($sale["attr"]);
				$sale["number"]=json_decode($sale["number"]);
				$sale["images"]=json_decode($sale["images"]); 
			
			// dump($sale);
			// if(is_object($sale)){  
			// 	$sale->attr  =json_decode($sale->attr);
			// 	$sale->images=json_decode($sale->images);
			// 	$sale->number=json_decode($sale->number);

			// }
	    	return CURD_result(200,'',$sale);
		}
		else{
	    	return CURD_result(2004,'未找到数据',$sale);
		}
    }
    public function getSaleList()
    {	
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
    	$request=Request::instance();
		$parameter=$request->param();
		$sale='';
		if(isset($parameter['currentPage'])&&isset($parameter['num'])){
    		$sale=Db::table('think_goods')->field('think_sale.id,name,attr,images,think_sale.price,number,think_sale.good_id,think_sale.createtime')->join('think_sale','think_sale.good_id= think_goods.id')->order('think_sale.createtime desc')->limit(($parameter['currentPage']-1)*$parameter['num'],$parameter['num'])->select(); 
		}
		else{
    		$sale=Db::table('think_goods')->field('think_sale.id,name,attr,images,think_sale.price,number,think_sale.good_id,think_sale.createtime')->join('think_sale','think_sale.good_id= think_goods.id')->order('think_sale.createtime desc')->limit(0,20)->select(); 
		} 

		if($sale){
			$j=0;
			foreach($sale as $i){      //针对select 返回来的array
				$sale[$j]["attr"]  =json_decode($i['attr']);
				$sale[$j]["number"]=json_decode($i['number']);
				$sale[$j]["images"]=json_decode($i['images']);
				$j++;
			}
			if(is_object($sale)){  
				$sale->attr  =json_decode($sale->attr);
				$sale->images=json_decode($sale->images);
				$sale->number=json_decode($sale->number);
			}

			if($parameter['currentPage']==1)
			{
				$acount=Db::table('think_goods')->count();
				return CURD_result_for_acount(200,'yes',$acount,$sale);
			}
			else{
	    		return CURD_result(200,'yes',$sale);
			}
		}
		else{
			return 'error';
		}
    }
    /*添加*/
    public function addSale() 
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$sale=new Sale();

		$request = Request::instance();
		$data=$request->param();
		$data2='';

		if($data['price']!=''&&$data['good_id']!=''){
			$data2['price']=$data['price'];
			$data2['good_id']=$data['good_id'];
			$data2['createtime']=date('Y-m-d H:i:s');


			$exist=Db::name('goods')->where('id',$data2['good_id'])->find();
			if($exist){
				$res=$sale->where("good_id",$data['good_id'])->select();
				// dump($res);
				if($res){
			    	return CURD_result(2004,'这件特价商品已经存在哟！',"");
				}
				else{
					$saleList=$sale->save($data2);	    
					if($saleList){ 
						// dump($sale->getLastSql());
						// 
						// dump($sale->_sql());
				    	return CURD_result(200,'插入成功',$saleList);

					}
					else{
				    	return CURD_result(2004,'操作失败',"");
					}
				}
			}
			else{
		    	return CURD_result(2004,'不存在这件商品，请您重新选择','');
			}
		}
		else{
			return CURD_result(2004,'必须输入数据哦！',"");
		}
    }
     /*修改*/
    public function modifySale()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$sale=new Sale();
		$request = Request::instance();
		$data=$request->param();

		$id=$data['id'];
		$data2=array();
		if($data['price']!=''){
		// if($data['price']!=''){
				$data2['price']=$data['price'];
			// }

			$res=$sale->where("id",$id)->update($data2);	    
			if($res){
		    	return CURD_result(200,'修改成功',$res);
			}
			else{
		    	return CURD_result(2004,'操作失败','');
			}
		}
		else{
			return CURD_result(2004,'如果修改，请输入数据在提交','');
		}

    }
    /*删除*/
    public function delSale()
    {
    	//判断是否存在Session
    	// if(!managerIsLogin()){
    	// 	return CURD_result(200,'请先登录哦！','');
    	// }
		$sale=new Sale();
		$request = Request::instance();
		$data=$request->param();

		$res=db('sale')->where('id',$data['id'])->delete();   
		if($res>1||$res==1){
	    	return CURD_result(200,'删除成功',$res);
		}
		else{
	    	return CURD_result(2004,'操作失败',$res);
		}	
	}
}
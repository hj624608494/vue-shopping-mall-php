<?php
namespace app\index\model;

class Goods extends \think\Model
{
	public  function sale(){
		return $this->hasOne('Sale','good_id');
	}

}
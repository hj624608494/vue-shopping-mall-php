<?php
namespace app\index\model;

class Sale extends \think\Model
{
	public function goods()
	{
		return $this->belongsTo('Goods','id');
	}
}

<?php

namespace Home\Model;
use Think\Model;

/**
 * 订单管理模型
 * 
 * @package     app
 * @subpackage  Home
 * @category    MODEL
 * @author      Tony<879833043@qq.com>
 *
 */ 


class OrdersModel extends Model{

	protected $fields = array(
		'orders' => array(
			'order_id',		//key
			'phone',		//key
			'campus_id',
			'together_id',
			'create_time',
			'status',
			'price',
			'order_count',
			'is_remarked',
			'food_id',
			'rank',
			'together_date',
			'admin_phone',
			'reserve_time',
			'message',
			'tag'
			)
		);

	/**
     * 模型函数
     * 取得用户购物车信息
     * @access public
     * @param  null
     * @return array(array()) 购物车数据
     */
	public function getShoppingCart(){

        $campus_id = $_SESSION['campusId'];

        if($campus_id==null) {
            $campus_id = 1;
        }

		$join  = 'food On orders.food_id = food.food_id and orders.campus_id = food.campus_id';
		$field = array(
			'orders.order_id',
			'orders.campus_id',
			'orders.order_count',
			'food.food_id',
			'food.name',
			'food.price',
			'food.discount_price',
			'food.is_discount',
			'food.message',
			'food.img_url',
            'is_full_discount'
			);
		$order = 'create_time desc';

        $where['phone'] = $_SESSION['username'];
        $where['orders.campus_id'] = $campus_id;

		$cart  = $this->join($join)
					  ->where('orders.status=0 and orders.tag=1 and food.tag=1')
                      ->where($where)
					  ->field($field)
					  ->order($order)
					  ->select();
		for ($i = 0;$i < count($cart);$i++) {
			if ($cart[$i]['is_discount'] == 0) {
				$cart[$i]['discount_price'] = $cart[$i]['price'];
			}
		}

		return $cart;
	}

	/**
     * 模型函数
     * 计算购物车所选货物的总价
     * @access public
     * @param  array(array()) 购物车数据
     * @return array() 价格状况：原价Price，折扣价dPrice，节省save
     */
	public function settleAccounts($cart,$campusId){
        $fullDiscount = 0;

		for ($i = 0;$i < count($cart);$i++) {
            if($cart[$i]['is_full_discount']) {
                $fullDiscount += $cart[$i]['dPrice'];
            }
			$price['Price']  += $cart[$i]['Price'];
			$price['dPrice'] += $cart[$i]['dPrice'];
		}

        $preferential = D('Preferential')->getPreferentialList($campusId);
        $discount['prefer_id']=0;
        foreach ($preferential as $key => $p) {
            if($fullDiscount >= $p['need_number']){
                $fullDiscountPrice = $p['discount_num'];            //优惠d数额
                $discount['prefer_id']=$p['preferential_id'];
                       //将优惠力度存到表里面
                break;
            } 
        }
       
       for ($i = 0;$i < count($cart);$i++) {
            if($cart[$i]['is_full_discount']) {
                M('orders')->where('order_id = %s',$cart[$i]['order_id'])
                           ->save($discount);
            }
       }
          
       $price['dPrice'] -= $fullDiscountPrice;
	   $price['save'] = $price['Price'] - $price['dPrice'];

		return $price;
	}

	/**
     * 模型函数
     * 点击加减按钮修改物品购买数量
     * @access public
     * @param  String $order_id 订单号
     * @param  int    $order_count 物品购买数量
     * @return boolean
     */
	public function updateOrderCount($order_id,$order_count){
		$Orders = M('orders');
		
		/*$where = array(
			'phone'    => $_SESSION['username'],
			'order_id' => $order_id, 
			);*/
		$data  = array(
			'order_count' => $order_count
			);

        //只允许代付款订单和购物车里面订单
		$res = $Orders->where('phone = %s and order_id = %s and tag=1 and (status=0 OR status=1)',array($_SESSION['username'],$order_id))
					  ->save($data);
		return $res;
	}

	/**
     * 模型函数
     * 根据大订单号获取小订单号字符串
     * @access public
     * @param  String $together_id 大订单号
     * @param  String $is_remarked 是否评价
     *                ''			无所谓
     *                isRemark 		已评价
     *                isNotRemarked 待评价
     * @return String $orderIds    订单号组成的字符串，以','分割
     */
    public function getOrderIds($together_id,$is_remarked = ''){
    	$field = array(
    		'order_id'
    	);

    	if ($is_remarked == 'isNotRemarked') {
	    	 $ordersList=M('orders')->field('order_id')->where("together_id='%s' and phone = '%s' and is_remarked=0",array($together_id,session('username')))->select();
    	}
    	else if ($is_remarked == 'isRemarked') {
    	    $ordersList=M('orders')->field('order_id')->where("together_id='%s' and phone = '%s' and is_remarked=1",array($together_id,session('username')))->select();
    	}
    	else {
             $ordersList=M('orders')->field('order_id')->where("together_id='%s' and phone = '%s'",array($together_id,session('username')))->select();
            // dump(M('orders')->getLastSql());
    	}
       
    	for ($i = 0;$i < count($ordersList);$i++) {
    		if ($i < count($ordersList)-1) {
    			$orderIds .= $ordersList[$i]['order_id'].',';
    		}
    		else {
    			$orderIds .= $ordersList[$i]['order_id'];
    		}
    	}

    	return $orderIds;
    }

	/**
     * 模型函数
     * 分割订单号字符串
     * @access public
     * @param  String $orderIds 订单号组成的字符串，以','分割
     * @return array() 订单号
     */
	public function orderIdsSplit($orderIds){
		$ordersList = explode(',', $orderIds);

		return $ordersList;
	}

	/**
     * 模型函数
     * 通过订单号获取单个物品信息
     * @access public
     * @param  String $orderId 订单号
     * @return array() 单个物品信息
     */
	public function getGoodInfo($orderId){
		$field = array(
			'food_id',
			'campus_id',
			'order_count',
			'status',
			'together_id',
			'together_date'
			);

		$where = $this->where('order_id=%s',$orderId)
					  ->field($field)
					  ->find();
		$Food     = D('Food');
		$goodInfo = $Food->getGoodInfo($where['food_id'],$where['campus_id']);


		$goodInfo['order_id']      = $orderId;
		$goodInfo['order_count']   = $where['order_count'];
		$goodInfo['status']		   = $where['status'];
		$goodInfo['together_id']   = $where['together_id'];
		$goodInfo['together_date'] = $where['together_date'];
		$goodInfo['Price']  	   = $goodInfo['price'] * $goodInfo['order_count'];
		$goodInfo['dPrice'] 	   = $goodInfo['discount_price'] * $goodInfo['order_count'];


		return $goodInfo;
	}

	/**
     * 模型函数
     * 通过订单号获取物品信息
     * @access public
     * @param  String $orderIds 订单号组成的字符串，以','分割
     * @return array(array()) 物品信息
     */
	public function getGoodsInfo($orderIds){
		$ordersList = $this->orderIdsSplit($orderIds);
		for ($i = 0;$i < count($ordersList);$i++) {
			$goodsInfo[$i] = $this->getGoodInfo($ordersList[$i]);
		}

		return $goodsInfo;
	}

    public function deleteOrders($orderIds,$phone) {
        $flag = 1;  
        $ordersList = $this->orderIdsSplit($orderIds);
        for ($i = 0;$i < count($ordersList);$i++) {
            $res = $this->deleteOrder($ordersList[$i],$phone);
            if($res===false) {
                $flag = 0;
            }
        }

        return $flag;
    }

    public function deleteOrder($orderId,$phone) {
        $res = M('orders')->where('order_id=%s and phone=%s',$orderId,$phone)
                ->delete();
        return $res;
    }

    /**
     * 模型函数
     * 获取一个用户某种状态的所有大订单号
     * @access public
     * @param  String $status 状态
     * @return array() 大订单号
     */
    public function getTogetherIds($status){
    	$field = array(
    		'together_id',
            'tag'
    		);
        $where['phone'] = $_SESSION['username'];
        $where['status'] = $status;
        $where['tag'] = '1';
        
    	$togetherIds = M('orders')->field($field)
                            ->where($where)
                            ->where('together_id is not null')
                            ->limit(8)
    						->distinct('together_id')
                            ->order('together_date desc')
    						->select();

        //dump(M('orders')->getLastSql());
    	return $togetherIds;
    }

    /**
     * 模型函数
     * 删除或取消用户大订单
     * @access public
     * @param  String  $together_id 大订单号
     * @return boolean 数据库操作结果
     */
    public function deleteOrCancel($together_id){
    	$Orders = M('orders');

    	$where = array(
    		'phone'		  => $_SESSION['username'],
    		'together_id' => $together_id
    		);
    	$data = array(
    		'tag'		  => 0
    		);

    	$res = $Orders->where($where)
    				  ->save($data);

    	return $res;
    }

    /**
     * 模型函数
     * 用户确认大订单
     * @access public
     * @param  String  $together_id 大订单号
     * @return boolean 数据库操作结果
     */
    public function confirmOrder($together_id){
    	$Orders = M('orders');

    	$where = array(
    		'phone'		  => $_SESSION['username'],
    		'together_id' => $together_id
    		);
    	$data = array(
    		'status'	  => 4
    		);

    	$res = $Orders->where($where)
    				  ->save($data);

    	return $res;
    }

    /**
     * 模型函数
     * 设置订单已评价状态
     * @access public
     * @param  String  $order_id 订单号
     * @return boolean 数据库操作结果
     */
    public function setRemarked($order_id){
        $Orders = M('orders');
        $where  = array(
            'phone'    => $_SESSION['username'],
            'order_id' => $order_id
            );
        $data   = array(
            'is_remarked' => 1
            );

        $res = $Orders->where($where)
                      ->save($data);     
        return $res;
    }

    /**
     * 模型函数
     * 判断该订单中是否全部评论，并做出相应改变
     * @access public
     * @param  String  $together_id 大订单号
     * @return int -1 数据库操作失败
     *              0 订单状态没有改变
     *              1 订单状态发生改变，订单已完成
     */
    public function setStatus($together_id){
        $field = array(
            'order_id'
            );
        $data = $this->where('phone='.$_SESSION['username'].' and '.'together_id='.'\''.$together_id.'\''.' and '.'is_remarked=0')
                     ->field($field)
                     ->select();

        if ($data !== false) {
            if (count($data) != 0) {
                return 0;
            }
            else {
                $Orders = M('orders');
                $where  = array(
                    'phone'       => $_SESSION['username'],
                    'together_id' => $together_id
                    );
                $status = array(
                    'status' => 5
                    );
                $res = $Orders->where($where)
                              ->data($status)
                              ->save();

                if ($res !== false) {
                    return 1;
                }
                else {
                    return -1;
                }
            }
        }
        else {
            return -1;
        }
    }

    /**
     * 模型函数
     * 获取订单购买数量
     * @access public
     * @param  String  $order_id    订单号
     * @return int     $order_count 订单购买数量 
     */
    public function getOrderCount($order_id){
        $field = array(
            'order_count'
            );
        $data = $this->where('order_id='.$order_id)
                     ->field($field)
                     ->find();

        return $data['order_count'];
    }

    /**
     * 模型函数
     * 立即购买操作时把购买信息写入orders表
     * @access public
     * @param  String  $food_id     食品号
     * @return int     $order_count 订单购买数量 
     */
    public function buyNowAction($food_id,$order_count){
        $Orders = M('orders');
       
        $data = array(
            'phone'       => $_SESSION['username'],
            'order_id'    => time().rand(100,999),
            'campus_id'   => $_SESSION['campusId'],
            'create_time' => date("Y-m-d H:m:s",Time()),
            'status'      => 0,
            'order_count' => $order_count,
            'is_remarked' => 0,
            'food_id'     => $food_id,
            'tag'         => 1
            );

        $res = $Orders->data($data)
                      ->add();

        if ($res !== false) {
            return $data;
        }
        else {
            return $res;
        }
    }

    /**
     * 更新订单
     * @param  [type] $togetherId  [description]
     * @param  [type] $phone       [description]
     * @param  [type] $campusId    [description]
     * @param  [type] $rank        [description]
     * @param  [type] $reserveTime [description]
     * @param  [type] $message     [description]
     * @param  [type] $totalPrice  [description]
     * @return [type]              [description]
     */
    public function updateOrder($togetherId,$phone,$rank,$reserveTime,$message,$totalPrice){
       $order=M('orders');
      
       try{
             $where['together_id']=$togetherId;
             $goods=$order
                    ->join('food on food.food_id=orders.food_id')
                    ->field('order_id,is_discount,is_full_discount,food.price,discount_price,order_count')
                    ->where($where)
                    ->select();

            $savePriceWhere['phone']=$phone;
            
            foreach($goods as $key => $good) {
                if($good['is_discount']==1){
                    $price=$good['order_count']*$good['discount_price'];  
                }else{
                    $price=$good['order_count']*$good['price'];
                }
                
                $savePriceWhere['order_id']=$good['order_id'];
                $data['price']=$price;
                $data['pay_way']=1;
                $data['reserve_time']=$reserveTime;
                $data['message']=$message;
                $data['rank']=$rank;
                $data['total_price']=$totalPrice;
                $flag=$order->where($savePriceWhere)->save($data);    //将该笔订单的小订单写进数据库
                
                //dump($order->getLastSql());
                if($flag===false){
                    break;
                }
                unset($price);
            }

            if($falg!==false){ 
               return 1;
            }else{
             
               return 0;
            }
       }catch(exception $e){
           echo $e;
           return 0;
       }
    }
    /**
     * 计算一笔订单的总价（已折扣，已优惠）
     * @param  [type] $togetherId [description]
     * @return [type]             [description]
     */
   public function calculatePriceByOrderIds($togetherId,$campusId){
       $order=M('orders');

       $where['together_id']=$togetherId;
       $goods=$order
              ->join('food on food.food_id=orders.food_id')
              ->field('order_id,is_discount,is_full_discount,food.price,discount_price,order_count')
              ->where($where)
              ->select();
      
      $discountPrice=0.0;                   //折扣之后的总价
      $fullDiscountPrice=0.0;            //满减商品的总价
      foreach($goods as $key => $good) {
          if($good['is_discount']==1){
              $price=$good['order_count']*$good['discount_price'];  
          }else{
              $price=$good['order_count']*$good['price'];
          }
    
          if($good['is_full_discount']==1){
             $fullDiscountPrice+=$price;
          }

          $discountPrice+=$price;
          unset($price);
      }
   
     $preferWhere['campus_id']=$campusId;
     $prefer=M('preferential')
             ->field('need_number,discount_num,preferential_id')
             ->where($preferWhere)
             ->order('need_number DESC')
             ->select();

         
         foreach ($prefer as $key => $p) {
            if($fullDiscountPrice>=$p['need_number']){
                $fullDiscount=$p['discount_num'];            //优惠d数额
                $discount['prefere_id']=$p['preferential_id'];
                //dump($discount);
                M('orders')->where($where)
                           ->save($discount);           //将优惠力度存到表里面
                //dump(M('orders')->getLastSql());
                break;
            } 
         }

         $totalPrice=number_format($discountPrice-$fullDiscount,1);

         return $totalPrice;
   }

   /**
    * 设置一笔订单的总订单号
    * @param [type] $orderIds [description]
    */
   public function setTogetherId($orderIds,$phone){
      $togetherId=$phone.time().rand(100,999);
       //为销订单设置订单号
      $orderIdArr=explode(',', $orderIds);
     
      $data['status']=1;
      $data['together_id']=$togetherId;
    
      $together_date = date("Y-m-d H:m:s",time());
      $data['together_date']=$together_date;
      $where['phone']=$phone;
      foreach ($orderIdArr as $key => $orderId) {   
        $where['order_id']=$orderId;
        $result=M('orders')
               ->where($where)
               ->save($data);
      }
      if($result!=false){
         return $togetherId;
      }
     
      return null;
   }


    /**
        * 模型函数
        * 为一批订单设置一个订单号，同时设置下单时间
        * 根据phone和order_id在orders表中记录together_id,together_date
        * @access public
        * @param  String $orderIds 订单号组成的字符串，以','分割
        * @return string 订单号
    */
    public function setTogether($orderIds,$phone){

       $togetherId=$phone.time().rand(100,999);

        //为销订单设置订单号
       $orderIdArr=explode(',', $orderIds);

       $data['together_id']=$togetherId;
       $together_date = date("Y-m-d H:m:s",time());
       foreach ($orderIdArr as $key => $orderId) {  
         $result=M('Orders')
                ->where('order_id=%s and phone=%s',$orderId,$phone)
                ->save($data);
       }
    
       return $togetherId;
    }

    public function getCampusStateByTogeId($together_id) {
        $where['together_id']=$together_id;
        $campus_id = M('orders')->field('campus_id')
                                ->where($where)
                                ->find();


        $where1['campus_id']=$campus_id['campus_id'];
        $state = M('campus')->field('status')
                          ->where($where1)
                          ->find();
        return $state['status'];
    }

    public function getCampusIdByRank($user,$rank) {
      $campus_id = M('receiver')->field('campus_id')
                              ->where('phone_id=%s and rank=%s',$user,$rank)
                              ->find();

      return $campus_id['campus_id'];
    }

    public function getCampusIdByTog($user,$togetherId) {
        $where['together_id'] = $togetherId;
        $where['phone_id'] = $user;
        $campus_id = M('orders')->field('campus_id')
                                ->where($where)
                                ->find();

        return $campus_id['campus_id'];
    }

    /**
     * 取消订单并退款
     * @param  [type] $togetherId [description]
     * @return [type]             [description]
     */
    public function refundOrder($togetherId){
        $where['together_id']=$togetherId;
         
        $data['status']=9;
        $flag=M('orders')->where($where)->save($data);
        return $flag;
    }
   

}

?>
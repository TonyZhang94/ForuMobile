<?php

namespace Home\Model;
use Think\Model;

/**
 * 食品评价管理模型
 * 
 * @package     app
 * @subpackage  Home
 * @category    MODEL
 * @author      Tony<879833043@qq.com>
 *
 */ 


class FoodCommentModel extends Model{

	protected $fields = array(
		'food_comment' => array(
			'food_id',		//key
			'campus_id',	//key
			'phone',		//key
			'date',			//key
			'comment',
			'grade',
			'tag',
			'is_hidden',
			'order_id'
			)
		);

	/**
     * 模型函数
     * 存储用户食品评价
     * @access public
     * @param  String $food_id   食品号
     * @param  String $comment   评论
     * @param  int    $grde      星评
     * @param  int    $is_hidden 是否匿名评论
     * @param  String $order_id  订单号
     * @return boolean 评价是否成功
     */

	public function postComment($food_id,$comment,$grade,$is_hidden,$order_id){
		$FoodComment = M('food_comment');

		$commentData = array(
			'food_id'   => $food_id,
			'campus_id' => $_SESSION['campusId'],
			'phone'     => $_SESSION['username'],
			'date'		=> date('Y-m-d H:h:s',time()),
			'comment'  	=> $comment,
			'grade'		=> $grade,
			'tag'		=> 1,
			'is_hidden' => $is_hidden,
			'order_id'	=> $order_id
			);

		$commentRes = $FoodComment->data($commentData)
							->add();

		$Orders   = D('Orders');
		$orderRes = $Orders->setRemarked($order_id);

		return $orderRes&&$orderRes;
	}

	/**
     * 模型函数
     * 存储用户食品评价
     * @access public
     * @param  String $food_id   食品号
     * @param  String $campus_id 校区号
     * @return array() 评价信息
     */
	public function getGoodComment($food_id,$campus_id){
		$field = array(
			'phone',
			'comment',
			'grade',
			'date',
			'order_id',
			'is_hidden'
			);
		$goodComment = $this->where('food_id='.$food_id.' and '.'campus_id='.$campus_id)
							->field($field)
							->select();
							
		return $goodComment;
	}

	/**
     * 模型函数
     * 计算物品平均星级评价
     * @access public
     * @param  String $commentInfo 评价信息
     * @return float  $avgGrade    平均星级评价
     */
	public function getAvgGrade($commentInfo){
		$commentCount = count($commentInfo);

		for ($i = 0;$i < $commentCount;$i++) {
			$sumGrade += $commentInfo[$i]['grade'];
		}

		$avgGrade = $sumGrade / $commentCount;

		return $avgGrade;
	}
}






?>
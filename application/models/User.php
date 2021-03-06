<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2016/9/19
 * Time: 14:07
 */
class UserModel extends BaseModel {

    //可以在这里实现单例模式的提现

    public function getUserId(){

    }
    //增加用户
    public function insertUserAccount($inputParam){
        $id = $this->db->insertInto('user',$inputParam)->execute();
        return $id;
    }
    //查询用户账户
    public function getUserAccount($inputParam){
        $res = $this->db->from('user')->select(null)->select("*")->where($inputParam)->where('deleted_at',0)->fetch();
        return $res;
    }
    //检查email是否重复
    public function checkEmail($email){
        $res = $this->db->from('user')->select('email')->where('email',$email)->where('deleted_at',0)->count();
        return $res;
    }
    //查询token值
    public function activeAccount($token){
        $res = $this->db->from('user')->select(null)->select(array('id','token_expire_time','status'))->where('token',$token)->where('deleted_at',0)->fetch();
        return $res;
    }
    //更改账户的状态
    public function changeAccountStatus($id,$param){
        $res = $this->db->update('user')->set($param)->where('id',$id)->execute();
        return $res;
    }
    //账号过期直接删号处理
    public function deleteAccount($id){
        $res = $this->db->update('user')->set('deleted_at',1)->where('id',$id)->execute();
        return $res;
    }

    //===================================  用户个人信息类 ================================
    //更新用户个人信息
    public function updatePersonInformation($user_id,$param){
        $res = $this->db->update('user')->set($param)->where('id',$user_id)->execute();
        return $res;
    }
    //获取用户信息(通用查询类$field->查询的字段(array),$param->查询的条件(array))
    public function getUserInformation($field = '*' , $param){
        $res = $this->db->from('user')->select(null)->select($field)->where($param)->where('deleted_at',0)->fetch();
        return $res;
    }
}
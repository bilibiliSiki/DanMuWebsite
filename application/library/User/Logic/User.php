<?php
/**
 * Created by PhpStorm.
 * User: gewenrui
 * Date: 2016/9/19
 * Time: 15:09
 */
//用户逻辑层
class User_Logic_User{
    //用户登录界面
    public function userLogin($email,$password){
        $userModel = new UserModel();
        //密码md5加密
        $inputParam['email']    = $email;
        $inputParam['password'] = md5($password);
        $info = $userModel->getUserAccount($inputParam);
        if(!empty($info)){
            Yaf_Session::getInstance()->set(User_Keys::getLoginUserKey(),$info['id']);
            User_Api::checkLogin();
            return true;
        }
        return false;
    }
    public function checkEmail($email = ''){
        try{
            $userObj = new UserModel();
            $info = $userObj->checkEmail($email);
            $result = array(
                "CODE"    => Base_Error::MODEL_RETURN_SUCCESS,
                "MESSAGE" => '成功!',
                "RES"     => $info
            );
            return $result;
        }catch (Exception $e){
            $log = new Base_Log();
            $log->ERROR($e->getMessage());
            $result = array(
                "CODE"    => Base_Error::MYSQL_EXECUTE_ERROR,
                "MESSAGE" => $e->getMessage(),
            );
            return $result;
        }
    }
    //增加用户账户
    public function insertAccount($email,$password){
        try{
            //过期时间为24h
            $expireTime                      = time()* 60 * 60 * 24;
            //Token
            $token                           = md5($email.$password);
            $data['email']                   = $email;
            $data['password']                = md5($password);
            $data['token_expire_time']       = $expireTime;
            $data['token']                   = $token;
            $data['created_at']              = date("Y-m-d H:i:s");
            $userModel  = new UserModel();
            $info       = $userModel->insertUserAccount($data);
            $result = array(
                'CODE'              => Base_Error::MODEL_RETURN_SUCCESS,
                'MESSAGE'           => '注册成功!',
                'TOKEN'             => $token,
                'RES'               => $info
            );
            return $result;
        }catch(Exception $e){
            $log = new Base_Log();
            $log->ERROR($e->getMessage());
            $result = array(
                "CODE"    => Base_Error::MYSQL_EXECUTE_ERROR,
                "MESSAGE" => $e->getMessage(),
            );
            return $result;
        }
    }
    //激活账户
    public function activeAccount($verify){
        try{
            $userModel = new UserModel();
            $account = $userModel->activeAccount($verify);
            $nowTime = time();
            //如果当前时间小于过期时间的话,返回true
            $flag = ($account['token_expire_time'] >= $nowTime)?ture:false;
            if($flag){
                //更改用户注册状态,用户注册成功后token,token_expire_time清空
                $data['token']              = null;
                $data['token_expire_time']  = null;
                $data['status']             = 1;
                $data['updated_at']         = date("Y-m-d H:i:s");
                $changeStatus = $userModel->changeAccountStatus($account['id'],$data);
                if($changeStatus){
                    $result = array(
                      'CODE'    => Base_Error::MYSQL_EXECUTE_SUCCESS,
                      'MESSAGE' => '注册成功,正在为您跳转登录界面'
                    );
                    return $result;
                }else{
                    $result = array(
                        'CODE'    => Base_Error::MYSQL_EXECUTE_ERROR,
                        'MESSAGE' => '注册执行错误'
                    );
                    return $result;
                }
            }else{
                //已经过了有效期,删号,
                $info = $userModel->deleteAccount($account['id']);
                if($info){
                    $result = array(
                      'CODE'      =>   Base_Error::MYSQL_EXECUTE_SUCCESS,
                      'MESSAGE'   =>   '您的账号已经删除,请重新注册!'
                    );
                     return $result;
                }else{
                    $result = array(
                        'CODE'    => Base_Error::MYSQL_EXECUTE_ERROR,
                        'MESSAGE' => '删除执行错误'
                    );
                    return $result;
                }
            }

        }catch(Exception $e){
            $log = new Base_Log();
            $log->ERROR($e->getMessage());
        }
    }
}
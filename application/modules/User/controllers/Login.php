<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2016/9/19
 * Time: 10:28
 */
class LoginController extends Base_Controller_Page {
    //验证登录
    public function init() {
        $this->setNeedLogin(false);
        parent::init();
    }

    public function indexAction(){
        $this->getView()->display('login/login.tpl');
    }

    //登录界面
    public function loginAction(){
        $email =  Base_Request::getRequest('email',null);
        $password =  Base_Request::getRequest('password',null);
        //实例化user逻辑层
        $userObj  = new User_logic_User();
        $flag = $userObj -> userLogin($email,$password);
        echo json_encode($flag);
        if($flag){
            echo "123";
        }else{
            echo "登录错误";
        }
    }
    //注销登录
    public function logoutAction(){
        $session=Yaf_Session::getInstance();
        //注销登录
        $session->del(User_Keys::getLoginUserKey());
    }
}
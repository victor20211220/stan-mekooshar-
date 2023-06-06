<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 6/4/18
 * Time: 11:13 AM
 */
require_once APPLICATION_PATH . 'library/model/invite_by_key.php';

 class Invites_Controller extends Controller_User
{
     protected $subactive = 'invites';

     public function  before() {
         parent::before();
     }

    public function index()
    {
        $result = \Model_Invite_by_key::getListFollowers(Auth::getInstance()->getIdentity()->id);

        return $result;
    }

    public function actionCreate()
    {
        $result = \Model_Invite_by_key::createKey(Auth::getInstance()->getIdentity()->id);

        return  $this->response->redirect('https://' . Request::$host . '/connections/invite/');
    }

    public function actionDestroy($invite_id)
    {
        if(Request::$isAjax) {
            $this->autoRender = false;
            $this->response->setHeader('Content-Type', 'text/json');

            \Model_Invite_by_key::destroy($invite_id);

            $removeId[] = 'h5[data-id=\'invite_key_' . $invite_id . '\']';
            $this->response->body = json_encode(array(
                'status' => true,
                'function_name' => 'removeItem',
                'data' => array(
                    'target' => $removeId
                )
            ));
            return;
        }

         $this->response->redirect(Request::generateUri('connections', 'invite') . Request::getQuery());
    }

    public function show($invite_id)
    {
        $result = \Model_Invite_by_key::getById($invite_id);

        return $result;
    }

    public function addFollower($id, $follower_id)
    {

        $result = \Model_Invite_by_key::addFollower($id, $follower_id);

        return $result;
    }

    public function getInviter($follower_id)
    {
        $result = \Model_Invite_by_key::getInviter($follower_id);

        return $result;
    }

     //**
     //get string
     //return boolean
    public function checkKey($invite_key){

        $result = \Model_Invite_by_key::existKey($invite_key);

        return $result;

    }

     public function getByKey($invite_key){

         $result = \Model_Invite_by_key::getBy('invite_key',$invite_key);

         $result = array_shift($result['data'])->getDataAtribute() ;

         return $result;
     }

     public function exist($key,$value){

        return Model_Invite_by_key::exist($key,$value);
     }

     public function addConnection($user_id, $friend_id){
         \Model_Connections::create(array(
             'user_id' => $user_id,
             'friend_id' => $friend_id,
             'message' => '',
             'typeApproved' => 1
         ));
     }

}
<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 6/4/18
 * Time: 11:40 AM
 */

class Model_Invite_by_key extends Model
{
    protected static $table = 'invite_by_key';

    public static function createKey($inviter_id){

        $result = self::create(array(
            'user_invite_id' => $inviter_id,
            'invite_key' => Text::random('distinct', 31),
         ));
        return $result;
    }

    public static function getListFollowers($inviter_id){
        $result = self::getList(array(
            'where' => "user_invite_id = $inviter_id")
        );

        return $result;
    }


    public static function getInviter($follower_id ){

        $result = self::getList(array(
                'where' => "follower_id = $follower_id")
        );

        return $result;
    }

    public static function getById($invite_id){

        $result = self::getList(array(
                'where' => "id = $invite_id")
        );

        return $result;
    }

    public static function closeKey($invite_id){
        $result = self::update(array('status' => 0,  $invite_id));

        return $result;
    }

    public static function destroy($invite_id){
        $result = self::remove($invite_id);

        return $result;
    }

    public static function addFollower($id, $follower_id){
        $result = self::update(array('follower_id' => $follower_id,'status' => 0), $id);
        return $result;
    }

    public static function existKey($invite_key)
    {
         $result = self::exists('invite_key',$invite_key);

        return $result;

    }

    public static function getBy($getBy, $value){

         $result = self::getList(array(
                'where' => "$getBy = '$value'")
        );

        return $result;
    }

    public static function exist($key,$value)
    {
        $result = self::exists($key,$value);

        return $result;

    }

}
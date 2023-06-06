
<?$config = Config::getInstance();?>

<!--block My keys-->
<div class="block-invite">
    <div class="title-big">My keys</div>
    <div class="btn-roundblue-border">
        <a href="<?=Request::generateUri('invites', 'create' ) ?>" title="View profile">Create new key</a>
    </div>
    <? foreach($followers['data'] as $follower) : ?>
        <?if(! isset($follower->follower_id)) : ?>
            <?if($follower->status == 1) : ?>
              <h5 data-id="invite_key_<?= $follower->id?>">
                  <?=   $config->protocol . '://'. $config->host . '/?key=' . $follower->invite_key ;?>
                  <div class="btn-roundblue-border">
                      <a href="<?= Request::generateUri('invites', 'destroy', $follower->id) ?>"
                         title="View profile" onclick="return box.confirm(this, true);">destroy</a>
                </div>
              </h5>
            <? endif; ?>
        <? endif; ?>
    <? endforeach; ?>
</div>

<!--block My Follovers -->
<div class="block-invite">
	<div class="title-big">My Followers</div>
    <? foreach($followers['data'] as $follower) : ?>
             <?if( Model_User::exists('id', $follower->follower_id)) :?>
               <?  $user = Model_User::getById($follower->follower_id);  ?>
                     <a href="<?=  $config->protocol . '://'. $config->host  . '/profile/' . $user->id?>/" class="userava avasize_44" title="View profile"  data-id="profile_<?= $user->id?>">
                        <? if(isset($user->avaToken[2])):?>
                            <img src="/content/ava/image/<?=$user->avaToken[0]?>/<?=$user->avaToken[1]?>/<?=$user->avaToken[2]?>/<?=$user->avaToken?>/userava_44.jpg" title="" />
                        <? else : ?>
                             <img src="/resources/images/noimage_44.jpg" title="" />
                        <? endif; ?>
                        <div class="userava-info">
                            <div>
                                <div class="userava-name"><?= $user->firstName?>  <?=  $user->lastName?> </div>
                                <div class="userava-kye"><h4>data create: <?= $follower->create_key?></h4> </div>
                            </div>
                         </div>
                     </a>
               <? endif; ?>
        <? endforeach; ?>
</div>

<!--block Invite -->

<? if(! empty( $invite = array_shift($inviter['data']) )): $invite = $invite->getDataAtribute() ?>
    <? if(! empty( Model_User::exists('id', $invite['user_invite_id'])) ): ?>

    <div class="block-invite">
        <div class="title-big">Invite</div>
         <?  $user = Model_User::getById($invite['user_invite_id']);  ?>
        <a href="<?=  $config->protocol . '://'. $config->host  . '/profile/' . $user->id?>/" class="userava avasize_44" title="View profile"  data-id="profile_<?= $user->id?>">
            <img src="/content/ava/image/<?=$user->avaToken[0]?>/<?=$user->avaToken[1]?>/<?=$user->avaToken[2]?>/<?=$user->avaToken?>/userava_44.jpg" title="" />
            <div class="userava-info">
                <div>
                    <div class="userava-name"><?= $user->firstName?>  <?=  $user->lastName?> </div>
                </div>
            </div>
        </a>
    </div>
    <? endif;?>
<? endif;?>

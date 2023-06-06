<?// dump($notification); ?>

<li class="<?= (!$notification->isView) ? 'is-new' : null ?>" data-id="notification_<?= $notification->id ?>">
	<div class="notifications-date_type">
		<?= date('m-d-Y', strtotime($notification->createDate)) ?> | <?= t('notifications_type.' . $notification->type) ?>
<!--		<a href="--><?//= Request::generateUri('notifications', 'delete', $notification->id) ?><!--" onclick="return web.removeNotifications(this);" class="icons i-closewhite" title="Delete notification"><span></span></a>-->
	</div>
	<? switch($notification->type) :
		case NOTIFICATION_TYPE_VIEWPROFILE:
		case NOTIFICATION_TYPE_NEWCONNECTION:
		case NOTIFICATION_TYPE_LIKEPOST:
		case NOTIFICATION_TYPE_COMMENTPOST:
		case NOTIFICATION_TYPE_ENDORSESKILL: ?>
			<div class="notifications-user">
				<?= View::factory('parts/userava-more', array(
					'isCustomInfo' => TRUE,
					'isTooltip' => FALSE,
					'avasize' => 'avasize_52',
					'keyId' => 'user_id',
					'ouser' => $notification
				)); ?>
			</div>
			<? break;
		case NOTIFICATION_TYPE_APPLICANTDENY:
		case NOTIFICATION_TYPE_APPLICANTAPPROVE:
			$location = array();
			$location[] = t('countries.' . $notification->notificationJobCountry);
			if($notification->notificationJobCountry == 'US') {
				$location[] = t('states.' . $notification->notificationJobState);
			} else {
				$location[] = $notification->notificationJobState;
			}
			$location[] = $notification->notificationJobCity;

			$company = (object) array(
				'id' => $notification->company_id,
				'name' => $notification->notificationCompanyName,
				'avaToken' => $notification->notificationCompanyAvaToken,
				'industry' => $notification->notificationJobIndustry
			);
			?>
			<div class="notifications-user">
				<?= View::factory('parts/companiesava-more', array(
					'company' => $company,
					'avasize' => 'avasize_52',
					'isLinkProfile' => TRUE,
					'isCompanyIndustry' => TRUE,
					'otherInfo' => '<div>' . implode(', ', $location) . '</div>'
				)); ?>
			</div>
			<? break;
	endswitch ?>

	<? switch($notification->type) :
		case NOTIFICATION_TYPE_LIKEPOST:
		case NOTIFICATION_TYPE_COMMENTPOST: ?>
			<div class="notifications-post">
				<?= $notification->notification ?>
			</div>
			<? break;
		case NOTIFICATION_TYPE_ENDORSESKILL: ?>
			<div class="notifications-skill">
				Endorsed your skill <?= $notification->notification ?>
			</div>
			<? break;
		case NOTIFICATION_TYPE_APPLICANTDENY:
		case NOTIFICATION_TYPE_APPLICANTAPPROVE:
			$text = 'Vacancy: <a href="' . Request::generateUri('jobs', 'job', $notification->job_id) . '">' . $notification->notificationJobTitle . '</a>';
			?>
			<div class="notifications-applicant">
				<?= $text ?>
			</div>
	<? endswitch; ?>
	<script type="text/javascript">
		web.notificationHideNew();
	</script>
</li>


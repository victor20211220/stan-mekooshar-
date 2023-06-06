<?// dump($users, 1); ?>
<?// dump($filters, 1); ?>


		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						Profile ID
					</th>
					<th>
						User Name
					</th>
					<th>
						Date registered
					</th>
					<th>
						User email
					</th>
					<th>
						User type
					</th>
					<th>
						Confirmed
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($users['data'] as $user): ?>
					<tr>
						<td>
							<b><?= $user->id ?></b>
						</td>
						<td>
							<a href="<?= Request::generateUri('profile', $user->id)?>" target="_blank"><?= $user->firstName . ' ' . $user->lastName ?></a>
						</td>
						<td>
							<?= date('m/d/Y H:m:i', strtotime($user->createDate)) ?>
						</td>
						<td>
							<?= $user->email ?>
						</td>
						<td>
							<? $userType = t('user_type') ?>
							<? if($user->role != 'user') : ?>
								<?= $userType[$user->role] ?>
							<? else: ?>
								<?= $userType[$user->accountType] ?>
							<? endif; ?>
						</td>
						<td>
							<? if($user->isConfirmed) : ?>
								Yes
							<? else : ?>
								No
							<? endif; ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>

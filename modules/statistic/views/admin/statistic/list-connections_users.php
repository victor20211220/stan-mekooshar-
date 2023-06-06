<?// dump($connections, 1); ?>
<?// dump($filters, 1); ?>


		<table class="w100 TableWithPadding TableWithBorder table-white" id="userList">
			<thead>
				<tr>
					<th>
						Connect ID
					</th>
					<th>
						User
					</th>
					<th>
						Connect with
					</th>
					<th>
						Date connection
					</th>
				</tr>
			</thead>
			<tbody>
				<? foreach ($connections['data'] as $connect): ?>
					<tr>
						<td>
							<b><?= $connect->id ?></b>
						</td>
						<td>
							<a href="<?= Request::generateUri('profile', $connect->user1Id)?>" target="_blank"><?= $connect->user1FirstName . ' ' . $connect->user1LastName ?></a>
						</td>
						<td>
							<a href="<?= Request::generateUri('profile', $connect->user2Id)?>" target="_blank"><?= $connect->user2FirstName . ' ' . $connect->user2LastName ?></a>
						</td>
						<td>
							<?= date('m/d/Y H:m:i', strtotime($connect->createDate)) ?>
						</td>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>

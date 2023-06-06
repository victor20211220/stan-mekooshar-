<? // dump($active, 1) ?>
<? // dump($messages_countnew, 1); ?>


<div class="messages-menupanel">
	<div class="filterpanel">
		<div class="content-title">
			My private messages
		</div>
		<ul class="filterpanel-menu">
			<li>
				<ul class="filterpanel-submenu active">

					<li>
						<a href="<?= Request::generateUri('messages', 'new') ?>"
						   class="icons i-newmessage icon-text <?= (isset($active) && $active == 'newmessage') ? 'active' : NULL ?>">
							<span></span>
							New message
						</a>

					</li>
					<li>
						<a href="<?= Request::generateUri('messages', 'index') ?>"
						   class="icons i-received icon-text <?= (isset($active) && $active == 'received') ? 'active' : NULL ?>">
							<span></span>
							Received
							<? if (isset($messages_countnew) && $messages_countnew != 0) : ?>
								<div
									class="filterpanel-counter connections-countreceived">(<span
										data-count="<?= $messages_countnew ?>"><?= $messages_countnew ?></span>)
								</div>
							<? endif; ?>
						</a>
					</li>
					<li>
						<a href="<?= Request::generateUri('messages', 'sent') ?>"
						   class="icons i-resent icon-text <?= (isset($active) && $active == 'sent') ? 'active' : NULL ?>"><span></span>Sent</a>
					</li>
					<li>
						<a href="<?= Request::generateUri('messages', 'archive') ?>"
						   class="icons i-archive icon-text <?= (isset($active) && $active == 'archive') ? 'active' : NULL ?>"><span></span>Archive</a>
					</li>
					<li>
						<a href="<?= Request::generateUri('messages', 'trash') ?>"
						   class="icons i-delete icon-text <?= (isset($active) && $active == 'trash') ? 'active' : NULL ?>"><span></span>Trash</a>
					</li>

				</ul>
			</li>
		</ul>
	</div>
</div>
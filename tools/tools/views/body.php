<?php ?>
		<div class="app docManager" id="docapp">
			<div class="tight">
				<h1>Coop Tools</h1>
			</div>


			<? if(\Publications\StaffUser::isSuperAdmin()){ ?>
				<div class="mainPanel">
					<h2>Update Topics for the Ingest XSLT</h2>
					<p>Run this as soon as you add a new topic, or anytime you change the relationship of a topic to umbrella terms.</p>
					<q-btn color="primary" @click="updateSchema">update</q-btn>
				
				</div>
			<? } ?>

			<div class="mainPanel">
				<h2>Syncronize Header/Menu</h2>
				<p>Click below to make the header and menu synchronized between WordPress and the Coop documents, read, and search pages. Do this any time after you have altered the menu in Wordpress, or change your project's name.</p>

				<q-btn color="primary" @click="syncmenu">Sync Header/Menu</q-btn>
				
			</div>

			<div class="mainPanel">
				<h2>Import Tool</h2>
				<a href="/tools/excel/index.php" target="_blank">Open Import Tool in new tab</a>
			</div><!--// .mainPanel -->


			<q-dialog v-model="statusOpen">
				<q-card>
					<q-card-section class="row items-center q-pb-none">
					<div class="text-h6">{{statusLabel}}</div>
					<q-space />
					<q-btn icon="close" flat round dense v-close-popup />
					</q-card-section>
					<q-card-section v-html="statusText"></q-card-section>
				</q-card>
			</q-dialog>
		</div>
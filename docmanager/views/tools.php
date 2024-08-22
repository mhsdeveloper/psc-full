<?php ?>

<template v-if="userLevel > 2 || userRole == 'xml_editor'">

	<? if(DM_ALLOW_UPLOAD) { ?>	
		<div class="toolsUploader">
			<template v-if="warnSolrError == false">
				<q-file filled multiple v-model="uploadChoices" label="Upload new files">
					<template v-slot:prepend>
						<q-icon name="cloud_upload" @click.stop.prevent></q-icon>
					</template>
					<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">Upload a new file to manage and publish</q-tooltip>
				</q-file>
			</template>
		</div>
	<? } ?>
	</template>

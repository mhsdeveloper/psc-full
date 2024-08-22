<?php ?>
<div class="headings rowish">
	<div class="drawerToggle"></div>
	<div class="filename">Filename</div>

	<div class="docTools">Tools</div>
	<? if(DM_ENABLE_WORKFLOW) { ?><div class="steps">Workflow</div><? } ?>
	<? if(DM_ENABLE_CHECKOUT) {?>
		<div class="checked_out">Check in/out</div>
	<? } ?>
	<? if(DM_ENABLE_EDIT) {?>
		<div class="edit">Edit</div>
	<? } ?>
	<div class="view">View</div>
	<div class="published">Publish/ unPublish</div>
</div>


<div v-for="(document, index) in documents" class="document"  v-bind:data-id="document.id" v-bind:title="''">
	<div class="rowish">
		<div class="drawerToggle" v-on:click="toggleDrawer">&nbsp;</div>

		<div class="filename" v-on:click="toggleDrawer">
			<div class="date">{{fileDate(document)}}</div>
			<h2>{{fileNoExt(document)}}</h2>
			<div class="context" v-if="document.context && document.context.length" v-html="document.context"></div>
		</div>		


		<div  class="docTools">
			<? if(is_readable(__DIR__ . "/../customize-frontend/document_tools.php")) {
				include __DIR__ . "/../customize-frontend/document_tools.php";
			} ?>
		</div>

		<? if(DM_ENABLE_WORKFLOW) {?>	
			<div class="steps">
				<template v-for="step in document.steps">
					<span class="step" v-on:click="toggleStepStatus(step)">
						<span class="status" v-if="step.status == '1'" v-bind:style="'background: ' + step.color"><q-icon name="done" style="color: #fff; font-size: 1.3rem"></q-icon></span>
						<span class="status" v-if="!step.status || step.status == '0'" v-bind:style="'opacity: .35; background: ' + step.color"></span>
						{{step.short_name}}
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
							<b>Last changed by {{step.username}}</b><br/>
							<b>{{step.name}}</b><br/>
							{{step.description}}
						</q-tooltip>
					</span><span class="stepArrow"> </span>
				</template>
			</div>
		<? } ?>


		<? if(DM_ENABLE_CHECKOUT) {?>
			<div v-if="userLevel > 3" class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>
						<q-btn size="sm" unelevated label="Checkin" color="negative" v-on:click="checkin(document)">
							<q-tooltip>
								Click to check-in<br/>
								<span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span> 
							</q-tooltip>
						</q-btn>
					</span>
				</template>
				<template v-else>
					<q-btn size="sm" unelevated label="Checkout" v-on:click="checkout(document)" color="positive">
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
							Click to check-out<br/>
							last checked out/in by<br/><b>{{document.checked_outin_by}}</b> on {{document.checked_outin_date}}
						</q-tooltip>
					</q-btn>
				</template>
			</div>
			
			<div v-else-if="userLevel > 2 || userRole == 'xml_editor'" class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>
						<q-btn size="sm" v-if="username == document.checked_outin_by" unelevated label="Checkin" color="negative" v-on:click="checkin(document)">
							<q-tooltip>
								Click to check-in<br/>
								<span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span> 
							</q-tooltip>
						</q-btn>
					</span>
				</template>
				<template v-else>
					<q-btn size="sm" unelevated label="Checkout" v-on:click="checkout(document)" color="positive">
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
						Click to check-out<br/>
						last checked out/in by<br/><b>{{document.checked_outin_by}}</b> on {{document.checked_outin_date}}
						</q-tooltip>
					</q-btn>
				</template>
			</div>

			<div v-else class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>Checked out to <span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span></span>
				</template>
				<template v-else>
					<span>Last edited by {{document.checked_outin_by}}<br/>{{checkedOutInDate(document)}}</span>
				</template>
			</div>
		<? } //end DM_ENABLE_CHECKOUT ?>


		<? if(DM_ENABLE_EDIT) {?>
			<div v-if="userLevel > 3" class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>
						<q-btn size="sm" unelevated label="Release" color="negative" v-on:click="releaseDocument(document)">
							<q-tooltip>
								<span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span> 
							</q-tooltip>
						</q-btn>
					</span>
				</template>
				<template v-else>
					<q-btn size="sm" unelevated label="Edit" v-on:click="editDocument(document)" color="positive">
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
						last edited out/in by<br/><b>{{document.checked_outin_by}}</b> on {{document.checked_outin_date}}
						</q-tooltip>
					</q-btn>
				</template>
			</div>
			
			<div v-else-if="userLevel > 2 || userRole == 'xml_editor'" class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>
						<q-btn size="sm" v-if="username == document.checked_outin_by" unelevated label="Release" color="negative" v-on:click="releaseDocument(document)">
							<q-tooltip>
								<span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span> 
							</q-tooltip>
						</q-btn>
					</span>
				</template>
				<template v-else>
					<q-btn size="sm" unelevated label="Edit" v-on:click="editDocument(document)" color="positive">
						<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
						last edited out/in by<br/><b>{{document.checked_outin_by}}</b> on {{document.checked_outin_date}}
						</q-tooltip>
					</q-btn>
				</template>
			</div>

			<div v-else class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
				<template v-if="document.checked_out == 1">
					<span>Being edited by <span>{{document.checked_outin_by}}: {{checkedOutInDate(document)}}</span></span>
				</template>
				<template v-else>
					<span>Last edited by {{document.checked_outin_by}}<br/>{{checkedOutInDate(document)}}</span>
				</template>
			</div>
		<? } ?>

		<div class="view">
			<span v-if="Array.isArray(document.xmlid) && document.xmlid.length > 1"
				v-on:click="document.linksOpen = document.linksOpen == 'open' ? 'closed' : 'open'" v-bind:class="'docLinks ' + document.linksOpen">
				VIEW
				<div class="list">
					<a v-for="link in document.xmlid" v-bind:href="Env.viewURL + link">{{link}}</a>
				</div>
			</span>
			<a v-else v-bind:href="Env.viewURL + document.xmlid" target="_blank">View</a>
			<a v-bind:href="Env.viewURL + document.xmlid + '?xml=1&filename' + document.filename" target="_blank">XML</a>


		</div>

		<div class="published" v-bind:data-status="document.published == -1 ? 'loading': '' ">
			<template v-if="document.published == 1">
				<q-btn size="sm" unelevated label="Unpublish" v-on:click="unPublish(document)" color="positive" title="click to unpublish"></q-btn>
			</template>

			<template v-else>
				<q-btn size="sm" unelevated label="Publish" v-on:click="publish(document)" color="dark" title="click to publish"></q-btn>
			</template>

		</div>
	</div>




	<div class="drawer">
		<? if(is_readable(__DIR__ . "/../customize-frontend/document-drawer-template.html")) include __DIR__ . "/../customize-frontend/document-drawer-template.html";
		else { ?>
		<div class="other">
			<q-btn v-if="userLevel > 3" unevelated color="negative" label="DELETE" v-on:click="deleteFile(document)"></q-btn>
		</div>
		<? } ?>
	</div>
</div>
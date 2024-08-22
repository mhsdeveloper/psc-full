<?php ?>
		<div class="app docManager" id="docapp">
			<div class="tight row">
				<div class="col-9">
					<h1><? ECHO DM_MANAGER_TITLE?></h1>
				</div>

				<div class="col-3 tools">
					<? include("views/tools.php");?>
					<? if(is_readable(__DIR__ ."/../customize-frontend/tools.php")) include(__DIR__ . "/../customize-frontend/tools.php");?>
				</div>
			</div>


			<? // ERROR BAR ?>
			<div class="errorBar" v-if="errorMsgs.length">
				<span tabindex=0 class="closeButton" v-on:click="closeError" v-on:keydown="closeError">×</span>
				<p v-for="error in errorMsgs">{{error}}</p>
			</div>

			<div class="error" v-if="warnSolrError">Warning: the search engine is not working at this time, so the application is disabled.</div>

			<div v-else class="mainPanel">
				<? // UPLOAD TOOLS ?>
				<div class="documentPanels">

					<!-- <div id="doclistChoices" class="choices">
						<a @click="selectFileTab" @keyup.enter="selectFileTab" tabindex="0"
							v-bind:class="showFileTab ? 'selected' : ''"
						>File List</a>
					</div> -->

					<div class="limitsForm">

						<div class="limitsRow">
							<div class="formControl"><label>Limit to filename</label><input v-model="doclistFields.filename"/></div>
							<div class="formControl"><label>limit to user</label><input v-model="doclistFields.user"/></div>

							<? if(is_readable(__DIR__ ."/../customize-frontend/search-inputs.php")) include(__DIR__ . "/../customize-frontend/search-inputs.php");?>
						</div>

						<div class="limitsRow alignRight">
							<div class="formControl">
								<label>Sort by</label>
								<select v-model="doclistOrder">
									<option value="filename">Filename</option>
									<option value="filenameRev">Filename Reverse</option>
									<? if(DM_ENABLE_CHECKOUT) {?>
										<option value="checked_outin_date">last checked out/in<output>
										<option value="checked_outin_by">user checked out/in</option>
									<? } ?>
									<option value="published">Published</option>
									<option value="unpublished">Unpublished</option>
								</select>
							</div>

							<q-btn v-on:click="getDocs" label="Go" style="background: #4488ff; color: white"></q-btn>
						</div>

					</div>


					<div id="documentList" class="panel" v-bind:class="showFileTab ? 'selected' : ''">

						<? // MAIN DOCUMENTS LIST ?>
						<div class="documentList">
							<? include(__DIR__ . "/document_template.php");?>
						</div>

						
						<div v-if="docHits" class="pagination">
							<div class="q-gutter-md">
								<q-pagination
									v-model="docPage"
									:max="docPageTotal"
									direction-links
								></q-pagination>
							</div>
						</div>
					</div>

					<div id="contentSearch" class="panel" v-bind:class="showSearchTab ? 'selected' : ''">
						<input type="text" v-model="fields.text" placeholder="keywords or phrase" />
						<button value="Search" v-on:click="search">Find</button>

						<div id="searchResults">
								<div v-for="group in searchResults">
									<div v-for="doc in group.doclist.docs">
										<p v-html="doc.highlighting[0]"></p>
									</div>
								</div>
						</div>
					</div>
				</div>


			</div><!--// .mainPanel -->


			<q-dialog v-model="showCheckinDialog">
				<q-card style="min-width: 350px">
					<q-card-section class="q-pt-none">
						<h3>Checking in</h3>
						<h4>{{checkinDoc.filename}}</h4>
					</q-card-section>
					<q-card-section>
						<q-file filled bottom-slots v-model="checkinFile" label="Label" counter v-on:input="checkinFileSelected">
							<template v-slot:prepend><q-icon name="cloud_upload" @click.stop></q-icon></template>
							<template v-slot:hint>
								Select your copy of {{checkinDoc.filename}}
							</template>
						</q-file>
					</q-card-section>

					<div v-if="uploadProgress > 0">
						<q-linear-progress stripe rounded size="40px" :value="uploadProgress" color="warning" class="q-mt-sm"></q-linear-progress>
					</div>

					<!-- <q-card-section class="q-pt-none">
						<p>Please enter a short description of changes for the TEI header:</p>
						<q-input dense v-model="changeNote" autofocus @keyup.enter="prompt = false"></q-input>
					</q-card-section> -->

					<q-card-actions align="around" class="text-primary">
						<q-btn flat label="Undo checkout" v-close-popup v-on:click="uncheckout">
							<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
								File was not changed: click to check back in without uploading.
							</q-tooltip>
						</q-btn>
						<q-btn flat label="Cancel" v-on:click="showCheckinDialog = false"></q-btn>
						<q-btn flat label="Check-in" v-on:click="checkinContinue"></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>


			<q-dialog v-model="publishDialog">
				<q-card style="min-width: 350px">

					<q-card-section class="q-pt-none">
						<h3>Ready to publish?</h3>
					</q-card-section>

					<q-card-actions align="right" class="text-primary">
						<q-btn flat label="Maybe not. More work to do..." icon="sentiment_dissatisfied" v-close-popup></q-btn>
						<q-btn unelevated color="positive" label="Share with the world!" icon="sentiment_very_satisfied" v-close-popup></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>


			<q-dialog v-model="deleteDialog">
				<q-card style="min-width: 350px">

					<q-card-section class="q-pt-none">
						<h3>Are you sure you want to delete<br/><b>{{deleteFilename}}</b>?</h3>
						<p>The content from this file will no longer be found in any part of the website, including search and browse lists.</p>
					</q-card-section>

					<q-card-actions align="right" class="text-primary">
						<q-btn flat label="No. What was I thinking..." icon="back_hand" v-close-popup></q-btn>
						<q-btn unelevated color="positive" label="Delete" v-on:click="deleteContinue" icon="delete" v-close-popup></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>

			<q-dialog v-model="showResync">
				<q-card style="min-width: 350px">
					<q-card-section class="q-pt-none">
						<h3>Are you sure you want to resync all the files?</h3>
						<p>This makes the document manager's database match the actual xml files that are uploaded. All records will be deleted, and new records created to match only the XML files that have been uploaded. All checkin/checkout statuses will be reset to be checked in, and each record will be marked published or not according to the XML.</p>
						<p>After you will need to reindex all files in SOLR or else nothing will be searchable, and the docmanager list will show "undefined" dates.</p>
					</q-card-section>

					<q-card-actions align="right" class="text-primary">
						<q-btn flat label="Maybe not..." icon="back_hand" v-close-popup></q-btn>
						<q-btn unelevated color="positive" label="Resync" v-on:click="resyncAll" v-close-popup></q-btn>
					</q-card-actions>
				</q-card>
			</q-dialog>



			<q-dialog v-model="showNewFileDialog">
				<q-card style="min-width: 350px">
					<q-card-section class="q-pt-none">
						<h3>Uploading new files</h3>
					</q-card-section>

					<q-card-section>
						<div v-if="uploading">
							<q-linear-progress stripe rounded size="40px" :value="uploadProgress" color="warning" class="q-mt-sm"></q-linear-progress>
						</div>
					</q-card-section>

					<q-card-section>
						<q-card-actions align="right" class="text-primary">
							<q-btn unelevated label="Close" color="positive" v-on:click="showNewFileDialog = false"></q-btn>
						</q-card-actions>
					</q-card-section>
				</q-card>
			</q-dialog>

			<q-dialog v-model="showStatusDialog">
				<q-card style="min-width: 350px">
					<div class="statusLog" :class="statusDialogMode">
						<q-card-section class="q-pt-lg">
							<h3>{{statusTitle}}</h3>

							<q-linear-progress rounded size="20px" :value="reindexProgress" color="warning" class="q-mt-sm"></q-linear-progress>

							<div v-for="(log, index) in statusLog" class="fileReport"
								v-bind:class="'log' + log.type" v-html="log.text">
							</div>
						</q-card-section>
					</div>
				</q-card>
			</q-dialog>

			<q-dialog v-model="showContReindex">
				<q-card style="min-width: 500px">
					<q-card-section class="q-pt-none">
						<h3>A previous reindexing job was found. Continue that job?</h3>
						<q-btn unelevated color="positive" label="yes" v-on:click="continueLSReindex"></q-btn> 
						&nbsp; 
						<q-btn unelevated color="negative" label="cancel previous job" v-on:click="cancelLSReindex"></q-btn>
					</q-card-section>
				</q-card>
			</q-dialog>

			
			<? if(is_readable(__DIR__ ."/../customize-frontend/modals.html")) include(__DIR__ . "/../customize-frontend/modals.html");?>


			<div class="otherTools">
				<h2>Other tools</h2>
				<a href="#reindex" id="reindex" v-on:click="reindex" @keyup.enter="reindex">Reindex files listed above</a>
				<a href="#zipListed" id="zipListed" v-on:click="zipListed" @keyup.enter="zipListed">Download zip of files listed above</a>
				<a href="#zipAll" id="zipAll" v-on:click="zipAll" @keyup.enter="zipAll">Download zip of ALL xml files in this project</a>
				<br/>

				<div v-if="Env.role == 'administrator'">
					<a href="#reindexall" id="reindexall" @click="reindexAll" @keyup.enter="reindexAll">Reindex ALL project docs (this will take time!)</a>
					<a href="#resyncall" id="resyncAll" v-on:click="resyncConfirm" @keyup.enter="resyncConfirm">Resync docmanager to XML files</a>
				</div>

				<? if(DM_ENABLE_WORKFLOW){?>
					| <a href='<? echo PATH_ABOVE_WEBROOT;?>steps'>Edit Workflow Steps</a>
				<? } ?>
			</div>


			<div v-if="showPleaseWait" class="pleaseWait">
					<h4>Please wait...</h4>
			</div>
		</div>


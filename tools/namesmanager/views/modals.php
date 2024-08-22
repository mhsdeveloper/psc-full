<?php ?>
        <q-dialog v-model="showEditNameModal" persistent full-width full-height>
            <q-card style="min-width: 1000px;">
              <div class="row q-pa-sm">
                <div class="col-11 text-h5 q-pa-xs text-center"  v-if="isExistingName">{{this.name.name_key}} <q-btn outline size="sm" padding="xs" icon="content_copy" @click="copyNameKey(name.name_key)"></q-btn></div>
                <div v-else class="col-11 text-h5 q-pa-xs">Add a name</div>
                <div class="col-1 text-right"><q-icon size="md" name="help" @click="editHelp = true"></q-icon></div>
              </div>

              <q-separator></q-separator>

              <q-card-section>
                     <q-form ref="nameForm" greedy>
                        <div class="row" v-if="!isExistingName">
                          <div class="col-6 q-py-sm q-px-sm">
                            <q-input class=""  v-model="name.name_key" label="HUSC" @keyup="stopAutoNamekey(); checkNameKey()"></q-input>
                          </div>
                          <q-icon class="col-2" v-if="nameKeyAvailable" size="md" name="check_circle_outline" style="color: green"></q-icon>
                          <q-icon class="col-2" v-else name="report" size="md" style="color: red"></q-icon>

                          <div class="col-2 q-py-sm q-px-sm">
                            <q-btn outline color="primary" @click="suggestNameKey" label="suggest" v-if="suggestNameFlag"></q-btn>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.title" label="Title" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.family_name" @keyup="setNameKey" label="Family Name"></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.given_name" @keyup="setNameKey" label="Given Name"></q-input>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.maiden_name" label="Birth Name" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.middle_name" label="Middle Name" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.suffix" label="Suffix" hint=""></q-input>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-1 q-py-sm q-px-sm">
                            <q-checkbox v-model="name.birth_ca" label="circa" toggle-order="ft"></q-checkbox>
                          </div>
                          <div class="col-2 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.date_of_birth" data-OLD_mask="####-##-##" label="Date of Birth" hint=""></q-input>
                          </div>
                          <div class="col-1 q-py-sm q-px-sm">
                            <q-select  v-model="name.birth_era" :dense="true" :options="dateEras" emit-value map-options></q-select>
                          </div>
                          <div class="col-2">&nbsp;</div>
                          <div class="col-1 q-py-sm q-px-sm">
                            <q-checkbox v-model="name.death_ca" label="circa" toggle-order="ft"></q-checkbox>
                          </div>
                          <div class="col-2 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.date_of_death" data-OLD_mask="####-##-##" label="Date of Death" hint=""></q-input>
                          </div>
                          <div class="col-1 q-py-sm q-px-sm">
                            <q-select  v-model="name.death_era" :dense="true" :options="dateEras" emit-value map-options></q-select>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.variants" label="Variants" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.professions" label="Professions" hint=""></q-input>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.identifier" label="Identifier" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.first_mention" label="First Mention" hint=""></q-input>
                          </div>
                          <div class="col-3 q-py-sm q-px-sm">&nbsp;</div>
                          <div class="col-3 q-py-sm q-px-sm">
                            <q-input class="full-width"  v-model="name.verified" label="Verified by" hint=""></q-input>
                          </div>
                        </div>
                    </q-card-section>
                    <q-separator></q-separator>
                    <q-card-section>
						<input type="checkbox" v-model="name.visible"/>publicly searchable
                        <div class="row" v-if="isExistingName">
						<div class="pscLabel">Public Notes</div>
                          <textarea class="full-width" v-model="name.public_notes"></textarea>
                          <div class="pscLabel">Staff Notes</div>
                          <textarea class="full-width" v-model="name.staff_notes"></textarea>
                        </div>
                    </q-card-section>
                    <q-separator></q-separator>
                    <q-card-section>
                          <q-list v-if="isExistingName" bordered separator class="rounded-borders">
                          <q-item v-for="link in name.links" :key="link.id">
                            <q-item-section v-if="link.type === 'authority'">{{link.authority}} - {{link.authority_id}}</q-item-section>
                            <q-item-section v-else><b>{{link.display_title}}:</b> {{link.url}}</q-item-section>
                            <q-item-section class="col-1">
                              <q-btn label="Edit" outline dense @click="editLink(link)"></q-btn>
                            </q-item-section>
                            <q-item-section class="col-1">
                              <q-btn label="" icon="delete" dense color="negative" @click="deleteLink(link)"></q-btn>
                            </q-item-section>
                          </q-item>
                          <q-item>
                            <q-item-section>
                              <q-btn icon="add_circle_outline" label="New Link" color="green-6" @click="editLink()"></q-btn>
                            </q-item-section>
                          </q-item>
                        </q-list>

                  </template>
                <!-- </q-splitter> -->
              </q-card-section>

              <q-separator></q-separator>

              <q-card-actions align="between">
                <q-btn v-if="userRole == 'administrator'" label="Delete" flat color="negative" align="left" @click="deleteName(); showEditNameModal = false" :disabled="name.id === null"></q-btn>
                <q-btn label="Cancel" outline @click="showEditNameModal = false"></q-btn>
                <q-btn v-if="!isExistingName" label="Save & Continue" color="primary" @click="saveAndContinue"></q-btn>
                <q-btn label="Save" color="primary" @click="saveName"></q-btn>
              </q-card-actions>

              <q-inner-loading :showing="loading"></q-inner-loading>
            </q-card>
          </q-dialog>






<? // THIS IS NOT THE EDIT BOX ?>
          <q-dialog v-model="showEditLinkModal" persistent>
            <q-card style="min-width: 600px;">
              <q-card-section>
                <div class="row col-12">
                  <div class="col-4 q-py-sm q-px-sm">
                    <q-select class="full-width" v-model="link.type" :options="linkOptions" label="Type"></q-select>
                  </div>
                  <template v-if="link.type === 'authority'">
                    <div class="col-4 q-py-sm q-px-sm">
                      <q-select class="full-width" v-model="link.authority" :options="authorityOptions" label="Authority"></q-select>
                    </div>
                    <div class="col-4 q-py-sm q-px-sm" v-show="link.type === 'authority'">
                      <q-input class="full-width" v-model="link.authority_id" label="Authority ID"></q-select>
                    </div>
                  </template>
                  <template v-if="link.type === 'source'">
                    <div class="col-8 q-py-sm q-px-sm">
                      <q-input class="" v-model="link.display_title" label="Display Title"></q-input>
                      <q-input class="full-width" v-model="link.url" label="URL"></q-select>
                    </div>
                  </template>
                </div>
              </q-card-section>

              <q-separator></q-separator>

              <q-card-actions align="right">
                <q-btn label="Cancel" outline @click="showEditLinkModal = false"></q-btn>
                <q-btn label="Save" color="primary" @click="saveLink"></q-btn>
              </q-card-actions>

              <q-inner-loading :showing="loading"></q-inner-loading>

            </q-card>
          </q-dialog>





          <q-dialog v-model="showNameModal">
            <q-card style="min-width: 1000px;">
              <q-card-section>
                <div class="text-h5 q-pa-xs text-center">{{this.name.name_key}} <q-btn outline size="sm" padding="xs" icon="content_copy" @click="copyNameKey(name.name_key)"></q-btn></div>
              </q-card-section>
              
              <q-separator></q-separator>

              <q-card-section>
                  <div class="row">
                    <div class="col-1 q-pa-sm text-right pscLabel">Title:</div>
                    <div class="col-2 q-pa-sm text-left text-bold">{{name.title}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Family Name:</div>
                    <div class="col-3 q-pa-sm text-left text-bold">{{name.family_name}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Given Name:</div>
                    <div class="col-2 q-pa-sm text-left text-bold">{{name.given_name}}</div>
                  </div>
                  <div class="row">
                  </div>
                  <div class="row">
                    <div class="col-2 q-pa-sm text-right pscLabel">Birth Name:</div>
                    <div class="col-2 q-pa-sm text-left text-bold">{{name.maiden_name}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Middle Name:</div>
                    <div class="col-2 q-pa-sm text-left text-bold">{{name.middle_name}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Suffix:</div>
                    <div class="col-1 q-pa-sm text-left text-bold">{{name.suffix}}</div>
                  </div>
                  <div class="row">
                    <div class="col-2 q-pa-sm text-right pscLabel">Date of Birth:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.birth_ca ? 'ca' : '' }} {{name.date_of_birth}} {{name.birth_era == "bce" ? "BCE" : ""}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Date of Death:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.birth_ca ? 'ca' : '' }} {{name.date_of_death}} {{name.death_era == "bce" ? "BCE" : ""}}</div>
                    <div class="col-2 q-pa-sm">&nbsp;</div>
                    <div class="col-2 q-pa-sm">&nbsp;</div>
                  </div>
                  <div class="row">
                    <div class="col-2 q-pa-sm text-right pscLabel">Variants:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.variants}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Professions:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.professions}}</div>
                    <div class="col-2 q-pa-sm">&nbsp;</div>
                    <div class="col-2 q-pa-sm">&nbsp;</div>
                  </div>
                  <div class="row">
                    <div class="col-2 q-pa-sm text-right pscLabel">Identifier:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.identifier}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">First Mention:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.first_mention}}</div>
                    <div class="col-2 q-pa-sm text-right pscLabel">Verified by:</div>
                    <div class="col-2 q-pa-sm text-bold text-left">{{name.verified}}</div>

                  </div>

                  <q-separator></q-separator>

                  
                  <div class="descriptions">
                    <div class="pscLabel">Staff Notes</div>
                    <div class="descTabs">
                      <span v-bind:class="viewingNote == note.project_id ? 'selected' : ''"  v-for="(note, ix) in name.notes" v-bind:data-note-pid="note.project_id" v-on:click="viewingNote = note.project_id">{{projects[note.project_id].abbr}}</span>
                    </div>
                    <hr class="pscHR"/>
                    <div class="descNotes">
                      <div v-for="(note, ix) in name.notes" v-bind:data-note-pid="note.project_id" v-bind:class="viewingNote == note.project_id ? 'selected' : ''" >{{note.notes}}</div>
                    </div>
                  </div>



                  <div class="descriptions">
                    <div class="pscLabel">Public Notes</div>
                    <div class="descTabs">
                      <span v-bind:class="viewingDescription == desc.project_id ? 'selected' : ''" v-for="(desc, ix) in name.projectmetadata"  v-bind:data-desc-pid="desc.project_id" v-on:click="viewingDescription = desc.project_id">{{projects[desc.project_id].abbr}}</span>
                    </div>
                    <hr class="pscHR"/>
                    <div class="descNotes">
                      <div v-for="(desc, ix) in name.projectmetadata" v-bind:data-desc-pid="desc.project_id"  v-bind:class="viewingDescription == desc.project_id ? 'selected' : ''" >{{desc.notes}}</div>
                    </div>
                  </div>

                  <q-separator></q-separator>

                  <div class="row">
                    <div class="pscLabel">Source/Authority Links</div>
                  </div>

                  <q-list class="">
                    <q-item v-for="link in name.links" :key="link.id">
                      <q-item-section v-if="link.type === 'authority'">{{link.authority}} - {{link.authority_id}}</q-item-section>
                      <q-item-section v-else><a v-bind:href="link.url" target="_blank">{{link.display_title}}</a></q-item-section>
                    </q-item>
                    <q-item v-if="name.links && name.links.length === 0">
                      <q-item-section>No Links Found</q-item-section>
                    </q-item>
                  </q-list>
              </q-card-section>

              <q-separator></q-separator>

              <q-card-actions align="center">
                <q-btn v-if="userRole =='names_editor' || userLevel > 2" label="Edit" outline  class="q-ma-xs" @click="editExistingName()"></q-btn>
                <q-btn label="Close" color="primary"  class="q-ma-xs" @click="showNameModal = false"></q-btn>
              </q-card-actions>

            </q-card>
          </q-dialog>

          <q-dialog v-model="showGroupModal" persistent>
            <q-card style="min-width: 600px;">
              <q-card-section>
                <q-form ref="groupForm" greedy>
                  <div class="row col-12">
                    <div class="full-width q-py-sm q-px-sm">
                      <q-input class="full-width" v-model="group.name" label="Name" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Name is Required']"></q-input>
                    </div>
                    <!-- <div class="col-4 q-py-sm q-px-sm" v-show="group.id === null">
                      <q-select class="full-width" v-model="group.type" :options="groupTypeOptions" label="Type" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Type is Required']"></q-select>
                    </div> -->
                  </div>
                </q-form>
              </q-card-section>
              <q-separator></q-separator>
              <q-card-actions align="right">
                <q-btn label="Cancel" outline @click="showGroupModal = false"></q-btn>
                <q-btn label="Save" color="primary" @click="saveGroup"></q-btn>
              </q-card-actions>
              <q-inner-loading :showing="loading"></q-inner-loading>
            </q-card>
          </q-dialog>

          <q-dialog v-model="showAddToGroupModal" persistent>
            <q-card style="min-width: 600px;">
              <q-card-section>
                <q-form ref="addToGroupForm" greedy>
                  <div class="row col-12">
                    <q-select label="Group" outlined emit-value map-options option-value="id" option-label="name" v-model="selectedGroup" :options="groupData" label="Group" class="full-width" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Group is Required']"></q-select>
                  </div>
                </q-form>
              </q-card-section>
              <q-separator></q-separator>
              <q-card-actions align="right">
                <q-btn label="Cancel" outline @click="showAddToGroupModal = false"></q-btn>
                <q-btn label="Save" color="primary" @click="addNamesToGroup()"></q-btn>
              </q-card-actions>
              <q-inner-loading :showing="loading"></q-inner-loading>
            </q-card>
          </q-dialog>



    <q-dialog v-model="searchHelp">
      <q-card>
        <q-card-section>
          <div class="text-h6">Search Help</div>
        </q-card-section>

        <q-separator></q-separator>

        <q-card-section style="max-height: 50vh" class="scroll">

          <div class="text-h6">Terms &amp; Wildcards</div>
          <p>
            Search terms should generally be single words. Multiple words only match if the words are found in the order listed. For example, searching <b>judge common</b> in <b>staff notes</b> will match both "judge of MA court of commons" and "judge of common pleas", but not "a common judge".</p>
            <p>For the <b>name</b> field, you can use the format <b>Last, First</b> to search the last and first name fields specifically, or enter a single word to search the last, first, middle, birth, and variants fields.</p>
           <p>Use * as a wildcard for multiple characters, and _ for a single character. For example, searching first name for "cath_rine" will return "Catherine" and "Catharine". Searching "ba*ton" in last names will yields results like "Babbington", "Barton", and "Barrington". Wildcards are automatically applied to the ends of all search terms, and also to the beginning of non-names terms. For example, searching "common" in "notes" fields will match both "common" and "uncommon", but searching "anna" in a name field will not match "Hannah". For the later, search for "*anna" to match both "Anna" and "Hannah".
          </p>

            <div class="text-h6">Fields</div>
            <p>
            Use the <q-icon size="sm" name="add_circle"></q-icon> button to add fields; choose specific fields using  the pulldown menu to the right of each search term box. Adding fields will narrow your results (fields are considered with the boolean AND).
           </p>

           <p>Do not use the same field more than once; only the last instance will matter.</p>

            <p><b>NB:</b> the "any" fields does not search staff or public notes, in order to allow much faster searches of the other fields. Use the <q-icon size="sm" name="add_circle"></q-icon> button to add the staff and/or public notes search fields specifically.</p>


            <div class="text-h6">Sorting</div>
           <p>Sorting by <b>name</b> is done alphabetical by the last, first, middle, birth, suffix, and variants fields, in that order.</p>
           <p>Sorting by <b>date</b> sorts by the birth date, with BCE dates preceding CE dates.</p>

		   <div class="text-h6">Display Fields</div>
            <p>Click on <b>display fields+</b>, next to the name column label, to see the choices for which fields to display.</p>
        </q-card-section>

        <q-separator></q-separator>

        <q-card-actions align="right">
          <q-btn flat label="Done" color="primary" @click="searchHelp = false"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>



    
    <q-dialog v-model="editHelp">
      <q-card>
        <q-card-section>
          <div class="text-h6">Add/Edit Name Help</div>
        </q-card-section>

        <q-separator></q-separator>

        <q-card-section style="max-height: 50vh" class="scroll">
        <div class="text-h6">Required Fields</div>
          <p>You must have a name in one of the following fields: last name, given name, middle name, birth name, or variants.
           </p>

           <div class="text-h6">Links to Sources and Authority Records</div>
           <p> For new names, you can not add links to sources until you've first saved the name. Use the <b>save &amp; continue</b> button to keep the edit window open, after which you will be able to add links.</p>

           <div class="text-h6">HUSCs</div>
           <p>The HUSC will be automatically generated as you enter the Family and Given name fields, however if you begin typing in the HUSC field you can override this behavior. If the HUSC field is empty, or the HUSC is incorrectly formed or already in use in the database,  <q-icon class="col-2" name="report" size="md" style="color: red"></q-icon>will appear; click the <b>SUGGEST</b> button to have the database find an appropriate HUSC. You can edit the HUSC as necessary to match prior encoding or your project's particular conventions. If your HUSC is properly formed and available, a green checkmark will appear. Note, HUSCS can only be edited for new names.</p>

        </q-card-section>

        <q-separator></q-separator>

        <q-card-actions align="right">
          <q-btn flat label="Done" color="primary" @click="editHelp = false"></q-btn>
        </q-card-actions>
      </q-card>
    </q-dialog>





	<q-dialog v-model="showSetActionConfirm">
      <q-card>
        <q-card-section>
          <div v-if="setAction == 'add'" class="text-h6">Confirm Add Names to Project</div>
          <div v-else-if="setAction == 'remove'" class="text-h6">Confirm Remove Names from Project</div>
        </q-card-section>

		<q-card-section>
			<div class="huscList">
				<span class="husc" v-for="husc in setActionHuscs">{{husc}}</span>
			</div>
		</q-card-section>

        <q-card-section>
			<div v-if="setAction == 'add'">Are you sure you want to add these names to your project? This will make the names available to your users, so that, for example, the name appear as auto-complete choices when searching.</div>
			<div v-else-if="setAction == 'remove'">Are you sure you want to remove these names from your project? These names will no longer appear as auto-complete choices for your users, nor will the be available for creating groups.</div>
		</q-card-section>

        <q-card-section>
			<q-btn label="cancel" v-on:click="showSetActionConfirm = false"></q-btn>
			<q-btn style="float: right" label="YES" color="negative" v-on:click="doSetAction"></q-btn>
		</q-card-section>
	  </q-card>
	</q-dialog>


	<q-dialog v-model="showWorkingMessage">
		<q-card>
			<q-card-section>
				<div v-bind:class="'workingMessage ' + workingMessageMode" v-html="workingMessage"></div>
			</q-card-section>

			<q-card-section>
				<q-btn label="close" v-on:click="showWorkingMessage = false"></q-btn>
			</q-card-section>
		</q-card>
	</q-dialog>



	<q-dialog v-model="showNoticeMessage" seamless position="bottom">
		<div class="noticeMessageBox">
			<p>{{noticeMessage}}</p>
		</div>
	</q-dialog>

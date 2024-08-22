<?php ?>
          <q-dialog v-model="showAliasModal" persistent>
            <q-card style="min-width: 1000px;">
              <q-card-section>
                <q-form ref="aliasForm" greedy>
                  <div class="row">
                    <div class="text-h5 q-py-md q-px-md">{{modeAliasModal}} Alias</div>
                  </div>
                  <div class="row">
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.family_name" label="Family Name" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Family Name is Required']"></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.given_name" label="Given Name" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Given Name is Required']"></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.middle_name" label="Middle Name" hint=""></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.maiden_name" label="Birth Name" hint=""></q-input>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.suffix" label="Suffix" hint=""></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.title" label="Title" hint=""></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="alias.role" label="Role" hint=""></q-input>
                    </div>
                    <div class="col-3 q-py-xs q-px-sm">
                      <q-select class="full-width" XXfilled v-model="alias.type" :options="aliasTypeOptions" label="Type"></q-select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.public_notes" label="Public Notes" type="textarea" hint=""></q-input>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 q-py-xs q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.staff_notes" label="Staff Notes" type="textarea" hint=""></q-input>
                    </div>
                  </div>
                </q-form>
              </q-card-section>

              <q-separator></q-separator>

              <q-card-actions align="right">
                <q-btn label="Cancel" outline @click="showAliasModal = false"></q-btn>
                <q-btn label="Save" color="primary" @click="saveAlias"></q-btn>
              </q-card-actions>

              <q-inner-loading :showing="loading"></q-inner-loading>              
            </q-card>
          </q-dialog>


		  
          <q-dialog v-model="showSubjectModal" persistent>
            <q-card style="min-width: 1000px;">
              <q-card-section>
                <q-form ref="subjectForm" greedy>
                  <div class="row">
                    <div class="text-h5 q-py-md q-px-sm">{{modeSubjectModal}} Subject</div>
                  </div>
                  <div class="row">
                    <div class="col-3 q-py-sm q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.subject_name" label="Subject Name" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Subject Name is Required']"></q-input>
                    </div>
                    <div class="col-3 q-py-sm q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.display_name" label="Display Name" :rules="[val => (val !== null && val !== '' && val !== undefined) || 'Display Name is Required']"></q-input>
                    </div>
                    <div class="col-3 q-py-sm q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.keywords" label="Keywords" hint=""></q-input>
                    </div>
                    <div class="col-3 q-py-sm q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.loc" label="loc" hint=""></q-input>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 q-py-sm q-px-sm">
                      <q-input class="full-width" XXfilled v-model="subject.staff_notes" label="Staff Notes" type="textarea" hint=""></q-input>
                    </div>
                  </div>
                </q-form>
              </q-card-section>

              <q-separator></q-separator>

              <q-card-actions align="right">
                <q-btn label="Cancel" outline @click="showSubjectModal = false"></q-btn>
                <q-btn label="Save" color="primary" @click="saveSubject"></q-btn>
              </q-card-actions>

              <q-inner-loading :showing="loading"></q-inner-loading>              
            </q-card>
          </q-dialog>




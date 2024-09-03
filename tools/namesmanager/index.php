<?php

	//MAINTENANCE CHECK
	if(MAINTENANCE_NAMES_DB !== false){
		print "This app is currently under maintenance. ";
		if(MAINTENANCE_NAMES_DB !== true){
				print MAINTENANCE_NAMES_DB;
		}

		die();
	}

  require_once("../index_core.php");

  $projectsJson = @file_get_contents(SERVER_WWW_ROOT . "projects.json");

?>
<!DOCTYPE html>
<html>
  <head>
    <script>
        var API_URL = "<?php print API_URL;?>";
        var ALTAPI_URL = "<?php print ALTAPI_URL;?>";
		var PROJECT_ID = <?php print \MHS\Env::PROJECT_ID;?>;
        var projects = <?=$projectsJson;?>;
		var role = "<? echo \Publications\StaffUser::role();?>";
		var level = <? echo \Publications\StaffUser::level();?>;

      </script>
  <? include("views/head.php");?>
  </head>

  <body style="background-color: #E7E7E7">
    <div id="q-app">

      <q-layout view="hHh lpR fFf">
        <q-page-container>
          <div class="row">
            <div class="col-6 q-pa-md">
              <div class="row">
                <div class="text-h3 q-py-md q-px-md">Names Manager</div>
              </div>

              <div class="row">
                <q-btn v-if="userLevel > 2 || userRole =='names_editor'" class="q-ma-sm" color="primary" icon="add" label="New Name" @click="newName()"></q-btn>
              </div>
            </div>

		</div>

          <div class="row q-px-sm searchBar">
            <div class="q-px-sm col-8">
              <div class="row q-px-sm" v-for="(field, index) in searchFields">
                  <q-input class="q-pa-sm col-6" label="Search" bg-color="white" @keyup="searchboxKeys" v-model="field.value" outlined></q-input>
                  <q-select  class="q-pa-sm col-2"  label="field" v-model="field.field" :dense="true" :options="fieldOptions" emit-value map-options></q-select>
                  <template v-if="searchFields.length > 1"><q-icon v-bind:data-index="index" class="q-pa-sm" size="sm" name="remove_circle" @click="removeSearchField"></q-icon></template>
              </div>

              <div class="q-px-md col-8">
                  <q-icon size="md" name="add_circle" @click="addSearchField"></q-icon>
              </div>
            </div>

            <div class="q-pa-sm col-2 searchOptions">
                <label>Sort 
                  <select v-model="searchSort">
                    <option value="name">Name</option>
                    <option value="date">Date</option>
                    <option value="verified">Verified by</option>
                  </select>
                </label>
                <label>Rows/page
                  <select id="rowsPerPage" v-model="pagination.rowsPerPage">
                    <option :value="10">10</option>
                    <option :value="25">25</option>
                    <option :value="50">50</option>
                    <option :value="100">100</option>
                  </select>
                </label>
				<label>
					<input type="checkbox" v-model="limitProject" v-on:click="changeLimitProject"/>
					Limit to my project
				</label>
            </div>
            <div class="q-pa-sm col-2 searchGo">
              <q-btn color="primary" label="Search" class="q-ma-md" @click="search(1)"></q-btn>
              <a href="#help" v-on:click="searchHelp = true">help</a>
            </div>

          </div>



          <?php require("views/searchResults.php");?>

        <?php include("views/modals.php");?>

        </q-page-container>
        
      </q-layout>


    </div>

  	<script src="js/axios.min.js"></script>
    <script src="/lib/vue2.6.12.js"></script>
    <script src="js/quasar.umd.modern.min.js"></script>

	<script src="js/namesManager.js?v=<?=$FRONTEND_VERSION;?>"></script>
    <script src="js/namesComponents.js?v=<?=$FRONTEND_VERSION;?>"></script>
    <script src="js/namesManagerComputed.js?v=<?=$FRONTEND_VERSION;?>"></script>
    <script src="js/namesManagerMethods.js?v=<?=$FRONTEND_VERSION;?>"></script>
    <script src="js/dwitterLoader.js?v=<?=$FRONTEND_VERSION;?>"></script>

    <script>
      NamesManager.created = async function() {
        console.log(this.projects);
       
        this.loadingDW = new DwitterLoader(document.getElementById("loadingCanvas"));
        this.loadingDW.start("swirl");
        this.loadingDW.pause();
      }

      var Napp = new Vue(NamesManager);

    </script>
  </body>
</html>

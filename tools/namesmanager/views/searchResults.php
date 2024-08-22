<?php ?>

<div v-bind:class="'resultsWrapper ' + (limitProject ? 'project' : '')">
    <canvas id="loadingCanvas"  width="1920" height="1080"></canvas>
    <div v-if="searchResultsLoading" class="searching"></div>

	<div v-else class="searchResults">
		<div v-if="showFieldChoices">
			<h4>Optional display fields</h4>
			<q-checkbox left-label v-model="fields.notes" label="Notes"></q-checkbox>
			<q-checkbox left-label v-model="fields.verified" label="Verified By"></q-checkbox>
			<q-checkbox left-label v-model="fields.firstMentioned" label="First Mentioned"></q-checkbox>
		</div>

		<div class="namesList">	
			<div class="header">
			<span class="cell actions"> </span>
			<span class="cell fullName">Name <span class="tinyLink" v-on:click="chooseFields">display fields</span></span>
			<span class="cell dates">Dates</span>
			<span class="cell husc">HUSC</span>
			<span class="cell public">Public</span>
			<div v-if="fields.notes" class="cell notes">Staff Notes</div>
			<span v-if="fields.verified" class="cell verified">Verified by</span>
			<span v-if="fields.firstMentioned" class="cell firstMention">First Mention</span>
			</div>

			<div v-if="pagination.rowsNumber == 0" class="noResultsMessage"><p>No results</p></div>

			<psc-name class="name" 
			v-for="name in searchResultsData" 
			v-bind:key="name.id"
			v-bind:name="name" 
			v-bind:project="project" 
			v-bind:projects="projects"
			v-bind:fields="fields"
			v-bind:userlevel="userLevel"
			v-on:view="viewName"
			v-on:copy-name-key="copyNameKey"
			>
			</psc-name>
		</div>

		<div class="controlsBelow" v-if="pagination.rowsNumber">

			<div class="results">
				<span class="hits" v-if="pagination.rowsNumber">{{pagination.rowsNumber}}
				{{pagination.rowsNumber > 1 ? 'results' : 'result'}}
				</span>
				<span class="hits" v-else>No results</span>
			</div>

			
			<div v-if="userLevel > 2" class="checkedActions">
				For the selected names: 
				<select v-if="limitProject" v-model="setProjectAction">
					<option value="remove" selected="selected">remove from project</option>
				</select>

				<select v-else v-model="setGlobalAction">
					<option value="add" selected="selected">add to project</option>
				</select>
				
				<q-btn label="Confirm" color="amber-14" v-on:click="setActionConfirm"></q-btn>
			</div>


			<div class="pagination">
				<span class="prev" v-if="pagination.prevPage" v-on:click="search(pagination.prevPage)">prev</span>
				<span class="firstPage" v-if="pagination.page > 1" v-on:click="search(1)">1</span>
				<span v-if="pagination.page > 6">...</span>
				<template v-if="pagination.page > 2">
					<span class="page"v-for="n in (pagination.page < 6 ? pagination.page - 2 : 4)" v-on:click="search(pagination.page - ((pagination.page < 6 ? pagination.page - 2 : 4) - (n - 1)))">
					{{ pagination.page - ((pagination.page < 6 ? pagination.page - 2 : 4) - (n - 1)) }}
					</span>
				</template>
				<span class="current">{{pagination.page}}</span>
				<template v-if="pagination.page < (pagination.totalPages - 2)">
					<span class="page"v-for="n in ((pagination.totalPages - pagination.page) > 6 ? 4 : (pagination.totalPages - pagination.page) - 1)" v-on:click="search(pagination.page + n)">
					{{ pagination.page + n }}
					</span>
				</template>
				<span v-if="pagination.page < (pagination.totalPages - 6)">...</span>
				<span class="lastPage" v-if="pagination.page < pagination.totalPages" v-on:click="search(pagination.totalPages)">{{pagination.totalPages}}</span>
				<span class="next" v-if="pagination.nextPage" v-on:click="search(pagination.nextPage)">next</span>
			</div>

		</div>
    </div>

</div>  


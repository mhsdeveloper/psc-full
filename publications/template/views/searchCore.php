<?php ?>
<div  id="searchApp" class="basicGutter">
		<div class="searchbox">

			<div  v-if="mode == 'search'" class="row filterChooser">
				<label class="mainLabel">Select Criteria</label>

				<button v-if="!useKeywords" class="mainChoice"
					@click="useKeywords = true">Keyword/phrase</button>

				<button v-if="!useDates"  class="mainChoice"
					@click="addSearchParam('date', 'dateplaceholder', 'dateplaceholder');">Date Range</button>

				<button @click="showNames = !showNames; showTopics = false" class="infront mainChoice">Names</button>
				<div :class="showNames ? 'lookupName' : 'hidden'">
					<label>Find a person:</label>
					<div class="nameLookup" data-callback="addNameToSearch" data-placeholder="last name, first name"></div><br/>
					<input v-model="pendingNameRole" value="" type="radio" name="nameRole" id="asAny"><label for="asAny">any role</label><br/>
					<input v-model="pendingNameRole" value="a" type="radio" name="nameRole" id="asAuthor"><label for="asAuthor">only as author</label><br/>
					<input v-model="pendingNameRole" value="r" type="radio" name="nameRole" id="asRecipient"><label for="asRecipient">only as recipient</label><br/>
					<button @click="addSearchedName" class="add">add to search</button>

				</div>
	
				<button @click="showTopics = !showTopics; showNames = false"  class="infront mainChoice">Topics</button>
				<div :class="showTopics ? 'lookupTopic' : 'hidden'">
					<topics-lookup @add-topic="addSearchedTopic"></topics-lookup>
				</div>

			</div>

			<div class="selectedParams">

				<div v-if="selectedParams.length == 0 && !useKeywords && mode == 'search'">
					Use the buttons on the left to select criteria for your search.
				</div>

				<div :class="useKeywords && mode == 'search' ? 'inuse' : 'hidden'" class="advSearchParam">
					<label>Keyword or phrase</label>

					<input v-model="params.terms" class="searchTerms" type="text" placeholder="Wrap phrases in quotes"
						@keyup.enter="search()"
					/>

					<button class="close roundDetached" @click="params.terms = ''; useKeywords = false;">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>

				</div>

				<div v-if="errorMessage.length">{{errorMessage}}</div>

				<template v-for="param, indx of selectedParams" class="rowBelow">
					<div v-if="mode == 'search' && param.param == 'date'" class="advSearchParam dateRangeParam" ref="dates">
						<label>Date range</label>
						<div class="part">
						From 
							<input v-model="startDate.year" type="number" :min="dateYearRange.min" :max="dateYearRange.max"/>

							<select v-model="startDate.month">
								<option value="00">month</option>
								<option value="01">January</option>
								<option value="02">February</option>
								<option value="03">March</option>
								<option value="04">April</option>
								<option value="05">May</option>
								<option value="06">June</option>
								<option value="07">July</option>
								<option value="08">August</option>
								<option value="09">September</option>
								<option value="10">October</option>
								<option value="11">November</option>
								<option value="12">December</option>
							</select>

							<select v-model="startDate.day">
								<option value="00">day</option>
								<option value="01">1</option>
								<option value="02">2</option>
								<option value="03">3</option>
								<option value="04">4</option>
								<option value="05">5</option>
								<option value="06">6</option>
								<option value="07">7</option>
								<option value="08">8</option>
								<option value="09">9</option>
								<option value="10">10</option>
								<option value="11">11</option>
								<option value="12">12</option>
								<option value="13">13</option>
								<option value="14">14</option>
								<option value="15">15</option>
								<option value="16">16</option>
								<option value="17">17</option>
								<option value="18">18</option>
								<option value="19">19</option>
								<option value="20">20</option>
								<option value="21">21</option>
								<option value="22">22</option>
								<option value="23">23</option>
								<option value="24">24</option>
								<option value="25">25</option>
								<option value="26">26</option>
								<option value="27">27</option>
								<option value="28">28</option>
								<option value="29">29</option>
								<option value="30">30</option>
								<option value="31">31</option>
							</select>	
						</div>

						<div class="part">
							To
								<input v-model="endDate.year" type="number" :min="dateYearRange.min" :max="dateYearRange.max"/>

								<select v-model="endDate.month">
									<option value="99">month</option>
									<option value="01">January</option>
									<option value="02">February</option>
									<option value="03">March</option>
									<option value="04">April</option>
									<option value="05">May</option>
									<option value="06">June</option>
									<option value="07">July</option>
									<option value="08">August</option>
									<option value="09">September</option>
									<option value="10">October</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>

								<select v-model="endDate.day">
									<option value="99">day</option>
									<option value="01">1</option>
									<option value="02">2</option>
									<option value="03">3</option>
									<option value="04">4</option>
									<option value="05">5</option>
									<option value="06">6</option>
									<option value="07">7</option>
									<option value="08">8</option>
									<option value="09">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
								</select>	
						</div>

						<button class="close roundDetached" @click="removeParam(indx)">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>

					<div v-else-if="param.param == 'p'" class="advSearchParam name">
						<label v-if="param.value.includes(':a')">author</label>
						<label v-else-if="param.value.includes(':r')">recipient</label>
						<label v-else>name</label>
						<div class="part">
							{{param.displayName}}
							<button class="close" @click="removeParam(indx)">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>
					</div>

					<div v-else-if="param.param == 's'" class="advSearchParam topic">
						<label>topic</label>
						<div class="part">
							{{param.displayName}}
							<button class="close" @click="removeParam(indx)">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
								</svg>
							</button>
						</div>
					</div>

				</template><!--// .rowBelow -->
			</div><!--// .selectedParams -->
		</div>

		<div v-if="mode == 'search'" class="row go">
			<button @click="search()" :class="needToClickSearch ? 'pending' : ''" class="doSearch">
					{{ searched? 'Update' : 'Search' }}</button>
				<!-- <button @click="reset()" class="light">Reset</button> -->
		</div>

		<div v-if="searched" class="summary">
			<div class="hits">{{hitCount}} {{hitCount == 1 ? "document" : "documents"}}</div>
			<div v-if="hitCount == 0 && selectedFacets.length && (params.terms.length || useDates)" class="noHitHelp">
			For better results, try searching first on just one parameter: for example just a keyword or date range. You can then refine your results further by limiting to the facets that appear to the left of your results list.
			</div>
		</div>

		<div v-if="searched" class="tools">

			<div v-if="facets.person_keyword.length || facets.subject.length"
					class="facets inner-container">
			<h3>Refine by</h3>
				<? /* LIST THE YET UNTOUCHED FACETS */?>
				<div v-if="facets.person_keyword.length" class="facetSet">
					<h4>person mentioned</h4>
					<template v-for="facet, i of facets.person_keyword">
						<span v-if="i < 5 || facetShowAllFlags.p"
							:data-key="facet.name_key" 
							@click="refine('p', facet)" 
							@keyup.enter="refine('p', facet)"
							tabindex="0" class="clickable"
						>
							<b>{{personFacetName(facet)}}</b> <u class="count">{{facet.count}} document{{facet.count > 1 ? "s" : ""}}</u>
						</span>
						<span v-if="i == 5 && !facetShowAllFlags.p" class="showAll"
							tabindex="0"
							@click="facetShowAllFlags.p = !facetShowAllFlags.p"
							@keyup.enter="facetShowAllFlags.p = !facetShowAllFlags.p">show all</span>
					</template>
				</div>

				<div v-if="facets.subject.length" class="facetSet">
					<h4>topic mentioned</h4>
					<template v-for="facet,i  of facets.subject">
						<span v-if="i < 5 || facetShowAllFlags.s"
							@click="refine('s', facet)" 
							@keyup.enter="refine('s', facet)" 
							tabindex="0" class="clickable"
						>
							<b>{{facetName(facet.name)}}</b> <u class="count">{{facet.count}} document{{facet.count > 1 ? "s" : ""}}</u>
						</span>		
						<span v-if="i == 5 && !facetShowAllFlags.s" class="showAll"
						tabindex="0"
							@click="facetShowAllFlags.s = !facetShowAllFlags.s"
							@keyup.enter="facetShowAllFlags.s = !facetShowAllFlags.s">show all</span>
					</template>
				</div>

				<!-- <div v-if="facets.person_keyword.length == 0 && facets.subject.length == 0"
					class="facetSet">
					<p class="diminutive">There are no other facets by which to limit your results. You can removed the current limits by clicking the <span class="clickable material-symbols-outlined">cancel</span> next to each limit.</p>
				</div>

				<div class="otherTools basicGutter">
					<button @click="resetAll" @keyup.enter="resetAll" tabindex="0">reset all</button>
				</div> -->

			</div>
		</div>

		<div class="docsList inner-container">
			<div id="loadingAni" v-if="showLoading">
				<div class="sk-cube-grid">
					<div class="sk-cube-grid-col">
						<div class="sk-cube sk-cube1"></div>
						<div class="sk-cube sk-cube2"></div>
						<div class="sk-cube sk-cube3"></div>
					</div>
					<div class="sk-cube-grid-col">
						<div class="sk-cube sk-cube4"></div>
						<div class="sk-cube sk-cube5"></div>
						<div class="sk-cube sk-cube6"></div>
					</div>
					<div class="sk-cube-grid-col">
						<div class="sk-cube sk-cube7"></div>
						<div class="sk-cube sk-cube8"></div>
						<div class="sk-cube sk-cube9"></div>
					</div>
				</div>
			</div>


			<template  v-if="!showLoading">
				<a v-for="(doc, index) in documents" 
					class="documentCard"
					:href="buildLink(doc, index)"
					>
					<p v-if="projects[env.projectID].docListShowDate" class="date_when">{{coopHelpers.formatDate(doc.date_when)}}</p>
					<h3>{{doc.title[0]}}</h3>
					<p v-if="doc.highlighting[0].length" v-html="formatBody(doc.highlighting[0])"></p>
					<p v-else v-html="formatBody(doc.doc_beginning)"></p>
				</a>
			</template>
		</div>
	</div>

	<div class="inner-container">
		<div class="pagination" id="pagination"></div>
	</div>
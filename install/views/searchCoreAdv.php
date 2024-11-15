<?php ?>
	<div class="basicGutter">
		<div id="searchApp" class="advancedSearch">
			<div v-for="obj, index of queryObjects" :class="'inner-container fieldRow bool' + obj.andOr">

				<select v-if="index > 0" v-model="obj.andOr" class="andOr">
					<option value="and">and</option>
					<option value="or">or</option>
				</select>

				<div class="field" :class="obj.field">
					<label>{{ fieldLabel(obj.field) }}</label>

					<!--
						the setup for each instance of the DateAs8 component is odd:
							we pass the prop wrapped in a getter, so that it fixes the value
							at instantiation time. It binds back to the obj prop using custom events,
							the @update:date part2

					-->
					<template v-if="obj.field == 'date_when'">
						<date-as-8 :date="obj.rangeStart" :index="index" data-type="rangeStart" @update="updateDate"></date-as-8>
							TO
						<date-as-8 :date="obj.rangeEnd" :index="index" data-type="rangeEnd" @update="updateDate"></date-as-8>
					</template>

					<template v-else-if="obj.field =='person_keyword'">
						<input :name="obj.field" :value="obj.terms" type="hidden"/>
						<input :name="obj.field + '_display'" :value="prettyHuscs[obj.terms]" readonly="true" type="text"/>
					</template>

					<template v-else-if="obj.field =='recipient'">
						<input :name="obj.field" :value="obj.terms" type="hidden"/>
						<input :name="obj.field + '_display'" :value="prettyHuscs[obj.terms]" readonly="true" type="text"/>
					</template>

					<template v-else-if="obj.field =='author'">
						<input :name="obj.field" :value="obj.terms" type="hidden"/>
						<input :name="obj.field + '_display'" :value="prettyHuscs[obj.terms]" readonly="true" type="text"/>
					</template>

					<template v-else-if="obj.field == 'subject'">
						<input :name="obj.field + '_display'" :value="obj.terms" readonly="true" type="text"/>
					</template>


					<template v-else>
						<input :name="obj.field" v-model="obj.terms"  type="text" @change="needsResearch = true"
							@keyup.enter="search()"
							placeholder="keywords or phrase in quotes"
						/>
					</template>
				</div>


				<button class="close" @click="removeField(index)">
						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
						<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
						</svg>
				</button>
			</div>



			
			<div class="lastRow inner-container">
				<div v-if="queryObjects.length < 10" class="addAField">
					<button class="adder" @click="showChooseField = !showChooseField">
						<!-- <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
							<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
						</svg>  -->
						add criteria</button>

					<div v-if="1 || showChooseField">
						<button @click="addField('text_merge')">add keyword/phrase</button>
						<button @click="addField('date_when')">add date range</button>
						<button @click="showNames = !showNames; showTopics = false" class="infront mainChoice">add name</button>
						<button @click="openTopics">add topic</button>

					</div>
				</div>

				<div class="controls">
					<div class="groupingControls">
						<button class="checkCircle" @click="groupEditions = !groupEditions">
							<svg v-if="groupEditions" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
							</svg>
							</button>
						<label @click="groupEditions = !groupEditions">group by edition</label>
					</div>

					<button class="search" :class="needsResearch ? '' : 'ghosted'" @click="search">{{ needsResearch ? 'Search' : 'Search' }}</button>
				</div>

			</div>

			<div :class="showNames ? 'lookupName' : 'hidden'">
				<button class="closer" @click="showNames = !showNames"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
					<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
					</svg></button>

				<label>Find a person:</label>
				<div class="nameLookup" id="nameLookupInput" data-callback="addNameToSearch" data-placeholder="last name, first name"></div><br/>
				<input v-model="pendingNameField" value="person_keyword" type="radio" name="nameRole" id="asAny"><label for="asAny">any role</label><br/>
				<input v-model="pendingNameField" value="author" type="radio" name="nameRole" id="asAuthor"><label for="asAuthor">only as author</label><br/>
				<input v-model="pendingNameField" value="recipient" type="radio" name="nameRole" id="asRecipient"><label for="asRecipient">only as recipient</label><br/>
				<button @click="addSearchName" class="add">add to search</button>
			</div>

			
			<topics-lookup ref="topicbox" :project="project" @add-topic="chooseTopic" @close="closeTopic()"></topics-lookup>


			<div :class="showNames || showTopics ? 'lookupMask' : 'hidden'" @click="closeTopic(); showNames = false"></div>



			<div v-if="!groupEditions" class="summary inner-container" id="summary">
				<div class="hits">{{hitCount}} {{hitCount == 1 ? "document" : "documents"}}</div>

				<div id="paginationTop"></div>
				<!-- <div v-if="hitCount == 0 && selectedFacets.length && (params.terms.length || useDates)" class="noHitHelp">
				For better results, try searching first on just one parameter: for example just a keyword or date range. You can then refine your results further by limiting to the facets that appear to the left of your results list.
				</div> -->
			</div>


			<div v-if="!groupEditions && hitCount > 9" class="tools">
				<faceter :facets="subjectFacets" :prettyfacets="{}" :hitcount="hitCount" fielddisplay="Topic" field="subject" @facet-selected="addFacet"></faceter>
				<faceter :facets="nameFacets" :prettyfacets="prettyHuscs" :hitcount="hitCount" fielddisplay="Name" field="person_keyword" @facet-selected="addFacet"></faceter>
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


				<div :class="groupEditions ? '' : 'hidden'" class="groupView">
						<div v-for="group of groups" class="group">
								<div class="infoRow">
									<div class="editionName">{{projects[projects.nameToID[group.groupValue]].name}}</div>
									<span class="resultsCount"><b>{{group.doclist.numFound}}</b> document{{group.doclist.numFound > 1 ? "s" : ""}}</span>
								</div>

								<div v-for="doc, index of group.doclist.docs" class="documentCard">
									<a :href="buildLink(doc, group.groupValue, index)">
										<span v-if="projects[projects.nameToID[group.groupValue]].docListShowDate" class="date">{{coopHelpers.formatDate(doc.date_when)}}</span>
										<h3>{{doc.title[0]}}</h3>
										<p v-if="highlighting && highlighting[doc.id] && highlighting[doc.id]['text_merge']" v-html="'...' + highlighting[doc.id]['text_merge'][0] + '...'"></p>
										<p v-else v-html="doc.doc_beginning"></p>
									</a>
								</div>

								<div class="editionSearch" v-if="group.doclist.numFound > 3">
									<span>Showing the first 3 results.</span> <a class="button" :href="buildEditionSearchLink(group.groupValue)">see all results for this edition</a>
								</div>
						</div>
				</div>

				<div :class="groupEditions ? 'hidden' : 'plainList'">
					<div v-for="doc, index of documents" class="documentCard">
						<a :href="buildLink(doc,doc.index, index)">
							<div class="editionName">{{projects[projects.nameToID[doc.index]].name}}</div>
							<span v-if="projects[projects.nameToID[doc.index]].docListShowDate" class="date">{{coopHelpers.formatDate(doc.date_when)}}</span>
							<h3>{{doc.title[0]}}</h3>
							<p v-if="highlighting && highlighting[doc.id] && highlighting[doc.id]['text_merge']" v-html="'...' + highlighting[doc.id]['text_merge'][0] + '...'"></p>
							<p v-else v-html="doc.doc_beginning"></p>
						</a>
					</div>
				</div>
			</div>
		</div>
		


		<div class="inner-container">
			<div id="paginationBot"></div>
		</div>


	</div>
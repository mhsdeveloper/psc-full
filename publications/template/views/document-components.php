<?php //holding ground for HTML templates of vue components ?>

	<template id="datepicker">
		<svg tabindex="0" v-on:click="togglePicker" v-on:keyup.enter="togglePicker" v-bind:class="show ? 'showing' : ''"
			xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
		</svg>
		<div v-if="show" class="datePickerForm">
			<label>Jump to</label>
		</div>
	</template>







	<template id="nextPrev">
	<!-- v-if="hasSearchCxt && sequenceMode == 'search'" -->
		<div  class="navTools">
			<div v-if="searchParams" class="cxtControls">Viewing no. {{startLabel}} of {{searchTotal}}
				{{seqReadOrSearch == "read" ? 'selected documents' : 'search results'}} | 
				<a tabindex="0" @click="backToResults()" @keyup.enter="backToResults()">
					back to list
				</a>
				 <!-- | <a tabindex="0"	@click="toChronoNav()" @keyup.enter="toChronoNav()">all documents</a> -->
			</div>

			<div class="innerNavTools">
				<div class="previous" v-cloak>
					<div class="docChoices">
						<h4 v-if="prevDocs.length">
							<p v-if="searchParams">Previous</p>
							{{prevDocs[0].displayDate}}
						</h4>
						<a v-for="doc in prevDocs"
							:href="buildLink(doc.id, doc.index)"
							:title="doc.doc_beginning + '...'"
						>{{doc.title[0]}}</a>
					</div>
				</div>

				<div class="docTitle">
					<h3 class="docDate">{{today}}
						<div class="datePicker" v-scope="DatePicker({initial: 'props'})"></div>
					</h3>
					{{docTitle}}

					<div v-if="todayDocs.length" class="docChoices" v-cloak>
						<h4 @click="showTodayDocs = !showTodayDocs"
							@keydown.enter="showTodayDocs = !showTodayDocs"
							tabindex="0"
							:class="showTodayDocs ? 'showing' : ''"
						>Other documents on this day
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
							</svg>
						</h4>
						<div v-if="showTodayDocs" class="otherDocs">
							<a v-for="doc in todayDocs"
								:href="doc.id"
								:title="doc.doc_beginning + '...'"
							>{{doc.title[0]}}</a>
						</div>
					</div>
				</div>

				<div class="next" v-cloak>
					<div class="docChoices">
						<h4 v-if="nextDocs.length">
							<p v-if="searchParams">Next</p>
							{{nextDocs[0].displayDate}}
						</h4>
						<a v-for="doc in nextDocs"
							:href="buildLink(doc.id, doc.index)"
							:title="doc.doc_beginning + '...'"
						>{{doc.title[0]}}</a>
					</div>
				</div>
			</div>
		</div>
	</template>




	<template id="sidebarContextualizer">
		<div class="sidebarContextualizer">
			<div v-for="name of sideNames"  class="name">
				<a class="displayName">{{name.displayName}}</a>
				<span v-if="name.birthDate != '' || name.deathDate != ''" 
					class="dates">{{name.birthDate}}&#8212;{{name.deathDate}}
				</span>
				<button class="expand" @click="name.showDetails = !name.showDetails">+</button>
				<div v-if="name.showDetails" class="nameDetails">
					<div class="description">{{name.description}}</div>
					<div v-if="name.unknown" class="description unknown">We have not been able to identify this individual. If you know who they are, please be in touch.</div>
				</div>
			</div>

			<template v-if="sideNote.text"><div v-html="sideNote.text"></div></template>
		</div>
	</template>



	<template id="authorRecipient">

		<div class="authors" v-if="authors.length">
			<h3>Author{{authors.length == 1? "" : "s"}}:</h3>
			<div class="name" v-for="name in authors">
				<a class="displayName" tabindex="0" @click="toggleName" @keyup.enter="toggleName">{{name.displayName}}</a>
				<div class="nameDetails">
					<div class="dates">{{name.birthDate}}&#8212;{{name.deathDate}}</div>
					<div class="description">{{name.descriptions}}</div>
				</div>
			</div>
		</div>

		<div class="recipients" v-if="recipients.length">
			<h3>Recipient{{recipients.length == 1? "" : "s"}}:</h3>
			<div class="name" v-for="name in recipients">
				<a class="displayName" tabindex="0" @click="toggleName" @keyup.enter="toggleName">{{name.displayName}}</a>
				<div class="nameDetails">
					<div class="dates">{{name.birthDate}}&#8212;{{name.deathDate}}</div>
					<div class="description">{{name.descriptions}}</div>
				</div>
			</div>
		</div>

	</template>





	<template id="pageImages">
		<div class="pageImages">
			<div v-for="img of images">
				<img :src="img.src" @click="viewImage(img.src)"/>
				<!-- <button @click="viewImage(img.src)">open in viewer</button> <button @click="viewDraggable(img.src)">open as draggable box</button> -->
				<p class="caption">page {{img.n}}</p>
			</div>
		</div>

		<div id="osdButtons">
			<svg id="zoomIn" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6" />
			</svg>


			<svg id="zoomOut" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
			  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM13.5 10.5h-6" />
			</svg>


			<svg id="osdReset" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
			</svg>



		</div>
	</template>


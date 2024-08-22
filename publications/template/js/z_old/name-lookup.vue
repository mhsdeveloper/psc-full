


export  default {

	data() {

	},

	template: `
	<div id="nameLookupModal">

		<div v-bind:class="showNameLookup ? 'open modal namesList' : 'modal namesList'">
			<h2>Lookup a name</h2>
			<input 
				v-on:keyup="queueLookupName" 
				ref="nameLookupInput" 
				autocomplete="off" 
				name="nameLookup" 
				placeholder="any name part, or last, first"
			/>
			<div id="namesList">
				<a 
					v-for="person in nameLookups" 
					v-on:click="chooseName" 
					tabindex="0" 
					@keyup.enter="chooseName" 
					v-bind:data-husc="person.name_key"
				>
				<span v-if="person.family_name">{{person.family_name}}</span>
				<span v-if="person.given_name">, {{person.given_name}}</span>
				<span class="dates">({{person.date_of_birth.substr(0,5)}} - {{person.date_of_death.substr(0,5)}})</span>
				</a>
			</div>
		</div>
	</div>
	`

}
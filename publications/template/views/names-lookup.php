<?php ?>
	<div id="nameLookupModal">
		<div v-bind:class="showNameLookup ? 'open modal namesList' : 'modal namesList'">
			<h2>Lookup a name</h2>
			<input ref="nameLookupInput" autocomplete="off" name="nameLookup" v-on:keyup="queueLookupName" placeholder="any name part, or last, first"/>
			<div id="namesList">
				<a v-for="person in nameLookups" v-on:click="chooseName" tabindex="0" @keyup.enter="chooseName" v-bind:data-husc="person.name_key">
					<?php include(\MHS\Env::APP_INSTALL_DIR . "customize/name-autocomplete-template.html");?>
				</a>
			</div>
		</div>
	</div>


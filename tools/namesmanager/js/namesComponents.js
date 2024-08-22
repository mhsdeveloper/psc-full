Vue.component('psc-name', {
	props: ['name', 'project', 'projects', 'fields', "userlevel"],
	data: function () {
		return {
			//name: null
		}
	},

	methods: {
		findPublic(name){
			for(let desc of name.projectmetadata){
				if(desc.project_id == this.project){
					return desc.public == "1" ? "YES" : "";
				}
			}
			return "";
		}
	},

	template: 
		`
		<div v-bind:class="(name.inProject ? 'inProject' : 'notInProject')">
			<span class="cell actions">
				<input type="checkbox" v-if="(userlevel > 2) && name.showCheck" v-model="name.selected" v-bind:title="name.id"/>
			</span>
			<span class="cell fullName" v-on:click="$emit('view', name)">
				<div class="toolTipWrapper">
					{{ name.middle_name ? name.family_name + ', ' +  name.given_name + ' ' + name.middle_name : name.family_name + ', ' + name.given_name}}
					<div class="toolTip">
						{{name.title}} {{name.given_name}} {{name.middle_name}} {{name.maiden_name}} {{name.family_name}} {{name.suffix}}
					</div>
				</div>
			</span>
			<span class="cell dates">{{ (name.birth_ca != "0" ? "ca." : "") + " " + name.date_of_birth + (name.birth_era == "bce" ? " BCE " : "") + "-" + name.date_of_death + " " + (name.death_era == "bce" ? "B.C.E." : "")}}</span>
			<span class="cell husc" v-on:click="$emit('copy-name-key', name.name_key)">{{name.name_key}}<span class="material-icons">content_copy</span></span>
			<span class="cell public">{{findPublic(name)}}</span>

			<div v-if="fields.notes" class="cell notes">
				<div class="toolTipWrapper">
					<div class="note" v-for="note in name.notes">
						<div v-if="note.project_id == project">
							<b>{{projects["" + note.project_id] ? projects["" + note.project_id].abbr : ""}}:</b> {{note.notes}}
						</div>
					</div>
					<div class="toolTip">
						<div class="note" v-for="note in name.notes">
							<b>{{projects["" + note.project_id] ? projects["" + note.project_id].abbr : ""}}:</b> {{note.notes}}
						</div>
					</div>
				</div>
			</div>
			<span v-if="fields.verified" class="cell verified">{{name.verified}}</span>
			<span v-if="fields.firstMentioned" class="cell firstMention">{{name.first_mention}}</span>
		</div>

		`
});


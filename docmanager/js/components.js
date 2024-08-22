NOT USED ANY MORE!!


Vue.component('dm-document', {
	props: ['document', 'index', 'username', "userrole", "userlevel"],
	data: function () {
		return {
			Env: Env
		}
	},

	methods: {
		view: function(){
		},

	},

	computed: {
		fileNoExt: function(){
			let f = this.document.filename.split(".xml")[0];
			return f;
		},


		fileDate: function(){
			let d = this.document.date_when;
			return d;
		},


		checkedOutInDate: function(){
			let d = this.document.checked_outin_date;
			return d;
		}
	},

	template: 
		`<div class="document"  v-bind:data-id="document.id">
			<div class="rowish">
				<div class="drawerToggle" v-on:click="toggleDrawer">&nbsp;</div>

				<div class="filename" v-on:click="toggleDrawer">
					<div class="date">{{fileDate}}</div>
					<h2>{{fileNoExt}}</h2>
					<div class="context" v-if="document.context && document.context.length" v-html="document.context"></div>
				</div>		

				<div class="steps">
					<template v-for="step in document.steps">
						<span class="step" v-on:click="toggleStepStatus(step)">
							<span class="status" v-if="step.status == '1'" v-bind:style="'background: ' + step.color"><q-icon name="done" style="color: #fff; font-size: 1.3rem"></q-icon></span>
							<span class="status" v-if="!step.status || step.status == '0'" v-bind:style="'opacity: .35; background: ' + step.color"></span>
							{{step.short_name}}
							<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
								<b>Last changed by {{step.username}}</b><br/>
								<b>{{step.name}}</b><br/>
								{{step.description}}
							</q-tooltip>
						</span><span class="stepArrow"> </span>
					</template>
				</div>

				<div v-if="userlevel > 2 || userrole == 'xml_editor'" class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
					<template v-if="document.checked_out == 1">
						<span>
							<span>{{document.checked_outin_by}}: {{checkedOutInDate}}</span> 
							<q-btn size="sm" v-if="username == document.checked_outin_by" unelevated label="Checkin" color="negative" v-on:click="$emit('checkin', document)"></q-btn>
						</span>
					</template>
					<template v-else>
						<q-btn size="sm" unelevated label="Checkout" v-on:click="$emit('checkout', document)" color="positive"></q-btn><br/>
						<span>{{document.checked_outin_by}}<br/>{{checkedOutInDate}}
							<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
							last checked out/in by {{document.checked_outin_by}} on {{document.checked_outin_date}}
							</q-tooltip>
						</span>
					</template>
				</div>
				<div v-else class="checked_out" v-bind:data-status="document.checked_out == -1 ? 'loading': '' ">
					<template v-if="document.checked_out == 1">
						<span>Checked out to <span>{{document.checked_outin_by}}: {{checkedOutInDate}}</span></span>
					</template>
					<template v-else>
						<span>Last edited by {{document.checked_outin_by}}<br/>{{checkedOutInDate}}
							<q-tooltip anchor="bottom middle" content-style="max-width: 300px; font-size: 16px" self="top middle" :offset="[10, 10]">
							last checked out/in by {{document.checked_outin_by}} on {{document.checked_outin_date}}
							</q-tooltip>
						</span>
					</template>
				</div>

				<div class="view">
					<a v-bind:href="Env.viewURL + fileNoExt" target="_blank">View</a>
					<a v-bind:href="Env.viewURL + fileNoExt + '?proof=1'" target="_blank">Proof</a>
				</div>

				<div class="published" v-bind:data-status="document.published == -1 ? 'loading': '' ">
					<span>{{document.published}}</span>
				</div>
			</div>
			<div class="drawer">
`
	+ window.docManagerCustomDrawerTemplate + 

`				<div class="other">
					<q-btn v-if="userlevel > 3" unevelated color="negative" label="DELETE" v-on:click="$emit('delete-file', document)"></q-btn>
				</div>
			</div>
		</div>`
});

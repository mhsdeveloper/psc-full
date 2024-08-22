let Faceter = {
	props: ['field', 'facets', "prettyfacets", 'fielddisplay', 'hitcount'],
	emits: ['facet-selected'],
	data() {
		return {
			showAll: false,
			showCount: 5,
		}
	},
	template: `
	<div class="facetSet">
		<h3 v-if="facets.length">Refine by {{fielddisplay}}</h3>
		<template v-for="facet, i of facets" class="facet">
			<span v-if="(i < showCount) && facet.count < hitcount"
				@click="$emit('facet-selected', {facet, field})"
			>
				<b>{{ prettyfacets[facet.name] ? prettyfacets[facet.name] : facet.name }}</b>
				<u class='count'>{{facet.count}} document{{ facet.count > 1 ? 's' : ''}}</u>
			</span>
		</template>
		<button v-if="facets.length > 10" @click="showCount += 10">{{ showAll ? "show fewer" : "show more" }}</button>
	</div>
	`,


	mounted() {

	}
}


export { Faceter }
// function DocumentList(props = {}) {

// 	return {
// 		documents: props.documents,
// 		highlighting: props.highlighting,
// 		$template: `
// 			<div v-for="doc of documents" class="documentCard">
// 				<h3>{{doc.title[0]}}</h3>
// 				<p v-if="highlighting[doc.id] && highlighting[doc.id]['text_merge']" v-html="'...' + highlighting[doc.id]['text_merge'][0]"></p>
// 				<p v-else v-html="doc.doc_beginning"></p>
// 			</div>
		
// 		`,


// 		mounted() {
// 		}
// 	}
// }


// export { DocumentList }
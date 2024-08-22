
(function(){


	//create if doesn't exist
	let feedbackEl = document.createElement("div");
	feedbackEl.className = "simpleFeedback";
	feedbackEl.innerHTML = `
		<h4>Let us know what you think</h4>
		<p>Please leave us a detailed note on what you liked or disliked, on any problems you ran into. If you'd like a response, you can leave us your email. We won't share it with anyone and will only use it to contact you regarding your feedback.</p>
		<p class="bold">Any and all feedback welcome!</p>
		<textarea id="feedbackText"></textarea>
		<input name="username" type="text" id="feedbackUser"/>
		<input type="email" id="feedbackEmail"/> 
		<button id="feedbackBut">Submit</button>
	`;
	document.body.appendChild(feedbackEl);

	let feedbackStarter = document.createElement("div");
	feedbackStarter.className = "feedbackStarter";
	let b = document.createElement("button");
	b.addEventListener('click', () =>{
		feedbackEl.classList.add("open");
	});

	let placementEl = document.getElementById("feedme");
	if(!placementEl) placementEl = document.body;
	placementEl.appendChild(feedbackStarter);	

})();
/* Web Custom Element
*/

class DatePicker extends HTMLElement {
	constructor() {
		super();

		const shadow = this.attachShadow({mode: 'open'});
		const style = document.createElement('style');
		this.wrapper = document.createElement("div");
		shadow.appendChild(style);
		shadow.appendChild(this.wrapper);
		this.wrapper.setAttribute('class', 'wrapper');

		
		this.wrapper.innerHTML = /*html*/
		`
			<svg
				xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
			</svg>

		`;


		style.textContent = /*css*/
		`
			.wrapper {
				display: inline-block;
				cursor: pointer;
			}

			svg {
				width: 27px;
				color: white;
				background: var(--psc-color-background-vivid);
				padding: 4px;
				top: 4px;
				position: relative;
				border-radius: 3px;
			}

		`;

		this.addEvents();
		this.callback = this.getAttribute("data-callback");
	}


	addEvents(){
		this.wrapper.setAttribute("tabindex", "0");
		this.addEventListener("click", this.open);
		this.addEventListener("keydown", (e)=>{if(!e.key == "Enter") return; this.open()});
	}

	open(){
		
	}
}
  
// Define the new element
customElements.define('date-picker', DatePicker);
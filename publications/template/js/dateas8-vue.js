let DateAs8 = {

	props: ['date', 'index'],
	emits: ['update'],
	data(){
		return {
			year: "",
			month: "",
			day: "",
			type: "",
		}
	},

	methods: {
		updateDate(){
			let dateString = this.year + this.month + this.day;
			this.$emit('update', {prop: this.$el.getAttribute("data-type"), dateString, index: this.index});

		},
	},


	mounted() {
		this.year = this.date.substring(0,4);
		this.month = this.date.substring(4,6);
		this.day = this.date.substring(6,8);
	},


	template: `
		<div class="date">
			<input v-model="year" type="number" @change="updateDate"/>

			<select v-model="month" @change="updateDate">
				<option value="00">month</option>
				<option value="01">January</option>
				<option value="02">February</option>
				<option value="03">March</option>
				<option value="04">April</option>
				<option value="05">May</option>
				<option value="06">June</option>
				<option value="07">July</option>
				<option value="08">August</option>
				<option value="09">September</option>
				<option value="10">October</option>
				<option value="11">November</option>
				<option value="12">December</option>
			</select>

			<select v-model="day" @change="updateDate">
				<option value="00">day</option>
				<option value="01">1</option>
				<option value="02">2</option>
				<option value="03">3</option>
				<option value="04">4</option>
				<option value="05">5</option>
				<option value="06">6</option>
				<option value="07">7</option>
				<option value="08">8</option>
				<option value="09">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
				<option value="13">13</option>
				<option value="14">14</option>
				<option value="15">15</option>
				<option value="16">16</option>
				<option value="17">17</option>
				<option value="18">18</option>
				<option value="19">19</option>
				<option value="20">20</option>
				<option value="21">21</option>
				<option value="22">22</option>
				<option value="23">23</option>
				<option value="24">24</option>
				<option value="25">25</option>
				<option value="26">26</option>
				<option value="27">27</option>
				<option value="28">28</option>
				<option value="29">29</option>
				<option value="30">30</option>
				<option value="31">31</option>
			</select>	
		</div>
		`
}


export { DateAs8 }
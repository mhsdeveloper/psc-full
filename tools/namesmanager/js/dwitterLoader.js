class DwitterLoader {

	constructor(el){
		var canv = el;
		this.canv = el;
		this.context = canv.getContext('2d');

		this.time = 0;
		this.frame = 0;
		this.activeFunction = ()=>{}
	}


	R(r,g,b,a) {
		a = a === undefined ? 1 : a;
		return 'rgba(' + (r|0) + ',' + (g|0) + ',' + (b|0) + ',' + a +')';
	}

  	loop() {
 	    if(this.paused) return;
		window.requestAnimationFrame(() => {this.loop();});
//if(this.frame % 60 == 1) console.log("FRAME " + this.frame);
		this.time = this.frame/60;
		if(this.time * 60 | 0 == this.frame - 1){
			this.time += 0.000001;
		}
		this.frame++;
		try{
			this.activeFunction(this.time);
		} catch(err){
		}
	}

 	reset(){
		this.context.clearRect(0, 0, 1920, 1080);
		this.time = 0;
		this.frame = 0;
	}


	start(type){
		switch(type){
			case "swirl": this.activeFunction = (t) => {this.swirl(t);}; break;
			case "ornament": this.activeFunction = (t) => {this.ornament(t);}; break;
			case "helix": this.activeFunction = (t) => {this.helix(t);}; break;
			case "dominos": this.activeFunction = (t) => {this.dominos(t);}; break;
			default:  this.activeFunction = (t)=> {this.ballTube(t);}; break;
		}

		window.requestAnimationFrame(() => {this.loop();});
	}

	pause(){
		this.paused = true;
	}

	resume(){
		this.paused =false;
		window.requestAnimationFrame(() => {this.loop();});
	}


	dominos(t){
		for(let i= this.canv.width = 2000;i--;){
			this.context.setTransform(1,0,0,1,i*99+500-t*389,590);
			let k = Math.min(t-i/4, 1.1);
			if(k > 0) {
				this.context.rotate(k);
			}
			this.context.fillRect(-50,-200,50,200)
		}
	}

	swirl(t){
		this.canv.width|=0;
		for(let i=0;i<300;i+=0.1){
			this.context.fillRect(960-Math.sin(i+t)*5*i, 540-Math.cos(i+t)*5*i, 10, 10);
		}
	}

	helix(t){
		//this.canv.width|=0;
		let r = 500;
		let s = 2 * r;
		this.context.clearRect(0, 0, 1920, 1080);
		for(let i= 2*r; i--;) {
			let m = Math.tan(i*i);
			let j = i/20;
			let X = Math.sin(j-t) * j / (20+10 * Math.tan(t));
			let Y= Math.tan(j-t)*j/40;
			let q = 10 * (Y + 2);
			this.context.fillRect(X*r+s,Y*r+r-m*r/((i/80+2)),q,q);
		}
	}



	ornament(t){
		this.canv.width = 1920;
		for(let i=3;i<30;i++) {
			let b=4 * Math.sin(Math.cos(t/3)*3)*i/5;
			let e=b+b/3+2;
	  		let r=40*i;
	  		this.context.lineWidth=30;
			this.context.beginPath();
	  		this.context.arc(960,540,r,b,e);
			this.context.stroke();
		}
	}




	ballTube(t){
//if(this.frame % 60 == 1) console.log("FRAME " + this.frame);
		this.context.strokeStyle='#FFF';
		this.context.fillStyle="#E7E7E7";
		for(let i=400; i > 0;){
			let red = i % 10;
			this.context.strokeStyle = this.R(255 - (200 + red * 30), 0,255 ); 
			this.context.fill();
			this.context.lineWidth = 2;
			this.context.beginPath();
			let r = 1.01**(300 - i) * 80;
			let a = i + t/1;
			this.context.arc( 960 + r * Math.sin(a) + 99 * Math.sin(i/50 + 2*t), 600+r * Math.cos(a), r/5, 0, 7);
			i--;
			this.context.stroke();
		}
	  }

}
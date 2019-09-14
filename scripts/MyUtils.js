function createReq(){
	let req = null;
	if (window.XMLHttpRequest) req = new XMLHttpRequest()
	else if (ActiveXObject("Microsoft.XMLHTTP")) req = new ActiveXObject("Microsoft.XMLHTTP")
	else alert('Problem z przeglądarką internetową, window.XMLHttpRequest lub ActiveXObject("Microsoft.XMLHTTP")nie mół zostać utworzony');
	
	return req;
}
function load(file_path, selector, callback){
	let req= createReq();
	if (req==null) return;
	req.open('GET',file_path,true);
	req.send();
	req.onreadystatechange = function () {
		if (req.readyState==4 && req.status==200) {
			if(selector!=''){
				let where = document.querySelector(selector);	
				where.innerHTML = req.responseText;	
			}
			if (undefined!==callback) return callback(req.responseText);;
		}
		if (req.status==404) {
			where.innerHTML = 'file not found...';
		}
	}
}
const c = console.log;
HTMLInputElement.prototype.isError = true;

HTMLInputElement.prototype.error = function(msg){
	this.clrError();
	let errorField = document.createElement('div');
	$(errorField).hide().fadeIn('slow');
	this.classList.add('input-error');
	errorField.className = 'input-error-msg';
	errorField.innerHTML = msg;
	errorField.style.cssText = 'color:red; text-align:center';
	$(this).after(errorField);
	this.isError = true;
		}
		
HTMLInputElement.prototype.clrError = function(){
	if (this.nextSibling.className == 'input-error-msg') {
		this.classList.remove('input-error');
		$(this.nextSibling).remove();
	}
	this.isError = false;
}

HTMLTextAreaElement.prototype.error = function(msg){
	this.clrError();
	let errorField = document.createElement('div');
	$(errorField).hide().fadeIn('slow');
	this.classList.add('input-error');
	errorField.className = 'input-error-msg';
	errorField.innerHTML = msg;
	errorField.style.cssText = 'color:red; text-align:center';
	$(this).after(errorField);
	this.isError = true;
		}
		
HTMLTextAreaElement.prototype.clrError = function(){
	if (this.nextSibling.className == 'input-error-msg') {
		this.classList.remove('input-error');
		$(this.nextSibling).remove();
	}
	this.isError = false;
}

function popupWait(){
	let body = document.querySelector('body');
	let backg = document.createElement('div');
	let outer = document.createElement('div');
	backg.id = 'awaiting-backg';
	outer.id = 'awaiting-outer';
	backg.setAttribute('style', 'position: fixed; top: 0; left: 0; opacity: 0; background-color: black; width: 100%; height:100%; z-index: 15');
	body.appendChild(backg);
	$(backg).animate({opacity:'0.7'},100);	
	outer.setAttribute('style', 'position: fixed; padding: 20px; min-width: 80px; min-height: 50px; border: 1px solid silver; border-radius: 5px; background-color: white; z-index: 20;');
	body.appendChild(outer);
	$(outer).hide();
	$(outer).fadeIn(150);
	outer.innerHTML = '<img src="img/loading.gif" width="100" height="100"><br>Zaczekaj chwileczkę...';
	let img = document.querySelector('#awaiting-outer img');
	img.style.marginLeft = '20px';
	centerDiv(body, outer);
}
function closePopupWait(){
	let backg = document.querySelector('#awaiting-backg');
	let outer = document.querySelector('#awaiting-outer');
	$(backg).fadeOut(100, function(){backg.parentNode.removeChild(backg)});
	$(outer).fadeOut(100, function(){outer.parentNode.removeChild(outer)});
}
function popUp(content, minWidth, minHeight){ //shows popUp window in the center of the screen
	let backg = document.createElement('div');
	let outer = document.createElement('div');
	backg.setAttribute('style', 'position: fixed; top: 0; left: 0; background-color: black; opacity: 0; width: 100%; height:100%; z-index: 5');
	let body = document.querySelector('body');
	body.appendChild(backg);
	$(backg).animate({opacity:'0.7'});	
	backg.id = 'black-backg-popup';
	outer.id = 'outer-popUp';
	outer.setAttribute('style', 'position: fixed; padding: 20px; min-width: '+minWidth+'px; min-height: '+minHeight+'px; border: 1px solid silver; border-radius: 5px; background-color: white; z-index: 10');
	outer.style.opacity = '0';
	body.appendChild(outer);
	$(outer).animate({opacity: '1'});
	outer.innerHTML = content;
	centerDiv(body, outer);
	
}
function closepopUp(){
	let backg = document.querySelector('black-backg-popup');
	let outer = document.querySelector('outer-popUp');	
	backg.parentNode.removeChild(backg);
	outer.parentNode.removeChild(outer);
}
function Xalert(type,title,content,callback=null){ //shows popUp window in the center of the screen
	let body = document.querySelector('body');
	let backg = document.createElement('div');
	let popUpOuter = document.createElement('div');
	let popUp = document.createElement('div');
	let logo = document.createElement('div');
	let tit = document.createElement('div');
	let cont = document.createElement('div');
	let buttsDiv = document.createElement('div');
	let close = document.createElement('span');
	let buttOk = document.createElement('button');
	let buttCancel = document.createElement('button');
	buttOk.innerHTML = 'Ok';
	buttCancel.innerHTML = 'Cancel';
	let logoCode;
	backg.setAttribute('style', 'position: fixed; top: 0; left: 0; background-color: black; opacity: 0; width: 100%; height:100%; z-index: 900');
	popUpOuter.setAttribute('style', 'position: fixed; text-align: center; padding: 0 20px 0 20px; min-width: 350px; max-width: 30%; min-height: 250px; border: 1px solid silver; border-radius: 5px; background-color: white; z-index: 901; word-wrap: break-word;');
	popUp.setAttribute('style', 'position: relative; top: -15px; ');
	logo.setAttribute('style','position: relative; top: -30px; font-weight: bolder; display: inline; color: white; font-size: 5em; border-radius: 15px; box-shadow: -1px 2px 20px -5px black');
	close.setAttribute('style', 'position: absolute; top: 15px; right: 2%; font-size: 2.3em; font-weight: bolder; transition: color 0.2s; cursor: pointer');
	tit.setAttribute('style', 'position: relative; top: -20px; color: #7b7b7b; font-size: 1.8em; font-weight: bold;  width: 100%');
	cont.setAttribute('style', 'position: relative; font-size: 1.2em; width: 100%');
	buttsDiv.setAttribute('style', 'position: relative; width: 100%; height: 30px; margin-top: 30px;');
	let butStyle = 'width: 100px; height: 30px; background-color: #4b9f4e; color: white;border: 0; border-radius: 4px; font-size: 1.2em; margin: 0 25px 0 25px;';
	buttOk.setAttribute('style', butStyle);
	buttCancel.setAttribute('style', butStyle);
	buttCancel.onclick = function() {closeXalert()};
	buttsDiv.appendChild(buttOk);
	switch (type) {
		case 'confirm':
			logo.style.backgroundColor ='#347AB8';
			logoCode = '&#x3f;';
			buttOk.style.backgroundColor = '#347AB8';
			buttCancel.style.backgroundColor = '#347AB8';
			buttsDiv.appendChild(buttCancel);
			break;
		case 'success': 
			logo.style.backgroundColor ='green';
			logoCode = '&#9745;';
			break;
		case 'warning': 
			logo.style.backgroundColor ='#ffcb00';
			logoCode = '&#9888;';
			buttOk.style.backgroundColor = 'rgb(255, 203, 0)';
			buttCancel.style.backgroundColor = 'rgb(255, 203, 0)';
			break;
		case 'info': 
			logo.style.backgroundColor ='#0974EE';
			logoCode = '&#8505;';
			buttOk.style.backgroundColor = '#0974EE';
			buttCancel.style.backgroundColor = '#0974EE';
			break;
		case 'error':
			logo.style.backgroundColor ='#D5554E';
			logoCode = '&#x292B;';
			buttOk.style.backgroundColor = '#D5554E';
			buttCancel.style.backgroundColor = '#D5554E';
			break;
	}
	buttOk.onclick = function() {closeXalert(); if(callback!=null)callback()};
	// if (type == 'success') {logo.style.backgroundColor ='green'; logoCode = '&#9745'}
	// if (type == 'warning') {logo.style.backgroundColor ='#ffcb00'; logoCode = '&#9888;'}
	// if (type == 'info') {logo.style.backgroundColor ='blue'; logoCode = '&#8505;'}
	// if (type == 'error') {logo.style.backgroundColor ='red'; logoCode = '&#x292B;'}
	logo.innerHTML ='<span style="font-size: 40px">&nbsp;</span>'+logoCode+'<span style="font-size: 40px">&nbsp;</span>'; //mark symbol
	close.innerHTML = '&#215'; // X symbol
	tit.innerHTML = title;
	cont.innerHTML = content;
	body.appendChild(backg);
	body.appendChild(popUpOuter);
	popUpOuter.appendChild(popUp);
	popUp.appendChild(logo);
	popUp.appendChild(close);
	popUp.appendChild(tit);
	popUp.appendChild(cont);
	popUp.appendChild(buttsDiv);
	
	
	
	$(backg).animate({opacity:'0.7'});	
	$(popUpOuter).animate({opacity:'1'});	
	centerDiv(body, popUpOuter);
	close.onmouseover = function(){close.style.color = 'red'};
	close.onmouseout = function(){close.style.color = 'black'}
	close.onclick = closeXalert;
	
	function closeXalert(){
		$(backg).fadeOut(200, function(){
			backg.parentNode.removeChild(backg);
			popUpOuter.parentNode.removeChild(popUpOuter);
		});
	}
	
}
function centerDiv(container, div){ //internal div in relation to the external element (window, html, body or div)
		let containerW; let containerH;
		if(container == window ) {containerW = container.innerWidth; containerH = container.innerHeight}
		else {containerW = container.offsetWidth; containerH = container.offsetHeight;}
		let x = ((containerW-div.offsetWidth)/2)+'px';
		let y = ((containerH-div.offsetHeight)/2)+'px';
		div.style.left = x;
		div.style.top = y;
		window.onresize = function(){centerDiv(container, div)};
	}
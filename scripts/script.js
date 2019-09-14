
function previewImg(picture){
	$(picture).mouseenter(function(){
			let itemId = $(this).attr('id');
			d = setTimeout(function(){
				wrap = document.createElement('div');
				wrap.classList = 'wrap';
				load('preview.php?itemId='+picture.id, '', function(result){
					wrap.innerHTML = result;
					picture.append(wrap);
					$('.preview-wrap').hide();
					$('.preview-wrap').fadeIn(150);
					$('.preview-mini').click(function(){
						document.querySelector('.preview-medium-pic-img').src= 'img-ads/'+itemId+'/'+this.id+'.jpg';
					});
				});
			}, 400);
		}).mouseleave(function(){
			clearInterval(d);
			$('.preview-wrap').fadeOut(100, function(){
				$('.wrap').remove();
			});
		});
}
 
function lightbox(miniImg){
	let wrapPopup = document.createElement('div');
	wrapPopup.classList = 'wrap-popup';
	let backg = document.createElement('div');
	backg.id = 'black-backg-popup';
	let cont = document.querySelector('#content-outer');
	cont.appendChild(wrapPopup);
	wrapPopup.appendChild(backg);
	let picFrame = document.createElement('div');
	picFrame.classList = 'pic-frame';
	let prev = document.createElement('div');
	let next = document.createElement('div');
	prev.classList = 'pic-frame-prev';
	next.classList = 'pic-frame-next';
	let leftArrow = document.createElement('img');
	let rightArrow = document.createElement('img');
	let closeButPreview =document.createElement('img');
	closeButPreview.src= 'img/closePreview.png';
	closeButPreview.classList = 'close-but-prewiev';
	leftArrow.src = 'img/prev.png';
	rightArrow.src = 'img/next.png';
	leftArrow.classList = 'left-arrow-img';
	rightArrow.classList = 'right-arrow-img';
	let picNo = parseInt(miniImg.dataset.item_no, 10);
	let allPicLength = document.querySelectorAll('#ad-bloc img').length;
	let itemId = miniImg.dataset.item_id;
	if(is_first_or_last() == 'last') next.style.opacity = '0';
	if(is_first_or_last() == 'first') prev.style.opacity = '0';
	prev.appendChild(leftArrow);
	next.appendChild(rightArrow);
	picFrame.appendChild(prev);
	picFrame.appendChild(next);
	picFrame.appendChild(closeButPreview);
	wrapPopup.appendChild(picFrame);
	$(backg).animate({opacity: '0.7'}, 'fast');
	$(picFrame).animate({opacity: '1'}, 'fast');
	let pic = document.createElement('img');
	pic.src = 'img-ads/'+miniImg.dataset.item_id+'/'+picNo+'.jpg';
	
	
	picFrame.appendChild(pic);
	$(pic).on('load', function(){
		wrapPopup.style.height = pic.offsetHeight + 'px';	
	});
	let wrapPicNo = document.createElement('div');
	picFrame.appendChild(wrapPicNo);
	function updatePicNo(){
		wrapPicNo.innerHTML = '<span class="pic-no"> <span style="font-size: 30px">'+((picNo+1))+'</span><span style="fonct-size: 20px">/'+((document.querySelectorAll("#ad-bloc img").length))+'</span> </span>';
	}
	updatePicNo();
	pic.onload = function(){
		leftArrow.style.top = (pic.height - leftArrow.height)/2;
		rightArrow.style.top = (pic.height - leftArrow.height)/2;
	}
	
	function is_first_or_last(){
		if (picNo == 0) return 'first';
		if (picNo == allPicLength-1)return 'last';
		return false;
	}
	prev.onclick = function(){
		if (picNo>0){
			picNo --;
			pic.src = 'img-ads/'+miniImg.dataset.item_id+'/'+(picNo)+'.jpg';
			if(is_first_or_last() == 'first') prev.style.opacity = '0';
			if(!is_first_or_last()) prev.style.opacity = '0.5';
			if(!is_first_or_last()) {
				$(next).hover(function(){$(next).css({opacity : '0.5'})}, function(){$(next).css({opacity : '0'})})
			}
			updatePicNo();
		}
	}
	next.onclick = function(){
		if (picNo < document.querySelectorAll('#ad-bloc img').length-1) {
			picNo ++;
			pic.src = 'img-ads/'+miniImg.dataset.item_id+'/'+(picNo)+'.jpg';
			if(is_first_or_last() == 'last') next.style.opacity = '0';
			if(!is_first_or_last()) next.style.opacity = '0.5';
			if(!is_first_or_last()) {
				$(prev).hover(function(){$(prev).css({opacity : '0.5'})}, function(){$(prev).css({opacity : '0'})})
			}
			updatePicNo();
		}
	}
	
	backg.onclick= function(){
		$('.wrap-popup').remove();
	};
	closeButPreview.onclick = function(){
		$('.wrap-popup').remove();
	}
}

function option(value){
	let z = load('suboption.php?opt='+value, '#suboption');
	$(z).ready(function() {	
		$('#suboption').slideUp('fast');
		$('#suboption').slideDown('fast');
	});
}

let inputFiles;
let info;
let td;
// function start(){
	// inputFiles = document.querySelectorAll('input[type="file"]');
	// for(let i=0; i<inputFiles.length; i++){
		// inputFiles[i].onchange = function(){update(i)}	
	// };
// }

function update(a){
	function size_prefix(element){
		if (element<1024) return element+' Bajtów';
		if (element>1023 && element<1048576) return (element/1024).toFixed(2)+' KB';
		if (element>1048576) return (element/1048576).toFixed(2)+' MB';
	}
	let maxFilesSize=18874368;
	let currSize=0;
	inputFiles = document.querySelectorAll('#tab-pic-add input[type="file"]');
	for(let i=0; i<inputFiles.length; i++){
		if(typeof(inputFiles[i].files[0])!= 'undefined') currSize += inputFiles[i].files[0].size;
	}
	if(currSize>maxFilesSize) {
		alert('Możesz dodać jedynie '+size_prefix(maxFilesSize));
		inputFiles[a].value='';
		return false;
	}		
	function updateProgressBar(){
		let sizeAll=0;
		for(let i=0; i<inputFiles.length; i++){
			if(typeof(inputFiles[i].files[0])!= 'undefined') sizeAll += inputFiles[i].files[0].size;
		}
		let prog = document.getElementById('prog-info');
		prog.max=maxFilesSize;
		prog.value=sizeAll;
		let divSize = document.getElementById('size-info');
		divSize.innerHTML='obecnie '+size_prefix(sizeAll)+' z '+size_prefix(maxFilesSize);
		$('#prog-info').slideDown('slow');
		return sizeAll;
	}
	
	inputFiles = document.querySelectorAll('#tab-pic-add input[type="file"]');
	$(inputFiles[a]).fadeOut('medium');
	divFile = $('#tab-pic-add td .div-file');
	let wrapImg = document.createElement('div');
	divFile[a].appendChild(wrapImg);
	let imgClose = document.createElement('img');
	imgClose.src='img/close.png';
	imgClose.classList.add('imgClose');
	wrapImg.appendChild(imgClose);
	let img = document.createElement('img');
	let ddd = document.getElementsByClassName('file-but');
	img.src = window.URL.createObjectURL(inputFiles[a].files[0]);
	img.classList.add('img');
	wrapImg.appendChild(img);
	let label = document.createElement('div');
	label.classList.add('label');
	wrapImg.appendChild(label);
	let info = document.createElement('div');
	info.classList.add('info');
	info.innerHTML = inputFiles[a].files[0].name+'<br>('+size_prefix(inputFiles[a].files[0].size)+')';
	wrapImg.appendChild(info);
	
	$(imgClose).hide();
	$(img).fadeOut(1)
	$(img).fadeIn('slow');
	$('.div-file:eq('+a+')').mouseenter(function(){
		$(imgClose).stop();
		$(imgClose).slideDown("fast");
	});
	$('.div-file:eq('+a+')').mouseleave(function(){
		$(imgClose).stop();
		$(imgClose).slideUp("fast");
	});
	let closeBut = wrapImg.querySelector('.imgClose');
	closeBut.onclick = function(){
		if (confirm('Usunąć?')) {
			$(wrapImg).fadeOut("slow", function(){
				inputFiles[a].value='';
				wrapImg.innerHTML=null;
				inputFiles[a].classList.add('file-but');
				$(inputFiles[a]).fadeIn('slow');
				updateProgressBar();
			});
		}
	}
	updateProgressBar();
}
let fileTypes = ['image/jpeg', 'image/pjpeg', 'image/png'];
function file_type(file) {
	for(let i=0; i<fileTypes.length; i++) {
		if(file.type === fileTypes[i]) return true;
	}
	return false;
}
	
	
function varsFromHref(){
	var vars = [], hash;
	var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	for(var i = 0; i < hashes.length; i++)
	{
		hash = hashes[i].split('=');
		vars[i] = hash[1];
	}
return vars;
}

function reg_popUp(){
	let backg = document.createElement('div');
	backg.id = 'black-backg-popup';
	let cont = document.querySelector('body');
	cont.appendChild(backg);
	let wrap = document.createElement('div');
	wrap.id = 'reg-wrap';
	cont.appendChild(wrap);
	$(backg).animate({opacity:'0.7'});	
	load('interfaces/register-form.html', '#reg-wrap', function(){
		$('#reg-form-wrap').animate({top: '200px'});
		function close_reg_popUp(){
			$('#reg-form-wrap').animate({top: '830px'}, function(){	$(this).remove()});
			$(backg).animate({opacity: '0'}, function(){$(this).remove()});
		}
		$('#close-but').click(function(){close_reg_popUp()});
		let login = document.querySelector("input[name='reg-login']");
		let email = document.querySelector("input[name='reg-email']");
		let pass = document.querySelector("input[name='reg-pass']");
		let passRep = document.querySelector("input[name='reg-pass-rep']");
		
		$(login).keyup(function(){
			if(this.value.length >= 3 ){
				let that = this;
				$.post('is_login_exist.php', {login:that.value}, function(data){
					if(data=='yes') that.error('ten login jest już zajęty');
					if(data=='no') that.clrError();
				});
			}
		});
		
		$(login).focusout(function(){
			if(this.value.length =='' ) {this.error('To pole nie może być puste!')}
			if (this.value.length < 3) this.error('login musi składać się conajmniej z 3 znaków');
			
		});
		
		$(email).focusout(function(){
			if(this.value != ''){
				let reg = /^([\w\-\.])+\@([\w\-\.])+\.([A-Za-z]{2,4})$/;
				if(!reg.test(this.value)) this.error('błędny format adresu email');
				else{
					this.clrError();
					let that = this;
					$.post('is_email_exist.php', {email:that.value}, function(data){
						if(data=='yes') that.error('ten email jest już zarejestrowany');
						if(data=='no') that.clrError();
					});
				}
			} else this.error('Te pole nie może być puste!');
		});
		
		$(pass).focusout(function(){
			console.log('focus out');
			if(this.value.length == 0 ) {this.error('To pole nie może być puste!')}
			else if(!this.value.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z_!@#$%\^&*\d]{6,18}$/)){
				this.error("Hasło musi zawierać od 6 do 18 znaków, co najmniej jedną dużą literę, Cyfrę oraz może zawierać znaki: !@#$%\^&*_");
				console.log('spełnia nasz regexp');
				console.log('nasze value to: '+this.value)	
			}
			else if(this.name =='reg-pass-rep' && this.value.length >4 && this.value != passRep.value && passRep.value != '') {this.error('Hasła nie są takie same!')}
			
			else this.clrError();
			//debugger;
		});
		
		$(passRep).keyup(function(){
			if(this.value.length >4 && this.value != pass.value) {this.error('Hasła nie są takie same!')}
			if(this.value == pass.value) this.clrError();
		});
		
		$(passRep).focusout(function(){
			if(this.value == '') {this.error('To pole nie może być puste!')} 
			else if(this.value != pass.value) {this.error('Hasła nie są takie same!')}
			else{this.clrError()};
		});
			
		$('#register-form').submit(function(){
			event.preventDefault();
			let error;
			$('#reg-form-left [type="text"]').each(function(index, val){
				if(this.value == '') this.error('Te pole nie może być puste!');
				if(this.isError != false) error='yes';
			});
			if(error != 'yes'){
				//Processing();
				$.post('register.php', $('#register-form').serialize(), function(data, status){
					
					//closeProcessing();
					if (status == 'error') Xalert('error', 'Błąd!', 'Błąd połączenia z serwerem!');
					else if(status == 'timeout') Xalert('error', 'Błąd!', 'Przekroczono czas połączenia z serwerem!');
					else {
						if(data.indexOf('Link aktywacyjny został wysłany')>-1){
							close_reg_popUp();
							Xalert('info', 'Wymagana akcja!', data);
						}
						if(data.indexOf('Błąd przy wysyłaniu maila aktywacyjnego!')>-1){
							Xalert('error', 'Błąd!', data);
						}
						if(data.indexOf('błąd formularza!, próba wysłania')>-1){
							Xalert('error', 'Bład formularza!', 'próba wysłania formularza z niekompletnymi danymi.');
						};
					}
					
				});
			}
		});
		
	});
}

function mainMenu(){
	if(document.getElementById("category-container")){
		let menu_list = document.getElementById("category-container").childNodes[1].childNodes;
		for(let i=0; i<menu_list.length; i++){
			$(".submenu"+i).hide();
			$(".menu"+i).click(function(){
				$('#category-container ul>ul>li').stop().slideUp('fast');
				$(".submenu"+i).stop().slideDown('fast');
			});
		}
	}
}
function maxChars(input, limit){
	input.clrError();
	if (input.value.length>limit) {
		input.error('Te pole może zawierać maksymalnie '+limit+' znaków!');
		input.value = input.value.substr(0, limit);
	}
	
}
function isDigit(value){
	let reg = /[0-9]+$/;
	return (reg.test(value)) ?  true : false;	
}
function deleteIfNotDigit(field_id){
	let field = document.querySelector('#'+field_id);
	if (!isDigit(field.value)){
		field.value = field.value.substring(0,(field.value.length-1));
		field.error('Proszę wpisać wyłącznie cyfry!');
	}else field.clrError();
	
}
function deleteAdd(itemId){
	Xalert('confirm', 'Czy na pewno chcesz skasowac to ogłoszenie?', null, function(){
		
		$.post('lib.php',
			{
				action: 'deleteItem',
				itemId: itemId,
			},
			function(data, status){
				if(status=="success"){
					//window.location.href = 'profil.php?action=myAds';
					Xalert("success", "Sukces!", "Ogłoszenie o skasowane!\n", function(){ window.location.reload()});
				}else{
					Xalert("error", "Błąd!", "Problem z połączeniem :(");
				}
		});
	});
}

function extendAdd(id, expires){
	function time() {
		var timestamp = Math.floor(new Date().getTime() / 1000)
		return timestamp;
	}
	daysLeft = (expires > time()) ? (expires-time())/60/60/24 : 0 ;
	if(daysLeft<22){
		let content = '<select id="days-selected">';
		content+='<option value="7">7 dni</option>';
		if (daysLeft<14) content+='<option value="14">14 dni</option>';
		if (daysLeft<=0 )content+='<option value="28">28 dni</option>';
		content+='</select>';
		Xalert('confirm', 'Przedłuż ważność ogłoszenia', content, function(){
			let daysSelected = document.querySelector('#days-selected').value;
			//tutaj okienko myślenia
			popupWait();
			$.post('lib.php',
				{
					action: 'extendAdd',
					daysSelected: daysSelected,
					itemId: id
				},
				function(data,status){
					closePopupWait();
					if((status == "success") && (data.indexOf("Error")<0)){
						Xalert("success", "Sukces!", "Twoje ogłoszenie zostało przedłużone! "+data, function(){ window.location.href= 'profil.php?action=myAds'});
					}else{
						Xalert("error", "Błąd!", "Problem z połączeniem :(\nszczegóły: "+data);
					}
				}
			);
		});
	}else Xalert('error', 'Limit dni przekroczony!', 'Możesz przedłużyć ogłoszenie maksymalnie o miesiąc od dzisiejszego dnia.')
}
function showNavbarContent(id){
	cont = document.querySelector('#right-column #content').children;
	nav = document.querySelector('#right-column #navbar').children;
	for(let i=0; i<cont.length; i++){
		if(cont[i].id=='content-'+id) {
			cont[i].classList.remove('hidden');
			nav[i].classList.add('navSelected');
		}
		else {
			cont[i].classList.add('hidden');
			nav[i].classList.remove('navSelected');
		}
	}
}

function show_search_suggest(){
	let input = document.querySelector('input[name=search]');
	let input_val = input.value;
	let input_datalist = document.querySelector('#search-datalist');
	input_datalist.innerHTML='';
	let tab;
	$.get('search.php?suggest='+input_val, function(data){
		tab = data.split(';');
		for(let i=0; tab.length>i; i++){
			input_datalist.innerHTML += "<option value='"+tab[i]+"'>";;
		}
	});
}
function updateURLparameters(name, value){
	var regex_p = new RegExp("([?&])+page=([0-9])+");
	var query = window.location.search.replace(regex_p, "");
	var regex = new RegExp("([?;&])" + name + "[^&;]*[;&]?");
    query = query.replace(regex, "$1").replace(/&$/, '');
    let result = (query.length > 2 ? query + "&" : "?") + (value ? name + "=" + value : '');
	return window.location.pathname + result;
}
window.onload = function() {
	
	$('#add_ad_but').click(function(e){
		e.preventDefault();
		$.post('lib.php',
			{
				action: 'islogged'
			}, function(data){	
				if(data.logged == 'yes') location.replace('add_ad.php');
				else Xalert("info", "Niezalogowany", "Proszę się zalogować aby móc dodawać ogłoszenia.");
			},"json")
		
	});
	$('#login').on('submit', function(e){
		e.preventDefault();
		$.post('lib.php',
			{
				action : 'login',
				user : $("#login-form input[name='user']").val(),
				pass : $("#login-form input[name='pass']").val()
			},function(data){
				if(data.valid == 'no') Xalert("error", "Błąd", "Błędny login lub hasło!");
				if(data.valid == 'yes' && data.active == 'no') Xalert("info", "Info", "Konto nie jest jeszcze aktywowane, proszę kliknąć na link aktywacyjny wysłany na maila: "+data.email);
				if(data.valid == 'yes' && data.active == 'yes') location.reload();
			}, "json")
	})
	
	let search_form = document.querySelector('#search-form');
	if(search_form) {
		search_form.addEventListener('keyup', function(){show_search_suggest()});
	};

	let inputFiles = document.querySelectorAll('input[type="file"]');
	if(inputFiles){
		for(let i=0; i<inputFiles.length; i++){
			inputFiles[i].onchange = function(){update(i)}	
		}
	}
	let menu = document.getElementById('category-container');
	let sort = document.querySelector('#sort select[name="sort"]');
	if(sort) {
		sort.addEventListener('change', function(){
		window.location =  updateURLparameters(sort.name, sort.value)
		
		});
	}
	let max_on_page = document.querySelector('#sort select[name="max-on-page"]');
	if(max_on_page) {
		max_on_page.addEventListener('change', function(){
		window.location =  updateURLparameters(max_on_page.name, max_on_page.value)
		
		});
	}
	//if(menu) markMenu();
	let sel = document.getElementById('category-sel');
	if (sel) sel.onchange = function(){option(this.value)};
	let regBut = document.getElementById('reg-but');
	if (regBut) regBut.onclick = function(){reg_popUp()};
	let extendAddBut = document.getElementsByClassName('extend-add-but')
	if(extendAddBut) {$(extendAddBut).click(function(){extendAdd(this.id, this.dataset.expires)});}
	let navBar = document.querySelector('#right-column #navbar');
	if(navBar) $(navBar.children).click(function(){showNavbarContent(this.id)});
	let titleField = document.querySelector('#add_title');
	if(titleField) {titleField.addEventListener("keyup", function(){maxChars(titleField, 40)})};
	let descField = document.querySelector('#add_desc');
	if(descField) {descField.addEventListener("keyup", function(){maxChars(descField, 1000)})};
	let phoneField = document.querySelector('#add_phone');
	if(phoneField) {phoneField.addEventListener("keyup", function(){deleteIfNotDigit(this.id)})};
	let cityField = document.querySelector('#add_town');
	if(cityField) {cityField.addEventListener("keyup", function(){maxChars(cityField, 40)})};
	let deleteButs = document.querySelectorAll('.delete-add-but');
	if(deleteButs) $(deleteButs).click(function(){deleteAdd(this.id)});
	let itemPictures = document.querySelectorAll('#ad-bloc img');
	if(itemPictures) $(itemPictures).click(function(){lightbox(this)});
	let inListPctures = document.querySelectorAll('.post-left');
	if(inListPctures) $(inListPctures).each(function(){previewImg(this)});
// window.onbeforeunload = confirmExit;  ///odpowiada za potwierdzenie wyjscia ze strony
// function confirmExit(){    
// return "Czy na pewno chcesz wyjść ze strony ?";  
// }
}


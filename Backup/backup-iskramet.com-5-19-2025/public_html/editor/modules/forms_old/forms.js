document.write('<script src="/editor/e_modules/JsHttpRequest/lib/JsHttpRequest/JsHttpRequest.js"></script>');

function readMessage(id, catId){
	var tr=document.getElementById('tr'+id);
	var div=document.getElementById('div'+id);
	if(tr.style.display=='none'){
		tr.style.display='block';
		div.innerHTML='<td colspan=4><img src="/editor/img/loading.gif" width="18" height="18" align=left> Пожалуйста, подождите...</td>';
		document.body.style.cursor='wait';
		var req = new JsHttpRequest();
		req.onreadystatechange = function() {
			if (req.readyState == 4) {
				div.innerHTML=req.responseText;
				document.body.style.cursor='default';
			}
		}
		req.open(null, '/editor/modules/forms/getData.php', true);
		req.send( {id:id, cat:catId} );
	}else{
		tr.style.display='none';
	}
}

function deleteMessage(id){
	var ok=confirm("Уверены, что хотите удалить запись №"+id+"?");
	if(ok){
		document.delFrm.delId.value=id;
		document.delFrm.submit();
	}
}

function line_over(id){
	var tr=document.getElementById("string"+id);
	var td=tr.children;
	for(var i=0; i<td.length; i++){
		if(td[i].tagName=='TD')td[i].style.backgroundColor="#DEE7EF";
	}
	document.getElementById("img"+id).src="/editor/img/leftmenu_arrow.gif";
}
function line_out(id){
	var tr=document.getElementById("string"+id);
	var td=tr.children;
	for(var i=0; i<td.length; i++){
		if(td[i].tagName=='TD')td[i].style.backgroundColor="#CCDCE6";
	}
	document.getElementById("img"+id).src="/editor/img/spacer.gif";
}

function checkAll(form, name, mode){
	var el=form.elements;
	for(var i=0; i<el.length; i++){
		if(el[i].type=='checkbox' && el[i].name.indexOf(name,0)!=-1){
			el[i].checked=mode;
		}
	}
}

function isChecked(form, name){
	var el=form.elements;
	var count=0;
	for(var i=0; i<el.length; i++){
		if(el[i].type=='checkbox' && el[i].name.indexOf(name,0)!=-1){
			var ch=el[i].checked;
			if(ch==false)count++;
		}
	}
	return (count==0)?true:false;
}
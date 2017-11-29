
window.baseUri = getInnerHtml('baseUriHolder');
var xhr = new XMLHttpRequest();
var searchXhr = new XMLHttpRequest();


// General Functions

function getValue(div){
    return document.getElementById(div).value;
}

function getInnerHtml(div){
    return document.getElementById(div).innerHTML;
}


function setValue(div, value){
    document.getElementById(div).value = value;
}

function setInnerHtml(div, value){
    document.getElementById(div).innerHTML = value;
}

function getSelectValue(div){
    var e = document.getElementById(div);
    return e.options[e.selectedIndex].text;
}

function getElement(div){
    return document.getElementById(div);
}

function getDiv(div){
    return document.getElementById(div);
}

function disableBtn(btn){
    getElement(btn).setAttribute('disabled', 'disabled');
}

function enableBtn(btn){
    getElement(btn).removeAttribute('disabled');
}

function makeReadOnly(div){
    getElement(div).setAttribute('readonly', true);
}

function removeReadOnly(div){
    getElement(div).setAttribute('readonly', false);
}

function showDiv(div){
    document.getElementById(div).style.display = 'block';
}

function hideDiv(div){
    document.getElementById(div).style.display = 'none';
}

function makeVisible(div){
    document.getElementById(div).style.visibility = 'visible';
}

function makeInvisible(div){
    document.getElementById(div).style.visibility = 'hidden';
}

function allowNosOnly(value, div){
    if(isNaN(value)){
        document.getElementById(div).value = value.substring(0, value.length - 1);
        return false;
    }

}

function getLoader(msg=''){
    var loaderSrc = window.baseUri + '/assets/images/loader.gif';
    var loaderUrl = '<p style="text-align:center; margin:50px auto"><img src="' + loaderSrc + '"  /> &nbsp;&nbsp;' + msg + '</p>';
    return loaderUrl;
}


//General Alert
function showAlert(msg){
    swal({
        title: 'Error Alert!',
        text: msg,
        timer: 2000
    }).then(
        function () {
        },
        // handling the promise rejection
        function (dismiss) {
            if (dismiss === 'timer') {
                console.log('I was closed by the timer')
            }
        }
    )
}

function showActionAlert(params){

    swal({
        title: params.title,
        text: params.text,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4fa7f3',
        cancelButtonColor: '#d57171',
        confirmButtonText: 'Yes, Continue!'
    }).then(function () {

        params.callbackFunction(params.params);

        swal(
            'Operation Successfull!',
            params.successMsg,
            'success'
        )
    })

}

function formatNumber(number)
{
	if(number != '' && number !== null){
	    number = number.toFixed(2) + '';
	    x = number.split('.');
	    x1 = x[0];
	    x2 = x.length > 1 ? '.' + x[1] : '';
	    var rgx = /(\d+)(\d{3})/;
	    while (rgx.test(x1)) {
	        x1 = x1.replace(rgx, '$1' + ',' + '$2');
	    }
	    //return x1 + x2;
	    return x1;
	}else{ return ''; }
}

function sync(){
   showDiv('cover');
    var uri = window.baseUri + '/admin/sync';
    xhr.open('GET', uri, true);
        xhr.onload = function(e){
            if(xhr.status == 200){
                console.log('Sync successfull');
                hideDiv('cover');
                setTimeout(sync, 1000 * 60 * 15);
            }
        };
    xhr.send();

}

setTimeout(sync, 1000 * 60 * 15);

/********
 * My Custom Functions
 */


//enterKey press e.keyCode == 13

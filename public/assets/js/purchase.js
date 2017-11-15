function getItem(){

    var codeNo = getValue('codeNo');

    var uri = window.baseUri + '/stock/fetchStockItemByCodeNo/' + codeNo + '/stockCard';

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){

            if(this.responseText != ''){

                var params = JSON.parse(this.responseText);


                //setValue('codeNo', params.codeNo);
                setValue('codeNoHidden', params.codeNo);
                setValue('itemName', params.name);
                setValue('price', params.costPrice);

                setValue('qty', 1);

                getElement('qty').focus();
            }

            setInnerHtml('responseHolder', '');
            hideDiv('responseHolder');

            return false;

        }
    };
    xhr.send();

}


function fetchItem(searchQuery, fetchEmpty = true){

    // set codeNo to empty in case the was another items codeNo atteched to the codeNo inout element
    setValue('codeNo', '');

    //show response holder
    showDiv('responseHolder');
    setInnerHtml('responseHolder', getLoader('Loading Results...'));

    if(searchQuery.length == 0){
        setInnerHtml('responseHolder', '');
        hideDiv('responseHolder');
    }

    var uri = window.baseUri + '/stock/suggestByName/' + searchQuery + '/salesDocket/' + fetchEmpty;

    if(searchXhr){
        searchXhr.abort();
    }

    searchXhr.open('GET', uri, true);
    searchXhr.onload = function(e){
        if(searchXhr.status == 200){
            if(this.responseText == ''){
                // hide response holder
                hideDiv('responseHolder');
            }else{
                setInnerHtml('responseHolder', this.responseText);
            }

        }
    };
    searchXhr.send();
}


function setItem(params){


    setValue('codeNo', params.codeNo);
    setValue('codeNoHidden', params.codeNo);
    setValue('itemName', params.name);
    setValue('price', params.costPrice);

    setValue('qty', 1);

    getElement('qty').focus();

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

function mirrorValue(value, div){
    allowNosOnly(value, div);
    setValue('codeNoHidden', value);

}

function addItemToDocket(e, divObj){

    var code = (e.keyCode ? e.keyCode : e.which);
    var text = getValue(divObj.id);

    allowNosOnly(getValue(divObj.id), divObj.id);


    if(code == 13) { //if enter key is pressed

        addToDocket();


    }

}

function addToDocket(){

    var codeNo = getValue('codeNo');
    var price = getValue('price');
    var qty = getValue('qty');
    var itemName = getValue('itemName');

    if( codeNo == ''){
        showAlert('Enter Item Name or scan Barcode');
        getDiv('codeNo').focus();
        return;
    }

    if(itemName == ''){
        showAlert('Enter Item Name');
        getDiv('price').focus();
        return;
    }

    if(price == ''){
        showAlert('Enter Item cost Price');
        getDiv('price').focus();
        return;
    }

    if(qty == ''){
        showAlert('Enter Item Quantity Purchased');
        getDiv('qty').focus();
        return;
    }

    var form = new FormData();
    form.append('data', JSON.stringify({
        'codeNo' : codeNo,
        'itemName' : itemName,
        'price' : price,
        'qty' : qty
    }));

    var uri = window.baseUri + '/purchasing/addTemp';
    xhr.open('POST', uri, true);


    // set loading icon
    setInnerHtml('docketHolder', getLoader('Loading Docket...'));

    xhr.onload = function(e){
        if(xhr.status == 200){

            setInnerHtml('docketHolder', this.responseText);
            //showDiv('docketOptions');

            setValue('codeNo', '');

            setValue('itemName', '');
            setValue('price', '');
            setValue('qty', '');

            getElement('codeNo').focus();

        }
    };
    xhr.send(form);

}

function deleteDocketItem(itemId){

        //hideDiv('row'+itemId);

        var uri = window.baseUri + '/purchasing/deleteDocketItem/' + itemId;

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            setInnerHtml('docketHolder', this.responseText);
            showDiv('docketOptions');
        };
        xhr.send();
}


function clearDocket(value = ''){
    var uri = window.baseUri + '/purchasing/clearDocket';

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        setInnerHtml('docketHolder', '');
    };
    xhr.send();
}

function fetchPurchase(value, div){
    allowNosOnly(value, div);

    var uri = window.baseUri + '/purchasing/fetchPrevious/PUR-' + value;

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){
            console.log(this.responseText);
            if(this.responseText.trim() == 'not found'){

                makeVisible('formHolder');
                showDiv('addItemsToStockBtn');
                setInnerHtml('docketHolder', '');

                hideDiv('supplierOld');
                showDiv('supplier');
                removeReadOnly('date');

            }else{

                makeInvisible('formHolder');
                hideDiv('addItemsToStockBtn');
                setInnerHtml('docketHolder', this.responseText);

                (function(){
                    //fetch puchase details
                    var uri = window.baseUri + '/purchasing/fetchPreviousDetails/PUR-' + value;

                    xhr.open('GET', uri, true);
                    xhr.onload = function(e){

                        var response = JSON.parse(this.responseText);

                        hideDiv('supplier');
                        showDiv('supplierOld');
                        setValue('supplierOld', response.supplier);

                        makeReadOnly('date');
                        setValue('date', response.date);

                    };
                    xhr.send();

                })();
            }

        }
    };
    xhr.send();

}

function clearDocketAction(){
    showActionAlert({
        'title' : 'Clear Docket?',
        'text' : "You won't be able to revert this!",
        'callbackFunction' : clearDocket,
        'successMsg' : 'Docket successfully Cleared',
        'params' : ''
    });
}

$('#clearDocketBtn').click(function () {
    clearDocketAction();
});


$('#addItemsToStockBtn').click(function () {

    showActionAlert({
        'title' : 'Add Items to Stock?',
        'text' : "You won't be able to revert this!",
        'callbackFunction' : addItemsToStock,
        'successMsg' : 'Docket successfully Cleared',
        'params' : ''
    });
});


function addItemsToStock(value = ''){

    var supplier = getValue('supplier');
    var purchaseNo = 'PUR-' + getValue('purchaseNo');
    var date = getValue('date');

    var uri = window.baseUri + '/purchasing/addItemsToStock';

    var form = new FormData();
    form.append('data', JSON.stringify({
        'supplier' : supplier,
        'purchaseNo' : purchaseNo,
        'date' : date
    }));

    xhr.open('POST', uri, true);
    xhr.onload = function(e){
        setInnerHtml('docketHolder', this.responseText);
        hideDiv('docketOptions');
        hideDiv('docketHolderMain');
    };
    xhr.send(form);
}

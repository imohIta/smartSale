function getItem(){

    var codeNo = getValue('codeNo');

    var uri = window.baseUri + '/stock/fetchStockItemByCodeNo/' + codeNo + '/sales';

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
                setValue('total', 1 * parseInt(params.costPrice));
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
    setValue('total', 1 * parseInt(params.costPrice));
    getElement('qty').focus();

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

function mirrorValue(value, div){
    allowNosOnly(value, div);
    setValue('codeNoHidden', value);

}

function calculateTotal(e, divObj){

    var code = (e.keyCode ? e.keyCode : e.which);
    var qty = getValue(divObj.id);
    if(qty == ''){
        setValue('total', '');
    }else{

        var price = getValue('price');
        var discount = getValue('discount');

        allowNosOnly(qty, divObj.id);

        var total = parseInt(qty) * parseInt(price);

        // calculate discount amount
        var discountAmt = (parseInt(discount) / 100) * total;

        // calculate grand total
        var grandTotal = total - discountAmt;

        setValue('total', grandTotal);


        if(code == 13) { //if enter key is pressed

            addToDocket();


        }

    }

}

function calculateAndSubtractDiscount(e, divObj){

    var code = (e.keyCode ? e.keyCode : e.which);
    var discount = getValue(divObj.id);
    if(discount == ''){
        setValue('total', '');
    }else{

        var price = getValue('price');
        var qty = getValue('qty');

        allowNosOnly(discount, divObj.id);

        var total = parseInt(qty) * parseInt(price);

        // calculate discount amount
        var discountAmt = (parseInt(discount) / 100) * total;

        // calculate grand total
        var grandTotal = total - discountAmt;

        setValue('total', grandTotal);


        if(code == 13) { //if enter key is pressed

            addToDocket();


        }

    }

}

function addToDocket(){

    var codeNo = getValue('codeNo');
    var price = getValue('price');
    var qty = getValue('qty');
    var discount = getValue('discount');
    var itemName = getValue('itemName');
    var transId = 'INV-' + getValue('invioceNoHidden');
    var total = getValue('total');

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

    if(price == ''){
        showAlert('Enter Discount ( % )');
        getDiv('discount').focus();
        return;
    }

    var form = new FormData();
    form.append('data', JSON.stringify({
        'codeNo' : codeNo,
        'itemName' : itemName,
        'price' : price,
        'qty' : qty,
        'discount' : discount,
        'transId' : transId,
        'total' : total
    }));

    var uri = window.baseUri + '/sales/addToDocket';
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
            setValue('total', '');
            getElement('codeNo').focus();

            setInnerHtml('subTotal', getValue('subTotalHidden'));
            setInnerHtml('discount', getValue('totalDiscountHidden'));
            setInnerHtml('grandTotal', getValue('grandTotalHidden'));


        }
    };
    xhr.send(form);

}

function deleteDocketItem(itemId){

        //hideDiv('row'+itemId);

        var uri = window.baseUri + '/sales/deleteDocketItem/' + itemId;

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            setInnerHtml('docketHolder', this.responseText);

            setInnerHtml('subTotal', getValue('subTotalHidden'));
            setInnerHtml('discount', getValue('totalDiscountHidden'));
            setInnerHtml('grandTotal', getValue('grandTotalHidden'));
        };
        xhr.send();
}


function clearDocket(value = ''){
    var uri = window.baseUri + '/sales/clearDocket';

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        setInnerHtml('docketHolder', '');
        setInnerHtml('subTotal', 0);
        setInnerHtml('discount', 0);
        setInnerHtml('grandTotal', 0);
    };
    xhr.send();
}

function fetchPrevious(value, div){
    allowNosOnly(value, div);

    var uri = window.baseUri + '/sales/fetchPrevious/INV-' + value;

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

function addSale(value = ''){

    var docketCount = getValue('docketCount');
    if(docketCount == 0){

        showAlert('Sales Docket is Empty');
        return false;

    }else{

        var customerName = getValue('customerName');
        var customerAddr = getValue('customerAddr');
        var invioceNo = getValue('invioceNoHidden');
        var date = getValue('date');
        var shippingAddr = getValue('shippingAddr');
        var payType = getValue('payType');

        var uri = window.baseUri + '/sales/completeSale';

        var form = new FormData();
        form.append('data', JSON.stringify({
            'customerName' : customerName,
            'customerAddr' : customerAddr,
            'date' : date,
            'invioceNo' : invioceNo,
            'shippingAddr' : shippingAddr,
            'payType' : payType
        }));

        xhr.open('POST', uri, true);
        xhr.onload = function(e){
            setInnerHtml('docketHolder', '');
            setInnerHtml('subTotal', '0');
            setInnerHtml('discount', '0');
            setInnerHtml('grandTotal', '0');
            setValue('customerName', '');
            setValue('customerAddr', '');
            setValue('shippingAddr', '');
            setInnerHtml('docket', this.responseText);
            printInvioce();
            // hideDiv('docketOptions');
            // hideDiv('docketHolderMain');
        };
        xhr.send(form);
    }


}

function printInvioce(){

    var divText = document.getElementById("docket").outerHTML;
    setInnerHtml('docket', '');
    var myWindow = window.open('', '', 'width=450,height=800');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();
}

function checkIfItemInDocket(){
    var docketCount = getValue('docketCount');
    if(docketCount == 0){
        showAlert('Sales Docket is Empty');
        return false;
    }

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

function emailInvioce(){

    var docketCount = getValue('docketCount');
    if(docketCount == 0){
        showAlert('Sales Docket is Empty');
        return false;
    }else{


    }
}

function holdAndRecall(){
    var docketCount = getValue('docketCount');
    if(docketCount == 0){

        var onHoldTransactionsCount = getValue('onHoldTransactionsCount');

        if(onHoldTransactionsCount > 0){

            // recall transaction
            showDiv('myModal');

            // fetch transactionson-hold
            var uri = window.baseUri + '/sales/fetchTransactionsOnHold';

            xhr.open('GET', uri, true);
            xhr.onload = function(e){

                setInnerHtml('', this.responseText);

            };
            xhr.send();



            // var modal = getElement('myModal');
            // modal.classList.add('in');

        }else{
            showError('Sales Docket is Empty');
            return false;
        }


    }else{

        // hold transaction

        setInnerHtml('docketHolder', '');
        setInnerHtml('subTotal', '0');
        setInnerHtml('discount', '0');
        setInnerHtml('grandTotal', '0');
        setValue('customerName', '');
        setValue('customerAddr', '');
        setValue('shippingAddr', '');

        //increaseInvioceNumber();

    }
}

function dismissModal(){
    hideDiv('myModal');
}


$('#clearDocketBtn').click(function () {
    clearDocketAction();
});


$('#printInvioceBtn').click(function () {

    addSale();
});

$('#emailInvioceBtn').click(function () {

    emailInvioce();
});

$('#holdAndRecallBtn').click(function () {

    holdAndRecall();
});

$('#dismissModalBtn').click(function () {

    dismissModal();
});

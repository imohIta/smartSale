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
    var invoiceNo = getValue('invioceNoHidden');

    if(invioceNo == ''){
        var transId = '';
    }else{
        var transId = 'INV-' + invoiceNo;
    }

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

            setValue('docketCount', getValue('docketCountHidden'));

        }
    };
    xhr.send(form);

}

function deleteDocketItem(itemId){

        //hideDiv('row'+itemId);
        var transId = 'INV-' + getValue('invioceNoHidden');
        var uri = window.baseUri + '/sales/deleteDocketItem/' + itemId + '/' + transId;

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
    var transId = 'INV-' + getValue('invioceNoHidden');
    var uri = window.baseUri + '/sales/clearDocket/'+transId;

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
    var invioceNo = getValue('invioceNoHidden');

    if(invioceNo == value){

        var uri = window.baseUri + '/sales/fetchDocket/INV-'+invioceNo;
        xhr.open('GET', uri, true);
        xhr.onload = function(e){

            makeVisible('formHolder');
            // showDiv('actionsHolder');
            // showDiv('totals');
            showDiv('bottomDiv');
            setInnerHtml('docketHolder', this.responseText);

            removeReadOnly('date');
            removeReadOnly('shippingAddr');
            removeReadOnly('customerName');
            removeReadOnly('customerAddr');

            setValue('date', getValue('dateHidden'));
            setValue('shippingAddr', '');
            setValue('customerAddr', '');
            setValue('customerName', '');

            setInnerHtml('subTotal', getValue('subTotalHidden'));
            setInnerHtml('discount', getValue('totalDiscountHidden'));
            setInnerHtml('grandTotal', getValue('grandTotalHidden'));
        };
        xhr.send();

    }else{

        var uri = window.baseUri + '/sales/fetchPrevious/INV-' + value;

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            if(xhr.status == 200){
                //console.log(this.responseText);
                if(this.responseText.trim() == 'not found'){

                    makeVisible('formHolder');
                    // showDiv('actionsHolder');
                    // showDiv('totals');
                    showDiv('bottomDiv');
                    setInnerHtml('docketHolder', '');

                    removeReadOnly('date');
                    removeReadOnly('shippingAddr');
                    removeReadOnly('customerName');
                    removeReadOnly('customerAddr');

                    setValue('date', getValue('dateHidden'));
                    setValue('shippingAddr', '');
                    setValue('customerAddr', '');
                    setValue('customerName', '');

                    setInnerHtml('subTotal', 0);
                    setInnerHtml('discount', 0);
                    setInnerHtml('grandTotal', 0);

                }else{

                    makeInvisible('formHolder');
                    // hideDiv('actionsHolder');
                    // hideDiv('totals');
                    hideDiv('bottomDiv');
                    setInnerHtml('docketHolder', this.responseText);

                    (function(){
                        //fetch puchase details
                        var uri = window.baseUri + '/sales/fetchPreviousDetails/INV-' + value;

                        xhr.open('GET', uri, true);
                        xhr.onload = function(e){

                            var response = JSON.parse(this.responseText);

                            makeReadOnly('date');
                            makeReadOnly('shippingAddr');
                            makeReadOnly('customerName');
                            makeReadOnly('customerAddr');


                            setValue('shippingAddr', response.shippingAddr);
                            setValue('customerName', response.customerName);
                            setValue('customerAddr', response.customerAddr);
                            setValue('date', response.date);

                        };
                        xhr.send();

                    })();
                }

            }
        };
        xhr.send();


    }

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
            'invoiceNo' : invioceNo,
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

            setValue('invioceNo', parseInt(invioceNo) + 1);
            setValue('invioceNoHidden', parseInt(invioceNo) + 1);
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

                setInnerHtml('transHolder', this.responseText);

            };
            xhr.send();

            // var modal = getElement('myModal');
            // modal.classList.add('in');

        }else{
            showAlert('Sales Docket is Empty');
            return false;
        }


    }else{

        // hold transaction



        var invioceNo = getValue('invioceNoHidden');

        var url = window.baseUri + '/sales/holdTransaction/INV-' + invioceNo;

        xhr.open('GET', url, true);
        xhr.onload = function(e){

            var response = JSON.parse(this.responseText);

            setValue('invioceNo', '');
            setValue('invioceNoHidden', '');

            setValue('onHoldTransactionsCount', parseInt(getValue('onHoldTransactionsCount')) + 1);

        };
        xhr.send();

        setInnerHtml('docketHolder', '');
        setValue('docketCount', 0);
        setInnerHtml('subTotal', '0');
        setInnerHtml('discount', '0');
        setInnerHtml('grandTotal', '0');
        setValue('customerName', '');
        setValue('customerAddr', '');
        setValue('shippingAddr', '');



    }
}

function recallTransaction(invioceNo){


    // fetch traction using invioceNo
    var uri = window.baseUri + '/sales/recallTransaction/' + invioceNo;

    xhr.open('GET', uri, true);
    xhr.onload = function(e){

        setInnerHtml('docketHolder', this.responseText);

        setInnerHtml('subTotal', getValue('subTotalHidden'));
        setInnerHtml('discount', getValue('totalDiscountHidden'));
        setInnerHtml('grandTotal', getValue('grandTotalHidden'));

        setValue('docketCount', getValue('docketCountHidden'));

        setValue('invioceNo', getValue('transId'));
        setValue('invioceNoHidden', getValue('transId'));

        dismissModal();

    };
    xhr.send();

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


function printSalesDetails(){

    showDiv('reportSlip');
    var divText = document.getElementById("reportSlip-Holder").innerHTML;
    hideDiv('reportSlip');
    var myWindow = window.open('', '', 'width=750,height=800');
    var doc = myWindow.document;
    doc.open();
    doc.write(divText);
    doc.close();
    myWindow.focus();
    myWindow.print();
    myWindow.close();

}

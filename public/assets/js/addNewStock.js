try{

    //window.onload = function(){
    //    getElement('itemQuery').focus();
    //}


    // Smart Search
    var itemElement = getElement('itemQuery');
    itemElement.addEventListener('keyup', function(e){

        var searchQuery = itemElement.value;
        var code = (e.keyCode ? e.keyCode : e.which);

        if(code == 13) { //if enter key is pressed

            // setInnerHtml('responseHolder', this.responseText);
            // hideDiv('responseHolder');
            //
            // getElement('price').focus();
            showAlert('Please Select Item to want to Enter to Stock or Scan Item Barcode');

        }else {

            //alert('hey');

            //show response holder
            showDiv('responseHolder');
            setInnerHtml('responseHolder', getLoader('Loading Results...'));

            if (searchQuery.length == 0) {
                setInnerHtml('responseHolder', '');
                hideDiv('responseHolder');
            }

            var uri = window.baseUri + '/stock/suggestByName/' + searchQuery + '/purchaseDocket';

            if (searchXhr) {
                searchXhr.abort();
            }

            searchXhr.open('GET', uri, true);
            searchXhr.onload = function (e) {
                if (searchXhr.status == 200) {

                    setInnerHtml('responseHolder', this.responseText);
                }
            };
            searchXhr.send();
        }

    });


    // add item to Docket
    var addToDocketBtn = getElement('addToDocket');
    addToDocketBtn.addEventListener('click', function(){

        addItemsToDocket();
    });


    var myContainer = getElement('myContainer');
    myContainer.addEventListener('click', function(){
        hideDiv('responseHolder');

    });


    // var qtyInput = getElement('qty');
    // console.log(qtyInput);
    // qtyInput.addEventListener('keyup', function(e){
    //     alert('cjeck');
    //     //allowNosOnly()
    //     var qty = qtyInput.value;
    //     var code = (e.keyCode ? e.keyCode : e.which);
    //
    //     if(code == 13) {
    //         if(qty == ''){
    //             showAlert('Enter Item Quantity');
    //             return;
    //         }else{
    //             addItemsToStock();
    //         }
    //     }
    //
    // });




}catch(e){}

function addItemsToDocket(){

    var code = getValue('codeNo');

    var nameHolder = getElement('nameHolder');

    if(nameHolder.style.display == 'none'){
        var name = getValue('itemQuery');
    }else{
        var name = getValue('itemName');
    }

    var price = getValue('price');
    var qty = getValue('qty');
    var unit = getValue('unit');

    if(name == ''){
        showAlert('Please enter Item Name or scan Barcode');
        getDiv('name').focus();
        return;
    }


    if(price == ''){
        showAlert('Please enter Item Price');
        getDiv('price').focus();
        return;
    }

    if(qty == ''){
        showAlert('Please enter Item Quantity Purchased');
        getDiv('qty').focus();
        return;
    }

    var form = new FormData();
    form.append('data', JSON.stringify({
        'code' : code,
        'name' : name,
        'price' : price,
        'qty' : qty,
        'unit' : unit
    }));

    var uri = window.baseUri + '/stock/addTemp';
    xhr.open('POST', uri, true);

    showDiv('docketHolderMain');

    // set loading icon
    setInnerHtml('docketHolder', getLoader('Loading Docket...'));

    xhr.onload = function(e){
        if(xhr.status == 200){

            setInnerHtml('docketHolder', this.responseText);
            showDiv('docketOptions');

            setValue('codeNo', '');

            setValue('itemQuery', '');
            setValue('price', '');
            setValue('qty', '');

            getElement('itemQuery').focus();

            try {
                setValue('itemName', '');
                hideDiv('nameHolder');
                setValue('itemQuery', '');
            }catch(e){}

            getElement('itemQuery').focus();

        }
    };
    xhr.send(form);


}

function setItem(codeNo, itemName, price){

    showDiv('formHolder');

    setValue('itemQuery', itemName);
    setValue('price', price);
    setValue('codeNo', codeNo);

    getElement('qty').focus();

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

function getItem(){


    var codeNo = getValue('itemQuery');

    var uri = window.baseUri + '/stock/fetchStockItemByCodeNo/' + codeNo;

    showDiv('formHolder');


    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){

            var response = JSON.parse(this.responseText);
            if(response.itemName == ''){
                setValue('codeNo', codeNo);
                setValue('itemQuery', codeNo);

                setInnerHtml('responseHolder', '');
                hideDiv('responseHolder');

                if(isNumeric(codeNo)){

                    showDiv('nameHolder');
                    getElement('itemName').focus();
                }

            }else {

                setValue('itemName', '');
                hideDiv('nameHolder');

                setValue('itemQuery', response.itemName);
                setValue('price', response.price);
                setValue('codeNo', response.itemCode);
                hideDiv('responseHolder');
                getElement('qty').focus();
            }

            return false;

        }
    };
    xhr.send();
    return false;
}


function fetchDocketItem(itemId){


    var uri = window.baseUri + '/stock/fetchDocketItemById/' + itemId;


    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){

            var response = JSON.parse(this.responseText);

            hideDiv('mainFormsHolder')
            showDiv('editFormHolder');

            setValue('codeNo2', response.itemCode);
            setValue('name2', response.itemName);
            setValue('price2', response.price);
            setValue('qty2', response.qty);
            //document.getElementById('price').value = response.price;
        }
    };
    xhr.send();
}

function editDocketItem(){

    var code = getValue('codeNo2');
    var name = getValue('name2');
    var price = getValue('price2');
    var qty = getValue('qty2');
    var unit = getValue('unit2');



    if(name == ''){
        alert('Please enter Item Name');
        getDiv('name2').focus();
        return;
    }


    if(price == ''){
        alert('Please enter Item Price');
        getDiv('price2').focus();
        return;
    }

    if(qty == ''){
        alert('Please enter Item Quantity Purchased');
        getDiv('qty2').focus();
        return;
    }

    var form = new FormData();
    form.append('data', JSON.stringify({
        'code' : code,
        'name' : name,
        'price' : price,
        'qty' : qty,
        'unit' : unit
    }));

    var uri = window.baseUri + '/stock/editDocketItem';
    xhr.open('POST', uri, true);

    // set loading icon
    setInnerHtml('docketHolder', getLoader('Loading Docket...'));

    xhr.onload = function(e){
        if(xhr.status == 200){

            setInnerHtml('docketHolder', this.responseText);
            showDiv('docketOptions');

            setValue('codeNo2', '');
            setValue('name2', '');
            setValue('price2', '');
            setValue('qty2', '');

            try {
                setValue('itemQuery', '');
            }catch(e){}

            hideDiv('editFormHolder');
            showDiv('mainFormsHolder');

        }
    };
    xhr.send(form);

}


function deleteDocketItem(itemId){

        //hideDiv('row'+itemId);

        var uri = window.baseUri + '/stock/deleteDocketItem/' + itemId;

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            setInnerHtml('docketHolder', this.responseText);
            showDiv('docketOptions');
        };
        xhr.send();
}

function addItemsToStock(){

    var uri = window.baseUri + '/stock/addItemsToStock';

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        setInnerHtml('docketHolder', this.responseText);
        hideDiv('docketOptions');
        hideDiv('docketHolderMain');
    };
    xhr.send();
}

function clearDocket(){

    var uri = window.baseUri + '/stock/clearDocket';

    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        setInnerHtml('docketHolder', this.responseText);
        hideDiv('docketOptions');
        hideDiv('docketHolderMain');
        getElement('itemQuery').focus();
    };
    xhr.send();
}


//Add Items to Stock
$('#addItemsToStock').click(function () {

    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4fa7f3',
        cancelButtonColor: '#d57171',
        confirmButtonText: 'Yes, Continue!'
    }).then(function () {

        addItemsToStock();

        swal(
            'Operation Successfull!',
            'Docket Items has been successfully added to Stock.',
            'success'
        )
    })
    getElement('itemQuery').focus();
});

//Clear Stock Docket
$('#clearDocketBtn').click(function () {


    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4fa7f3',
        cancelButtonColor: '#d57171',
        confirmButtonText: 'Yes, Continue!'
    }).then(function () {

        clearDocket();

        swal(
            'Operation Successfull!',
            'Docket has been succesfully cleared.',
            'success'
        )
    })
    getElement('itemQuery').focus();
});

// clear sales Docket
$('#clearSalesDocketBtn').click(function () {


    swal({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4fa7f3',
        cancelButtonColor: '#d57171',
        confirmButtonText: 'Yes, Continue!'
    }).then(function () {

        clearDocket();

        swal(
            'Operation Successfull!',
            'Docket has been succesfully cleared.',
            'success'
        )
    })
});

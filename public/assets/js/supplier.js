function setSupplier(supplierId){
    var uri = window.baseUri + '/supplier/fetchSupplierInfo/' + supplierId;

    makeVisible('formHolder');
    setInnerHtml('formHolder', getLoader());

    if(supplierId == 0){

        makeInvisible('formHolder');

    }else{

        makeVisible('formHolder');

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            if(xhr.status == 200){

                var response = JSON.parse(this.responseText);

                setValue('id', response.id);
                setValue('name', response.name);
                setValue('address', response.address);
                setValue('email', response.email);
                setValue('phoneNo', response.phone);

            }
        };
        xhr.send();
    }


}

function fetchPurchaseHistory(supplierId){

    var uri = window.baseUri + '/supplier/purchaseHistory/' + supplierId;

    makeVisible('purchaseHolder');
    setInnerHtml('purchaseHolder', getLoader());

    if(supplierId == 0){

        makeInvisible('purchaseHolder');

    }else{

        makeVisible('purchaseHolder');

        xhr.open('GET', uri, true);
        xhr.onload = function(e){
            if(xhr.status == 200){

                setInnerHtml('purchaseHolder', this.responseText);

            }
        };
        xhr.send();
    }


}

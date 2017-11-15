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
                setValue('wholesalePrice', params.wholesalePrice);
                setValue('costPrice', params.costPrice);
                setValue('retailPrice', params.retailPrice);
                setValue('tax', params.tax);
                //setValue('groupId', params.groupId);

                showDiv('infoHolder');

                if(parseInt(params.qtyInStock) < 5){
                    setInnerHtml('qtyInStock', '<span class="label label-danger" style="font-size:14px">' + params.qtyInStock + '</span>');
                }else{
                    setInnerHtml('qtyInStock', params.qtyInStock);
                }

                setInnerHtml('lastSoldDate', params.lastSoldDate);
                setInnerHtml('lastPurchaseDate', params.lastPurchaseDate);
                setInnerHtml('lastCostPrice', '=N= ' + params.lastCostPrice);
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
    //setValue('codeNo', '');

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
    setValue('wholesalePrice', params.wholesalePrice);
    setValue('costPrice', params.costPrice);
    setValue('retailPrice', params.retailPrice);
    setValue('tax', params.tax);
    //setValue('groupId', params.groupId);

    showDiv('infoHolder');

    if(parseInt(params.qtyInStock) < 5){
        setInnerHtml('qtyInStock', '<span class="label label-danger" style="font-size:14px">' + params.qtyInStock + '</span>');
    }else{
        setInnerHtml('qtyInStock', params.qtyInStock);
    }

    setInnerHtml('lastSoldDate', params.lastSoldDate);
    setInnerHtml('lastPurchaseDate', params.lastPurchaseDate);
    setInnerHtml('lastCostPrice', '=N= ' + params.lastCostPrice);

    //makeReadOnly('codeNo');

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

function mirrorValue(value, div){
    allowNosOnly(value, div);
    setValue('codeNoHidden', value);

}

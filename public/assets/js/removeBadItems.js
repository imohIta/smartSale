function fetchItem(){

    var searchQuery = getValue('itemQuery');

    setValue('codeNo', '');

    //show response holder
    showDiv('responseHolder');
    setInnerHtml('responseHolder', getLoader('Loading Results...'));

    if(searchQuery.length == 0){
        setInnerHtml('responseHolder', '');
        hideDiv('responseHolder');
    }

    var uri = window.baseUri + '/stock/suggestByName/' + searchQuery + '/removeItem/' +  true;

    if(searchXhr){
        searchXhr.abort();
    }

    searchXhr.open('GET', uri, true);
    searchXhr.onload = function(e){
        if(searchXhr.status == 200){

            setInnerHtml('responseHolder', this.responseText);
        }
    };
    searchXhr.send();
}

function getItem(){


    var codeNo = getValue('itemQuery');

    var uri = window.baseUri + '/stock/fetchStockItemByCodeNo/' + codeNo;

    showDiv('formHolder');


    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){

            var response = JSON.parse(this.responseText);


            setValue('itemQuery', response.itemName);
            showDiv('stockQtyHolder');
            setValue('stockQty', response.qty);
            setValue('codeNo', response.itemCode);

            setInnerHtml('responseHolder', '');
            hideDiv('responseHolder');

            getElement('qty').focus();

            return false;

        }
    };
    xhr.send();
    return false;
}


function setItem(params){

    showDiv('formHolder');

    setValue('itemQuery', params.name);
    hideDiv('stockQtyHolder');
    setValue('codeNo', params.codeNo);

    getElement('qty').focus();

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

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

    var uri = window.baseUri + '/stock/suggestByName/' + searchQuery + '/changeItemPrice';

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
            showDiv('priceHolder');
            setValue('oldPrice', response.price);
            setValue('codeNo', response.itemCode);

            setInnerHtml('responseHolder', '');
            hideDiv('responseHolder');

            getElement('newPrice').focus();

            return false;

        }
    };
    xhr.send();
    return false;
}


function setItem(codeNo, itemName, price){

    showDiv('formHolder');

    setValue('itemQuery', itemName);
    showDiv('priceHolder');
    setValue('codeNo', codeNo);
    setValue('oldPrice', price);

    getElement('newPrice').focus();

    setInnerHtml('responseHolder', '');
    hideDiv('responseHolder');

}

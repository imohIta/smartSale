
function editUser(userId){


    var uri = window.baseUri + '/account/fetchUser/' + userId;


    xhr.open('GET', uri, true);
    xhr.onload = function(e){
        if(xhr.status == 200){

            var response = JSON.parse(this.responseText);

            hideDiv('usersList');
            showDiv('editForm');

            setValue('userId', response.id);
            setValue('name', response.name);
            setValue('username', response.username);
            setValue('privilege', response.privilege);
        }
    };
    xhr.send();
}

function confirmDeleteUser(userId){
    showActionAlert({
        'title' : 'Delete User Account?',
        'text' : "You won't be able to revert this!",
        'callbackFunction' : editUser,
        'successMsg' : 'User Account successfully Deleted',
        'params' : userId
    });
}

/**
 * TESTCLIENT FOR FLOPPYDISK API
 */

// API URL <<IMPORTANT!>> PLEASE SET THE URL TO MATCH THE FILE-LOCATION ON YOUR SYSTEM
var baseURL = 'http://localhost/webb6/public/index.php?api=1'; // API-PARAMETER REQUIRED 

// Returns a sign up/in form if not signed in, else API return error message to sign in [GET] | [POST]
baseURL += '&login_page=1';


// Add new user: 'register=true&username=<username>&password=<password>' [POST]
// Sign in user: 'login=true&username=<username>&password=<password>' [POST]
// Sign out user: 'logout=true' [GET]
var user = {
    get LOGIN() {
        return 'login=true';
    },
    get REGISTER() {
        return 'register=true';
    }
}

var fileList = [];


function init() {

    // Sign up/in function
    login(user.LOGIN);

}

// login(<type>, <username>, <password>);
function login( type, username, password) {

    username = username || 'no_name_given';
    password = password || 'no_password_given';

    var action = '&' +type + '&username=' + username + '&password=' + password;
    ajax('POST', action, getFileData);

}

function getFileData( json ) {
    
    if(json['data'] && json['data'][0]) {

        fileList = [];
        var list = document.createElement('ul');
        list.id  = 'list';
        document.body.appendChild(list);
        
        for(var i = 0, item, files = json['data']; i < files.length; i++) {
            item = document.createElement('li');
            item.innerHTML = files[i]['file_name'];
            item.setAttribute('data-ix', i);
            item.onclick = onFileChosen;
            list.appendChild(item);
            fileList.push(files[i]);
        }
    
    }
    
}

function onFileChosen() {

    var file = fileList[this.getAttribute('data-ix')];

    var id, name = file['file_name'];

    // ID is required to (down)load shared files without authorization   
    id = file['owner_id'];

    // Download/Share/Delete/Load file (authorization required)
    getFile(name, false, parseHTML);

    // Download shared file (no authentication required)
    // getFile(name, id);

    // Load shared file (no authorization required)
    // getFile(name, id, parseHTML);
}


// getFile(<file_name>, <owner_id:optional>, <callback:optional>);
function getFile( name, id, callback ) {
    
    // Download: '&download=<file_name>' (authorization required)
    // Load: '&load=<file_name>' (authorization required)
    // Delete: '&delete=<file_name>' (authorization required)
    // Share: '&share=<file_name>' (authorization required)
    // Download shared file: '&download=<file_name>&id=<owner_id>' (no authorization required)
    // Load shared file: '&load=<file_name>&id=<owner_id>' (no authorization required)
    // Load files by category: '&category=<audio|image|video|other>' (authorization required)

    var url;

    if(callback) {
        url = '&load=' + name;
        callback = callback;
    } else {
        url = '&share=' + name;
        callback = setModification;
    }
        
    if(id) url += '&id=' + id;
    
    ajax('GET', url, callback);

}


/**
 * 
 *  YOU CAN PRETTY MUCH IGNORE THE CODE BELOW
 * 
 */


function parseHTML( json ) {
    
    var oldElem = document.getElementById('activeMedia');
    if(oldElem) document.body.removeChild(oldElem);
    
    parser = new DOMParser();
    xmlDoc = parser.parseFromString(json['data'],"text/html");
    var newElem = xmlDoc.body.children[0];
    newElem.id  = 'activeMedia';

    if(newElem.getElementsByClassName('login')[0]) {
        setSignupForm(newElem);
    } else document.body.appendChild(newElem);

}

function setModification( json ) {
        
    document.body.innerHTML = (json['data']) ? json['data'] : json['error'];

    if(!document.getElementsByClassName('action_URL')[0]) return; 
    var a = document.getElementsByClassName('action_URL')[0];
    a.click();
    a.parentNode.removeChild(a);

}

function setSignupForm( elem ) {

    var win = window.open();
    var loginForm = elem.getElementsByClassName('login')[0];
    
    loginForm.login.onclick = function() {
        login(user.LOGIN, loginForm.username.value, loginForm.password.value);
        win.close();
    };

    win.document.body.appendChild(elem);

}


function ajax(type, params, callback) {
    
    var request;

    if(XMLHttpRequest){
        request = new XMLHttpRequest();
    } else if (ActiveXObject){
        request = new ActiveXObject("Microsoft.XMLHTTP");
    } else{
        console.error("Denna webbläsare saknar stöd för kunna att köra denna sida."); 
        return false;
    }

    request.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(callback) callback(JSON.parse(this.responseText));
        }
    };

    if(type == 'GET') {
        request.open(type, baseURL + params, true);
        request.send();
    } else {
        request.open(type, baseURL);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send(params);
    }

}

window.onload = init;



if(location.href.indexOf('&share') > -1) {
    location.href = location.href.slice(0, location.href.indexOf('&share'));
} else if(location.href.indexOf('?logout') > -1) {
    location.href = location.href.slice(0, location.href.indexOf('?logout'));
}

function removeBtn( host, path ){
    location.assign(host + 'public/index.php?' + path);
}
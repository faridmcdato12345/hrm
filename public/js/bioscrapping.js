const rp = require('request-promise');

const url = 'http://127.0.0.1:84/';

rp(url).then(function(html){
    console.log(html);
})
.catch(function(err){
    console.log("error: " + err);
});
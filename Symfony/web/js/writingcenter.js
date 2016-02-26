
$(document).ready(function(){
    // Register any DOM templates.
    // This will automaticall add any <script type="text/html" /> blocks.
    $.Mustache.addFromDom();
});

function getUsernamesLike(username, cb){
    // $username is a string that will be used to search database.
    // e.g. where username LIKE %$username% 
    
    $.ajax({
          type: "POST",
          dataType: "json",
          url: Routing.generate('get_usernames_like'),
          data: { username: username}
    }).done(cb);
}

function getUsernamesLikeFirstLastNames(username, first, last, cb){
    // $username is a string that will be used to search database.
    // e.g. where username LIKE %$username% 
    
    $.ajax({
          type: "POST",
          dataType: "json",
          url: Routing.generate('get_usernames_like_first_last_names'),
          data: { username: username, first: first, last: last}
    }).done(cb);
}

function getLabSchedule(cb){
  $.ajax({
      type: "POST",
      dataType: "json",
      url: Routing.generate('get_lab_schedule'),
  }).done(cb);
}


// Quick way to check the type of an object. Remove it later.
var toType = function(obj) {
  return ({}).toString.call(obj).match(/\s([a-zA-Z]+)/)[1].toLowerCase()
}

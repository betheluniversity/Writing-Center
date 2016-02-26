  //-------------------------------
  //These functions are for the 'promote-user' page.
  //-------------------------------
  $(".promote-input").change(function(){
     username = $("#username").val();
     first = $("#first").val();
     last= $("#last").val();
     getUsernamesLikeFirstLastNames(username, first, last, function(data){
        $("#results").html($.Mustache.render('promotion-table', {users: data}));
        updatePromoteHandlers();
      });
  });

  function updatePromoteHandlers(){
    $( ".btn-promote" ).click(function(){
       // $username = $("#username").val();
       // $url=$("#usernameForm").attr("action");
        numID = this.id.replace(/\D/g,''); //get only the numbers (numerical ID)
        // Use this to get the username and new role.
        username = $("#username" + numID).text();
        role = $("#new-role" + numID).children()[0].value;
        // lets check to make sure role isn't "" before sending to server
        if (role == ""){
            $("#update-user-error-space").html('<div class="alert alert-error"> Please select a new role </div>');
            // lets gtfo before the ajax starts
            return;
        }
        $.ajax({
          type: "POST",
          url: Routing.generate('wc_cm_promote_update'),
          data: { username: username, role: role}
        }).done(function ( data ) {
            //Now that the role is updated, lets trigger the other callback to update the table
            $("#username").change();
            $("#update-user-error-space").html('<div class="alert alert-success"> System Settings updated successfuly </div>');
            $("#update-user-error-space").fadeOut(5000);
        });
    });
  }
  //-------------------------------
  // End 'promote-user'
  //-------------------------------


  //-------------------------------
  // Adjust System Settings (system_settings)
  //-------------------------------
    $("#sys-settings-edit").click(function(){
        apptLimit = $("#apptLimit").val();
        timeLimit = $("#timeLimit").val();
        banLimit = $("#banLimit").val();
        qualtricsLink = $("#qualtricsLink").val();
        $("#apptLimit").attr("readonly", false);
        $("#timeLimit").attr("readonly", false);
        $("#banLimit").attr("readonly", false);
        $("#qualtricsLink").attr("readonly", false);
        $("#sys-settings-cancel").show();
        $("#sys-settings-submit").show();
        $("#sys-settings-edit").hide();
    });
    $("#sys-settings-cancel").click(function(){
        $("#apptLimit").attr("readonly", true);
        $("#timeLimit").attr("readonly", true);
        $("#banLimit").attr("readonly", true);
        $("#qualtricsLink").attr("readonly", true);
        $("#apptLimit").val(apptLimit);
        $("#timeLimit").val(timeLimit);
        $("#banLimit").val(banLimit);
        $("#qualtricsLink").val(qualtricsLink);
        $("#sys-settings-cancel").hide();
        $("#sys-settings-submit").hide();
        $("#sys-settings-edit").show();
    });
    $("#sys-settings-submit").click(function(){
        apptLimit = $("#apptLimit").val();
        timeLimit = $("#timeLimit").val();
        banLimit = $("#banLimit").val();
        qualtricsLink = $("#qualtricsLink").val();
        $.ajax({
            type: "POST",
            url: Routing.generate('wc_cm_system_settings_update'),
            data: { apptLimit: apptLimit, timeLimit: timeLimit, banLimit: banLimit, qualtricsLink: qualtricsLink}
          }).done(function ( data ) {
            // Get the updated values to confirm something changed
            // and update the textfields
            $("#sys-settings-error-space").show();
            $("#sys-settings-error-space").html('<div class="alert alert-success"> System Settings updated successfully </div>');
            $("#sys-settings-error-space").fadeOut(5000);
            $("#apptLimit").val(data['apptLimit']);
            $("#timeLimit").val(data['timeLimit']);
            $("#banLimit").val(data['banLimit']);
            $("#qualtricsLink").val(data['qualtricsLink']);
            $("#apptLimitDescription").html('Description: Each student can sign up for <span style="font-weight:bold; color: red">' + data['apptLimit'] + '</span> appointments in a week.');
            $("#timeLimitDescription").html('Description: Each appointment closes sign-ups <span style="font-weight:bold; color: red">' + data['timeLimit'] + '</span> hours before the start time.');
            $("#banLimitDescription").html('Description: Each appointment closes sign-ups <span style="font-weight:bold; color: red">' + data['banLimit'] + '</span> hours before the start time.');
            $("#qualtricsLinkDescription").html('Description: Here is the link that will be given after each appointment.');
            $("#sys-settings-cancel").click();
          });
      });
  //-------------------------------
  // end Adjust System Settings (system_settings)
  //-------------------------------


  //-------------------------------
  // Create schedule view and supporting methods (schedule)
  //-------------------------------

  function registerCallbacks(){
    $( ".schedule-row" ).on('click', addScheduleRowClick);
    $("#scheduleCancelEdit").on('click', scheduleCancelButtonClick);
    $("#scheduleSaveButton").on('click', scheduleSaveButtonClick);
    $("#scheduleAddButton").on('click', scheduleAddButtonClick);
    $("#scheduleCancelSubmit").on('click', scheduleCancelSubmitClick);
    $("#scheduleSubmitButton").on('click', scheduleSubmitButtonClick);
    $("#scheduleDeleteButton").on('click', scheduleDeleteButtonClick);
  }

  function addScheduleRowClick(){
        //Make day of week editable
        $.cookie("rowID", this.id);
        $.cookie("originalEditHTML", this.innerHTML);

        //Make active editable
        var activeHTML = $(this).find('.active')[0];
        var value = activeHTML.innerHTML;
        activeHTML.innerHTML = $("#active-select")[0].outerHTML;
        var select = $($(activeHTML).children()[0]);
        select.show();
        select.val(value);

        // Timepickers
        var timeStart = $(this).find('.timeStart');
        var timeEnd = $(this).find('.timeEnd');
        
        var timeStartCurrent = timeStart[0].innerHTML;
        var timeEndCurrent = timeEnd[0].innerHTML;

        timeStart.html($("#newTimeStart").clone().attr('id', 'tempNewTimeStart').val(timeStartCurrent).show());
        timeEnd.html($("#newTimeEnd").clone().attr('id', 'tempNewTimeEnd').val(timeEndCurrent).show());


        $('#tempNewTimeStart').timepicker({
          showPeriod: true,
        });
        $('#tempNewTimeEnd').timepicker({
          showPeriod: true,
        });

        // show and hide button groups
        $("#schedule-cs-btns").show();
        $("#schedule-add-btn").hide();

        //Get rid of the onclick to ensure we are only editing one row at a time.
        $( ".schedule-row" ).off();
  }

  function scheduleSaveButtonClick(){
      var row = $("#"+$.cookie("rowID"));
      var id = $.cookie("rowID").replace(/\D/g,'');
      var start = row.find('#tempNewTimeStart').val();
      var end = row.find('#tempNewTimeEnd').val();
      var active = row.find('.active option:selected').val();

      $.ajax({
        type: "POST",
        url: Routing.generate('wc_cm_schedule_call_update'),
        data: { row:id, start:start, end:end, active:active}
      }).done(function (data) {

        getLabSchedule(function ( data ) {
          $("#schedule-content").html($.Mustache.render('schedule-table', {schedule: data}));
          registerCallbacks()
          $("#"+$.cookie("rowID")).addClass("alert alert-success");
          $("#update-user-error-space").html('<div class="alert alert-success"> Updated Successfuly </div>');
          $("#update-user-error-space").stop(true, true).show().fadeOut(5000, function(){
              //$("#"+$.cookie("rowID")).removeClass("alert alert-success");
           });
        }); 
      });
      // // now that the row is updated, reload the table. 
      // // Reloading ensures we are displaying the same info that is the db.
      // loadScheduleHTML();
  }

  function scheduleAddButtonClick(){
    $("#schedule-add-btn").hide();
    $("#schedule-cs-add-btns").show();
    $('#newTimeStart').timepicker({
      showPeriod: true,
    });
    $('#newTimeEnd').timepicker({
      showPeriod: true,
    });
    $('#addScheduleForm').children().each(
      function(){
          $(this).show();
        }
    );
  } 

  function scheduleSubmitButtonClick(){

    var dayOfWeek = $("#addScheduleForm").find('#day-select option:selected').val();

    //Get values from text fields
    var start = document.getElementById("newTimeStart").value;
    if(start == ''){

      $("#update-user-error-space").html('<div class="alert alert-success"> No Time Specified. Please try again. </div>');
       $("#update-user-error-space").stop(true, true).show();
      return;
    }
   // timeStartAMPM = $("#newStartAMPM").find(":selected").text();
    //start = timeStart + " " + timeStartAMPM;
    var end = document.getElementById("newTimeEnd").value;
    if(end == ''){
      $("#update-user-error-space").html('<div class="alert alert-success"> No Time Specified. Please try again. </div>');
       $("#update-user-error-space").stop(true, true).show();
      return;
    }
    //Cannot have the end time be before the start time.
      // if(start >= end)
      // {
      //   $("#update-user-error-space").html('<div class="alert alert-success"> Enter a valid time range. The end time is before the start time. </div>');
      //   return;
      // }
    var isActive = $("#active-select").find(":selected").text();
    $.ajax({
        type: "POST",
        url: Routing.generate('wc_cm_schedule_call_add'),
        data: {dayOfWeek:dayOfWeek, start:start, end:end, isActive:isActive}
      }).done(function (data) {
          var rowID = data;
          getLabSchedule(function ( data ) {
            $("#schedule-content").html($.Mustache.render('schedule-table', {schedule: data}));
            registerCallbacks()
            $("#schedule-row"+rowID).addClass("alert alert-success");
            $("#update-user-error-space").html('<div class="alert alert-success"> Updated Successfuly </div>');
            $("#update-user-error-space").stop(true, true).show().fadeOut(5000, function(){
                //$("#"+$.cookie("rowID")).removeClass("alert alert-success");
             });
          }); 
      });
    
  }

  function scheduleCancelSubmitClick(){
    $("#schedule-add-btn").show();
    $("#schedule-cs-add-btns").hide();
    $("#day-select").val("Monday");
    $("#active-select").val("Yes");
    $('#newTimeStart').val("");
    $('#newTimeEnd').val("");
    $('#addScheduleForm').children().each(
      function(){
          $(this).hide();
        }
    );
  }

  function scheduleDeleteButtonClick(){
      var row = $("#"+$.cookie("rowID"));
      var id = $.cookie("rowID").replace(/\D/g,'');
      $.ajax({
        type: "POST",
        url: Routing.generate('wc_cm_schedule_call_remove'),
        data: {id:id}
      }).done(function (data) {
         getLabSchedule(function ( data ) {
            $("#schedule-content").html($.Mustache.render('schedule-table', {schedule: data}));
            registerCallbacks()
            $("#update-user-error-space").html('<div class="alert alert-success"> Updated Successfuly </div>');
            $("#update-user-error-space").stop(true, true).show().fadeOut(5000, function(){
                //$("#"+$.cookie("rowID")).removeClass("alert alert-success");
            });
          }); 
      });
  }


  function scheduleCancelButtonClick(){
      var row = $.cookie("rowID");
      var html = $.cookie("originalEditHTML");
      $('#' + row).html(html);
      // The current edit was canceled, so we can enable editing again
      $( ".schedule-row" ).on('click', addScheduleRowClick);

      //buttons
      $("#schedule-cs-btns").hide();
      $("#schedule-add-btn").show();
  }

  function loadScheduleHTML(){
      getLabSchedule(function ( data ) {
          $("#schedule-content").html($.Mustache.render('schedule-table', {schedule: data}));
          //Register a ton of callbacks
          registerCallbacks()
      });
  }

  loadScheduleHTML();

  //-------------------------------
  // end Create schedule view and supporting methods (schedule)
  //-------------------------------



//---------------------------------
  // begin manage_tutors
  //---------------------------------


  //Load the datepicker jquery
  $( "#start-datepicker" ).datepicker();
  $( "#end-datepicker" ).datepicker();

  //Remove entry when remove button is called
  //Use a function because these are loaded dynamically
    function createRemoveButtonCallback(){
      $(".btn-remove-schedule-entry").click(function(){
         numID = this.id.replace(/\D/g,'');
         rowID = $("#row-id-" + numID).text();
          $.ajax({
            type: "POST",
            url: Routing.generate('wc_cm_manage_tutors_call_remove'),
            data: { rowID: rowID}
          }).done(function ( data ) {
            $("#lookup-tutor-username").change();
            $("#lookup-tutor-error-space").show();
            $("#lookup-tutor-error-space").fadeOut(3000);
            $("#lookup-tutor-error-space").html('<div class="alert alert-success">Removed Slot Successfully</div>');
          });
        });
    }

    function createUpdateButtonCallback(){
      $(".btn-update-select").click(function(){
         numID = this.id.replace(/\D/g,'');
         rowID = $("#update-row-id-" + numID).text();
         username = $("#update-select-" + numID).find('select').find(':selected').text();
         if (username == "-select-"){
            $("#update-tutor-error-space").show();
            $("#update-tutor-error-space").html('<div class="alert alert-error">Select a valid username</div>');
            return;
         }
          $.ajax({
            type: "POST",
            url: Routing.generate('wc_cm_manage_tutors_call_update'),
            data: { rowID: rowID, username: username}
          }).done(function ( response ) {
            username = response['username'];
            $("#tutor-username-" + numID).text(username);
            $("#tutor-username-" + numID).val(username);
            $("#update-tutor-error-space").show();
            $("#update-tutor-error-space").html('<div class="alert alert-success">Updated Successfully</div>');
            $("#update-tutor-error-space").fadeOut(3000);
          });
        });
    }

  $("#start-datepicker").val('');
  $("#end-datepicker").val('');
  
  // Lookup user when the textbox changes
  // ?? Where is the method manageTutorsCallLookup ??
  $("#lookup-tutor-username").change(function(){
     username = $("#lookup-tutor-username").val();
      $.ajax({
        type: "POST",
        url: Routing.generate('wc_cm_manage_tutors_call_lookup'),
        data: { username: username}
      }).done(function ( data ) {
          $("#lookup-tutor-results").html(data);
          createRemoveButtonCallback();
      });
    });

  // Lookup user when the textbox changes
  // ?? Where is the method manageTutorsCallUpdateLookup ??
  $("#update-tutor-username").change(function(){
     username = $("#update-tutor-username").val();
      $.ajax({
        type: "POST",
        url: Routing.generate('wc_cm_manage_tutors_call_update_lookup'),
        data: { username: username}
      }).done(function ( data ) {
          $("#update-tutor-results").html(data);
          createUpdateButtonCallback();
      });
    });

    $("#manage-tutor-submit").click(function(){
        startdate = $("#start-datepicker").val();
        enddate = $("#end-datepicker").val();
        if(startdate == "" || enddate == ""){
          $("#manage-results").html('<div class="alert alert-error">Please choose valid times.</div>');
          $("#manage-results").stop(true, true).show();
          return;
        }

        time = $("#timeselect").val();
        if(time == null){
          $("#manage-results").html('<div class="alert alert-error">Please choose a time slot.</div>');
          $("#manage-results").stop(true, true).show();
          return;
        }

        tutors = $("#tutorselect").val();
        selected = $(":selected");
        multilingual = $("#MultilingualSelect").val();
        if(multilingual == "Yes")
          multilingual = true;
        else
          multilingual = false;
        dropIn = $("#DropInSelect").val();
        if(dropIn == "Yes")
          dropIn = true;
        else
          dropIn = false;
        days = new Array();

        // selected.each(function(index, value){
        //   days.push(value.id);
        // });
        days = $("#manage_tutors_days").val()
        if(days == null){
          $("#manage-results").html('<div class="alert alert-error">Please choose a day.</div>');
          $("#manage-results").stop(true, true).show();
          return;
        }
        $.ajax({
          type: "POST",
          url: Routing.generate('wc_cm_manage_tutors_call'),
          data: { startdate:startdate, enddate:enddate, time:time, tutors:tutors, days:days, multilingual:multilingual, dropIn:dropIn }
        }).done(function ( data ) {
            $("#manage-results").html('<div class="alert alert-success">Added appointments successfully</div>');
            $("#manage-results").stop(true, true).show().fadeOut(5000);
        });
    });


  //---------------------------------
  // end manage_tutors
  //---------------------------------
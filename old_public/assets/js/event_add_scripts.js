$( document ).ready(function() {
    event_add_calendar();

//Event add dates form submit
$('.add_event_dates').on('click', function(e) {

    if (!$('#eventform_add').valid()) {
     
    }else{

        var dates = $('input[name="event_dates"]').val();
        if(dates != ''){
            var formData = $("#eventform_add").serialize();
            var form=$("#eventform_add");
            $.ajax({
                type:"POST",
                /* url:form.attr("action"),*/
                url:BASE_URL+"/recurringevent",
                data:formData,
                success: function(response){
                    var dates = $('input[name="event_dates"]').val();

                    var allDate = dates.split(',');
                    var newDatesArr = [];
                    allDate.forEach(function(item) {
                        newDatesArr.push(item.replace("-", "/").replace("-", "/"));
                    });                     
                    newDates = newDatesArr.join(",");
                    $('input[name="old_event_dates"]').val(function() {
                        if(this.value != ''){
                            return this.value + ',' + newDates;
                        }else{
                            return newDates;
                        }

                    });

                    var data = JSON.parse(JSON.stringify(response));
                    var eventData = data.data;
                    $('input[name="update_event_id"]').val(eventData.id);
                    $('#add_event_calendar').fullCalendar( 'refetchEvents' );

                    $('input[name="event_dates"]').val('');
                    $( "#event_dates_cal" ).datepicker("destroy");
                    $('#event_dates_cal').datepicker({
                        defaultDate: null,
                        startDate: new Date(),
                        multidate: true,
                        format: "dd-mm-yyyy",
                        language: 'en',
                            //datesDisabled: ['02/06/2021','05/06/2021']
                            datesDisabled: $('input[name="old_event_dates"]').val()
                        }).on('changeDate', function(e) {
                            $('#event_dates').val(' ' + e.dates);
                        });
                        
                        toastr.success('Event dates added succesfully.');
                    }
                });
        }else{
            $('.date-error-cls').text("Please select date");
        }
    }

});


function event_add_calendar(){
    var fullCalendar = $('#add_event_calendar').fullCalendar({
        showNonCurrentDates: false,
        height: 600,
        defaultView: 'month',
        eventRender: function(event, element, view) {
            element.find(".fc-title").remove();
            var events = event.title.split("~");
            var new_description = events[0] + '</br>' + events[1];
            element.append(new_description);
        },

        events: function(start, end, timezone, callback) {
            scout_app.getEventfunc(callback);
        },
        eventClick: function(data, event, view) {

            $('#editeventstartdate').pickadate({
                min: true,
            });
            var todayDate = moment(new Date).format('YYYY-MM-DD');
            var startdate = moment(data.start['_d']).format('YYYY-MM-DD');
            var segments = location.pathname.split('/');
            if (segments != undefined) {
                var action1 = segments[1];
                var action2 = segments[2];
            }
            if (todayDate <= startdate) {
                var id = data.id;
                var eventcode = data.eventcode;
                
               /* if(data.updatePublish == '0'){
                    return false;
                }*/
                if (id == $("input[name='update_event_id']").val()) {

                    $("#action_event").modal('show');
                    $('.event-id-cls').val(id);
                    $.ajax({
                        type: 'GET',
                        url: BASE_URL + '/recurringeditevent/edit/' + id + '/' + startdate,
                        success: function(response) {
                            $('.delete-btn-event').attr('data-id', response.response.eventScheduleData.id);
                            $('.event-schedule-id-cls').val(response.response.eventScheduleData.id);

                            $('#editeventstartdate').val(moment(response.response.eventScheduleData.date).format('DD MMMM, YYYY'))
                            $('#editeventstarttime').val(response.response.eventScheduleData.start_time);
                            $('#editeventendtime').val(response.response.eventScheduleData.end_time);
                            $('#edittotaleventhours').val(response.response.eventScheduleData.event_hours);
                            $('#occurs [value=' + response.response.occurs + ']').attr('selected', 'true');
                            $('select#status [value=' + response.response.status + ']').attr('selected', 'true');
                            var html = scout_app.changeeditMonthlyOccurs(response.response.startdate);
                            $(".occur-monthly-edit-cls").html(html);
                            $(".occur-monthly-clsss").remove();
                            if (response.response.occurs == 'Daily') {
                                $(".dailyoccurs-cls").show();
                                $(".monthlyoccurs").hide();
                                $(".weeklyoccurs").hide();
                                $('select.occurs [value=' + response.response.occurs + ']').attr('selected', 'true');
                            } else if (response.response.occurs == 'Weekly') {
                                $('select.occurs [value=' + response.response.occurs + ']').attr('selected', 'true');
                                $(".weeklyoccurs").show();
                                $(".dailyoccurs-cls").show();
                                $(".monthlyoccurs").hide();
                                var weeklysplit = response.response.occurs_weekly.split(',');
                                $.each(weeklysplit, function(index, value) {
                                    $(".weeklychkcls[value=" + value + "]").prop("checked", "true");
                                });

                            } else if (response.response.occurs == 'Monthly') {
                                $('select.occurs [value=' + response.response.occurs + ']').attr('selected', 'true');
                                var explode_monthly = response.response.occurs_monthly.split("/");
                                $(".monthlyoccurs").show();
                                $(".dailyoccurs-cls").show();
                                $(".weeklyoccurs").hide();
                                if (explode_monthly[1] == "month") {
                                    montly_date = explode_monthly[0];
                                    $('select.editmonthlycls [value="' + montly_date + '/month"]').attr('selected', 'true');
                                } else if (explode_monthly[1] == "weekday") {
                                    var startdate = response.response.startdate;
                                    var enddate = response.response.enddate;
                                    montly_date = explode_monthly[0];
                                    montly_day = explode_monthly[2];
                                    var result = '';
                                    if (montly_date == 'first') {
                                        monthday = 0;
                                    } else if (montly_date == 'second') {
                                        monthday = 7;
                                    } else if (montly_date == 'third') {
                                        monthday = 14;
                                    } else if (montly_date == 'fourth') {
                                        monthday = 21;
                                    } else if (montly_date == 'fifth') {
                                        monthday = 28;
                                    }
                                    var result = scout_app.monthweekday(startdate, enddate, montly_day, monthday);
                                    $(".weekmonthday").val(result);
                                    $('select.editmonthlycls [value="' + montly_date + '/weekday/' + montly_day + '"]').attr('selected', 'true');
                                }
                            } else {
                                $('select.occurs [value=' + response.response.occurs + ']').attr('selected', 'true');
                                $(".dailyoccurs-cls").hide();
                                $(".monthlyoccurs").hide();
                                $(".weeklyoccurs").hide();
                            }
                            if (response.response.enddate != null) {
                                $('#editeventenddate').val(moment(response.response.enddate).format('DD MMMM, YYYY'));
                            } else {
                                $('#editeventenddate').val('');
                            }
                        }
                    });
}
}
},
viewRender: function(currentView) {
    var minDate = moment();
    if (minDate >= currentView.start && minDate <= currentView.end) {
        $(".fc-prev-button").prop('disabled', true);
        $(".fc-prev-button").addClass('fc-state-disabled');
        $(".fc-past").prop('disabled', true);
        $(".fc-past").addClass('fc-state-disabled');
        $(".fc-day-grid-event").css('background-color', '#D3D3D3');
        $(".fc-day-grid-event").css('border-color', '#D3D3D3');
        $(".fc-day-grid-event").css('color', '#000');
    } else {
        $(".fc-prev-button").removeClass('fc-state-disabled');
        $(".fc-prev-button").prop('disabled', false);
    }
}
});
}

});
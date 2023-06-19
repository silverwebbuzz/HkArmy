$(function() {
    scout_app.init();
    scout_datatable.init();
    scout_validation.init();
    scout_login.init();
    scout_delete.init();
    scout_event.init();

    $("#cover-spin").hide();

    /** Manoj Added Code **/
    $('.deleteImage').on('click', function(e) {
        //$('#cover-spin').show();
        var id = $(this).attr("id");
        var imageName = $(this).attr("imageName");
        $(this).closest('.pip').remove();
        $.ajax({
            type: 'POST',
            url: BASE_URL+"/member/deleteDocument",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                id:id,
                imageName:imageName
            },
            success: function (response) {
                console.log(response);
                //$('#cover-spin').hide();
            }
        });
    });

    //Expired date datepicker use in token adjustment
    $('#expired_at').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        minYear: 1950,
        minDate:new Date(),
        maxYear: parseInt(moment().format('YYYY'), 10)
    });

    //$('.addMember').on('click', function(e) {
        $(document).on('click', '.addMember', function() {
            $('input[name=eventModal]').val('');
            var event_id = $(this).attr("modal-event-id");
            var event_name = $(this).attr("modal-event-name");
            $('.modal-event-name').html(event_name);
            $('input[name=eventModal]').val(event_id);
            $.ajax({
                type: 'POST',
                url: BASE_URL+"/member/eventmeber",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    event_id:event_id
                },
                success: function (response) {
                    var select = $('select[name= membermodal]');
                    select.empty();
                    select.append('<option value="">select member</option>');
                    test = response.member;
                    $.each(test,function(key, value) {
                        select.append('<option value=' + value.ID + '>' + value.English_name + '</option>');
                    });
                }
            });
        });



        $(document).on('click', '.assignMemberToEvent', function() {
            var eventID = $('input[name=eventModal]').val();
            var memberID = $("[name='membermodal']").children("option:selected").val();
            if (memberID != '') {
                $.ajax({
                    type: 'POST',
                    url: BASE_URL+"/member/assignMember",
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        eventID:eventID,
                        memberID:memberID
                    },
                    success: function(response) {
                        $("#cover-spin").hide();
                        location.reload();
                    }
                });
            }else{
                toastr.error("Please select member.");
            }

        });

//Generate automatic event code as selection of event type
$(document).on('change', '.eventTypeForCode', function() {
    var eventTypeID = $(this).val();
    if (eventTypeID != '') {
        $.ajax({
            type: 'POST',
            url: BASE_URL+"/event/generateEventCode",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                eventTypeID:eventTypeID
            },
            success: function(response) {
             $('input[name=event_code]').val(response.eventCode);
         }
     });
    }else{
        toastr.error("Please select event.");
    }

});


    /**
    * USE : On change graph options
    **/
    $(document).on('click', '.graph-options', function() {
        var GraphTypes = $(this).attr('type');
        var months = '';
        var usrestext = '';
        var userarr = [];
        var usersBarChartOptions = '';
        var usersBarChart ='';
        
        $("#cover-spin").show();
        // Send ajax call and fetch data
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/filterGraph",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'GraphType': GraphTypes,
            },
            success: function(response) {

                console.log('resp',response);
                $("#cover-spin").hide();

                var months = response.months;
                var usrestext = response.usrestext;

                var usersBarChartOptions = {
                    chart: {
                        height: 260,
                        type: 'bar',
                        toolbar: {
                            show: false
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '20%',
                            endingShape: 'rounded'
                        },
                    },
                    legend: {
                        horizontalAlign: 'right',
                        offsetY: -10,
                        markers: {
                            radius: 50,
                            height: 8,
                            width: 8
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#5A8DEE', '#E2ECFF'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'light',
                            type: "vertical",
                            inverseColors: true,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 70, 100]
                        },
                    },
                    series: [{
                        name: (new Date).getFullYear(),
                        data: response.userarr
                    }],
                    xaxis: {
                        categories: months,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#828D99'
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 300,
                        tickAmount: 3,
                        labels: {
                            style: {
                                color: '#828D99'
                            }
                        }
                    },
                    legend: {
                        show: false
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + " " + usrestext
                            }
                        }
                    }
                }
                var usersBarChart = new ApexCharts(
                    document.querySelector(".user-count-year"),
                    usersBarChartOptions
                    );
                usersBarChart.render();                
            }
        });

    });

    /*Dashboard JS START*/
    if ($(".user-count-year").length) {
        if ($('html').is(':lang(ch)')) {
            var months = ['一月', '二月', '三月', '四月', '可能', '君', '七月', '八月', '九月', '十月', '十一月', '十二月'];
            var usrestext = '用户数';
        } else {
            var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var usrestext = 'Users';
        }
        var usersBarChartOptions = {
            chart: {
                height: 260,
                type: 'bar',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '20%',
                    endingShape: 'rounded'
                },
            },
            legend: {
                horizontalAlign: 'right',
                offsetY: -10,
                markers: {
                    radius: 50,
                    height: 8,
                    width: 8
                }
            },
            dataLabels: {
                enabled: false
            },
            colors: ['#5A8DEE', '#E2ECFF'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "vertical",
                    inverseColors: true,
                    opacityFrom: 1,
                    opacityTo: 1,
                    stops: [0, 70, 100]
                },
            },
            series: [{
                name: (new Date).getFullYear(),
                data: userarr
            }],
            xaxis: {
                categories: months,
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: '#828D99'
                    }
                }
            },
            yaxis: {
                min: 0,
                max: 300,
                tickAmount: 3,
                labels: {
                    style: {
                        color: '#828D99'
                    }
                }
            },
            legend: {
                show: false
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " " + usrestext
                    }
                }
            }
        }
        var usersBarChart = new ApexCharts(
            document.querySelector(".user-count-year"),
            usersBarChartOptions
            );
        usersBarChart.render();
    }


    
    

    if ($("#user-count-chart").length) {
        var usercount = $('#user-count-chart').attr('data-users');
        scout_app.homecountdata(usercount, '#39DA8A', '#user-count-chart');
    }
    if ($("#attendance-count-chart").length) {
        var usercount = $('#attendance-count-chart').attr('data-attedance');
        scout_app.homecountdata(usercount, '#00CFDD', '#attendance-count-chart');
    }
    if ($("#event-count-chart").length) {
        var usercount = $('#event-count-chart').attr('data-event');
        scout_app.homecountdata(usercount, '#FDAC41', '#event-count-chart');
    }
    
    if ($("#mentor-user-count-chart").length) {
        var usercount = $('#mentor-user-count-chart').attr('data-mentor-users');
        scout_app.homecountdata(usercount, '#39DA8A', '#mentor-user-count-chart');
    }
    if ($("#elite-user-count-chart").length) {
        var usercount = $('#elite-user-count-chart').attr('data-elite-users');
        scout_app.homecountdata(usercount, '#39DA8A', '#elite-user-count-chart');
    }
    if ($("#district-user-count-chart").length) {
        var usercount = $('#district-user-count-chart').attr('data-district-users');
        scout_app.homecountdata(usercount, '#39DA8A', '#district-user-count-chart');
    }
    /*Dashboard JS END*/

    if ($('#dob').length) {
        $('#dob').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            minYear: 1950,
            maxYear: parseInt(moment().format('YYYY'), 10)
        });
        $('#dob').on('apply.daterangepicker', function(ev, picker) {
            dob = new Date($(this).val());
            var today = new Date();
            var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
            $('#age').val(age);
        });
        dob = new Date($('#dob').val());
        var today = new Date();
        var age = Math.floor((today - dob) / (365.25 * 24 * 60 * 60 * 1000));
        $('#age').val(age);
    }

    if ($(".filterdateofBirth").length) {
        $('.filterdateofBirth').daterangepicker({
            showDropdowns: true,
            minYear: 1950,
            maxYear: parseInt(moment().format('YYYY'), 10),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filterdateofBirth').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
    }

    if ($(".filterrank").length) {
        $('.filterrank').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filterrank').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
    }

    if ($(".filterlastactivity").length) {
        $('.filterlastactivity').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filterlastactivity').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
    }

    if ($(".filterjoindate").length) {
        $('.filterjoindate').daterangepicker({
            showDropdowns: true,
            minYear: 1950,
            maxYear: parseInt(moment().format('YYYY'), 10),
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filterjoindate').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
    }

    if ($('#filterrealtedactivity').length) {
        if ($('html').is(':lang(ch)')) {
            var noneselected = '請選擇';
        } else {
            var noneselected = 'None selected';
        }
        $("#filterrealtedactivity").multiselect({
            nonSelectedText: noneselected,
        });
    }
    if ($('#filterspecialty').length) {
        if ($('html').is(':lang(ch)')) {
            var noneselected = '請選擇';
        } else {
            var noneselected = 'None selected';
        }
        $("#filterspecialty").multiselect({
            nonSelectedText: noneselected,
        });
    }

    $(document).on('click', '.filterrealtedactivity-submit', function() {
        $("#cover-spin").show();
        var form = $('#filterDateForm');
        form.submit();
    });

    if ($(".filter_date_event").length) {
        $('.filter_date_event').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filter_date_event').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
        });
    }

    if ($(".filter_date_attendance").length) {
        $('.filter_date_attendance').daterangepicker({
            /*ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
            },*/
            singleDatePicker: true,
            alwaysShowCalendars: true,
            autoUpdateInput: false,
        });

        $('.filter_date_attendance').on('apply.daterangepicker', function(ev, picker) {
            //$(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            $(this).val(picker.startDate.format('MM/DD/YYYY'));

            //$("#cover-spin").show();
            $.ajax({
                type: 'POST',
                url: BASE_URL + "/attendance-event-list-search",
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    filter_date_attendance_event: $(this).val(),
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    $(".search-filter-cls").html(response);
                    $(".published_event_cls").hide();
                }
            });
        });
    }

    if ($('#status_member').length) {
        $(document).on('change', '#status_member', function() {
            var user_id = $(this).data('id');
            var value = $(this).val();
            //$("#cover-spin").show();
            $.ajax({
                type: 'POST',
                url: BASE_URL + '/update-status',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    user_id: user_id,
                    value: value,
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    var object = JSON.parse(JSON.stringify(response));
                    if (object.status) {
                        toastr.success(object.message);
                        location.reload();
                    } else {
                        toastr.error(object.message);
                    }
                }
            });
        });
    }

    $.validator.addMethod("remote_valid", function(value, element, jdata) {
        var x = $.ajax({
            type: "POST",
            url: jdata.url,
            async: false,
            dataType: "json",
            data: {
                "_token": $('#csrf-token').val(),
                query: jdata.query
            },
        }).responseText;
        return (x === 'false') ? false : true;
    }, function(value, element) {
        return value.msg
    });

    $.validator.addMethod('extension', function(value, element, param) {
        if ($("#Attachment").get(0).files.length != 0) {
            console.log("123")
            for (var i = 0; i < $("#Attachment").get(0).files.length; ++i) {
                var file1 = $("#Attachment").get(0).files[i].name;
                if (file1) {
                    var file_size = $("#Attachment").get(0).files[i].size;
                    var ext = file1.split('.').pop().toLowerCase();
                    if ($.inArray(ext, ['jpg', 'jpeg', 'png', 'PNG', 'JPG', 'JPEG', 'pdf', 'csv', 'xls', 'xlsx', 'xlsm', 'gif', 'docx', 'docm', 'doc', 'dotx', 'dotm', 'dot']) === -1) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }
    });

    $.validator.addMethod('filesize', function(value, element, param) {
        if ($("#Attachment").get(0).files.length != 0) {
            for (var i = 0; i < $("#Attachment").get(0).files.length; ++i) {
                var file1 = $("#Attachment").get(0).files[i].name;
                if (file1) {
                    var file_size = $("#Attachment").get(0).files[i].size;
                    if (file_size > 1048576) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        } else {
            return true;
        }
    });

    $.validator.addMethod("greaterThan",
        function(value, element, params) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) >= new Date($(params).val());
            }
            return isNaN(value) && isNaN($(params).val()) ||
            (Number(value) > Number($(params).val()));
        }, 'Must be greater than Startdate.');

    $(document).on('focusout', '#contact_number', function() {
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/check-contact-number",
            data: {
                '_token': $('#csrf-token').val(),
                contact_number: $('#contact_number').val(),
            },
            success: function(response) {
                if (response == 'false') {
                    $('#warning').modal('show');
                }
            }
        });
    });

    $(document).on('click', '.serach-events-cls', function() {
        $("#cover-spin").show();
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/event-list-search",
            data: {
                '_token': $('#csrf-token').val(),
                filter_date_event: $('#filter_date_event').val(),
                filter_event_type: $('#filter_event_type').val(),
                filter_occurs: $('#filter_occurs').val(),
            },
            success: function(response) {
                $("#cover-spin").hide();
                $(".event-search-list-cls").html(response);
                $("#search-eventtable").dataTable();
                $("#eventtable").hide();
            }
        });
    });

    $(document).on('focusout', '#edit_contact_number', function() {
        $.ajax({
            type: 'POST',
            url: BASE_URL + "/check-contact-number",
            data: {
                '_token': $('#csrf-token').val(),
                edit_contact_number: $('#edit_contact_number').val(),
                user_id: $('.user_id').attr('data-id'),
            },
            success: function(response) {
                if (response == 'false') {
                    $('#warning').modal('show');
                }
            }
        });
    });

    $(document).on('keyup', '#hour_point', function() {
        var hkdrate = $(this).attr('data-rate');
        var hourpoint = this.value;
        var total = hourpoint * hkdrate;
        $('#hour_point_rate').val(total);
    });

    /* USE : MEMBER QR CODE GENERATE ATTENDANCE */
    $(document).on("click", ".qrBTN", function() {
        let tr = $(this).closest('tr');
        var id = $(this).closest('tr').attr('id');
        $("#cover-spin").show();
        $.ajax({
            type: "GET",
            url: BASE_URL + "/generateQRCode/" + id,
            data: {},
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.url) {
                    //window.open(BASE_URL + '/' + data.url, '_blank');
                    window.open(data.url,'_blank');
                } else {

                }
            }
        });
    });

    /* USE : RECORD ATTENDANCE */
    $(document).on("click", "#recordAttend", function() {
        var validation = $("#attendEvent").valid();
        if (validation == true) {
            var event_id = $("#event_id").val();
            $(".membership-code-cls").hide();
            $('.currentevent').val(event_id);
            $('.attendances_type').val('1');
            $('.scheduleID').val($('.attendance_event_id option:selected').attr('data-event-schedule'));
            $('#eventAttend').modal('show');
            $(".qr-attend-login-cls").show();
        }
        if (validation == false) {
            $('#eventAttend').modal('hide');
        }
    });

    /* USE : ATTENDANCE LOGOUT */
    $(document).on("click", "#eventLogout", function() {
        var validation = $("#attendEvent").valid();
        if (validation == true) {
            var event_id = $("#event_id").val();
            $(".membership-code-logout-cls").hide();
            $('.currentevent').val(event_id);
            $('.attendances_type').val('2');
            $('.scheduleID').val($('.attendance_event_id option:selected').attr('data-event-schedule'));
            $('#eventLogoutModal').modal('show');
            $(".qr-attend-logout-cls").show();
        }
        if (validation == false) {
            $('#eventLogoutModal').modal('hide');
        }
    });

    $(document).on('click', '.qrattendance-cls', function() {
        $(".qr-attend-login-cls").show();
        $(".qr-attend-logout-cls").show();
        $(".membership-code-cls").hide();
        $(".membership-code-logout-cls").hide();
    });

    $(document).on('click', '.membership-cls', function() {
        $(".qr-attend-login-cls").hide();
        $(".membership-code-cls").show();
        $(".qr-attend-logout-cls").hide();
        $(".membership-code-logout-cls").show();
    });
    $(document).on("click", '.purchase-product-clear', function() {
        if ($("#seacrch-purchase-productTable").length) {
            location.reload();
        }
    });
    /*USE : EVENT ATTENDER LIST */
    $(document).on("change", "#event_id", function() {
        var event_id = $("#event_id").val();
        $("#cover-spin").show();
        if (event_id != '') {
            $.ajax({
                type: "GET",
                url: BASE_URL + "/getEventAttenderList/" + event_id,
                data: {},
                success: function(response) {
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    if (data.list) {
                        var list = data.list;
                        $("#tbodyid").empty();
                        $('#attendanceTable tbody').html(list);
                    } else {
                        $("#tbodyid").empty();
                        $('#attendanceTable tbody').html('<tr class="odd"><td valign="top" colspan="8" class="dataTables_empty">No data available in table</td></tr></tr>');
                    }
                }
            });
        } else {
            $("#cover-spin").hide();
        }
        $("#tbodyid").empty();
        $('#attendanceTable tbody').html('<tr class="odd"><td valign="top" colspan="8" class="dataTables_empty">No data available in table</td></tr></tr>');
    });

    $(document).on('click', '.event_report_search', function() {
        $("#cover-spin").show();
        $.ajax({
            type: "POST",
            url: BASE_URL + "/event-report-search",
            data: $("#event_report_form").serialize(),
            success: function(response) {
                $("#cover-spin").hide();
                $('.event-report').hide();
                $('#attendanceReporttable_wrapper').remove();
                $('.event-serach-data-cls').html(response);
                $("#attendanceSerachReporttable").DataTable({
                    bAutoWidth: false,
                });
            }
        });
    });

    $(document).on('change', '#eventserachtype', function() {
        $("#cover-spin").show();
        $.ajax({
            type: "POST",
            url: BASE_URL + "/event_type_serach",
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'eventtype': this.value,
                'user_id': $('.eventtypeserach').attr('data-user-id'),
            },
            success: function(response) {
                $("#cover-spin").hide();
                $('.activity-cls').hide();
                $('.count-attendance').hide();
                $('.count-attendance-cls').html(response.countattendance)
                $(".activity-serach-cls").html(response.html);
                $(".load-more-viewattendance").attr('last_id', response.last_id);

                if (response.last_id == 0) {
                    $(".load-more-viewattendance").hide();
                } else {
                    $(".load-more-viewattendance").show();
                }
            }
        });
    });

    $(document).on('click', '.load-more-viewattendance', function() {
        var attendanceId = $('.load-more-viewattendance:last').attr('last_id');
        if (attendanceId != 0) {
            $("#cover-spin").show();
            $.ajax({
                type: 'POST',
                url: BASE_URL + '/ajax_LoadMoreattendanceList',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'attendanceId': attendanceId,
                    'user_id': $('.eventtypeserach').attr('data-user-id'),
                    'eventtype': $('#eventserachtype').val(),
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    $(".load-more-viewattendance").attr('last_id', response.last_id);
                    $('.activity-cls').append(response.html);
                    if (response.last_id == 0) {
                        $(".load-more-viewattendance").hide();
                    } else {
                        $(".load-more-viewattendance").show();
                    }
                }
            });
        } else {
            $('.load-more-button').hide();
        }
    });

    if ($('.add-to-cart').length) {
        $(document).on('click', '.add-to-cart', function() {
            $("#cover-spin").show();
            var productid = $(this).attr('data-product-id');
            var userid = $(this).attr('data-user-id');
            var amount = $(this).attr('data-amount');
            $.ajax({
                type: 'POST',
                url: BASE_URL + '/add-to-cart',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'productid': productid,
                    'user_id': userid,
                    'amount': amount,
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    var object = JSON.parse(JSON.stringify(response));
                    if (object.status) {
                        toastr.success(object.message);
                        window.location = BASE_URL + object.redirect;
                    } else {
                        toastr.error(object.message);
                        window.location = BASE_URL + object.redirect;
                    }
                }
            });
        });
    }

    if ($(".removecartProduct").length) {
        $(document).on('click', '.removecartProduct', function() {
            var cartproductid = $(this).closest('div').attr('data-cart-id');
            $.ajax({
                type: 'POST',
                url: BASE_URL + '/remove-cart-product',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'cartproductid': cartproductid,
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    var object = JSON.parse(JSON.stringify(response));
                    if (object.status) {
                        toastr.success(object.message);
                        location.reload();
                    } else {
                        toastr.error(object.message);
                        location.reload();
                    }
                }
            });
        });
    }

    if ($(".qty-cls").length) {
        var grandtotal = 0;
        $(document).on('change', '.qty-cls', function() {
            if ($(this).val() < 1) {
                $(this).val(1);
                var productamount = $(this).prev().prev('p').find('span.productAmount-cls').attr('data-amount');
                var totalqty = $('.qty-cls').val();
                var totalamount = (productamount * totalqty);
                $(this).prev().prev('p').find('span.productAmount-cls').text(totalamount);
                scout_app.update_cart_amounts();
            } else {
                var productamount = $(this).prev().prev('p').find('span.productAmount-cls').attr('data-amount');
                var totalqty = this.value;
                var totalamount = (productamount * totalqty);
                $(this).prev().prev('p').find('span.productAmount-cls').text(totalamount);
                scout_app.update_cart_amounts();
            }
        });
    }

    if ($(".qtyplus").length) {
        $(document).on('click', '.qtyplus', function() {
            var qtyplus = $(this).prev().val(+$(this).prev().val() + 1);
            var productamount = $(this).prev().prev().prev('p').find('span.productAmount-cls').attr('data-amount');
            var totalqty = $(this).prev().val();
            var totalamount = (productamount * totalqty);
            $(this).prev().prev().prev('p').find('span.productAmount-cls').text(totalamount);
            var subtotalAmount = $(".totalamount-cls").text();
            var subtotalAmount1 = $(".totalsubamount-cls").text();
            $('.totalamount-cls').text(parseFloat(productamount) + parseFloat(subtotalAmount));
            $('.totalsubamount-cls').text(parseFloat(productamount) + parseFloat(subtotalAmount1));
        });
    }
    if ($(".qtyminus").length) {
        $(document).on('click', '.qtyminus', function() {
            if ($(this).next().val() > 1) {
                if ($(this).next().val() > 1) {
                    var qtyminus = $(this).next().val(+$(this).next().val() - 1);
                    var productamount = $(this).prev('p').find('span.productAmount-cls').attr('data-amount');
                    var totalqty = $(this).next().val();
                    var totalamount = (productamount * totalqty);
                    $(this).prev('p').find('span.productAmount-cls').text(totalamount);
                    var subtotalAmount = $(".totalamount-cls").text();
                    var subtotalAmount1 = $(".totalsubamount-cls").text();
                    $('.totalamount-cls').text(parseFloat(subtotalAmount) - parseFloat(productamount));
                    $('.totalsubamount-cls').text(parseFloat(subtotalAmount1) - parseFloat(productamount));
                }
            }
        });
    }
    if ($(".checkout-cls").length) {
        $(document).on('click', '.checkout-cls', function() {
            $("#cover-spin").show();
            var finalarr = [];
            $('.products-cart-list').each(function(index, value) {
                var dataarr = {};
                var productid = $(this).attr('data-product-id');
                var price = parseFloat($(this).find('span.productAmount-cls').attr('data-amount') || 0, 10);
                var qty = parseFloat($(this).find('.qty-cls').val() || 0, 10);
                var amount = (qty * price);
                dataarr['productID'] = productid;
                dataarr['amount'] = amount;
                dataarr['qty'] = qty;
                finalarr.push(dataarr);
            });
            $.ajax({
                url: BASE_URL + '/checkout-update',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'finalarr': finalarr,
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    var data = JSON.parse(JSON.stringify(response));
                    window.location = BASE_URL + data.redirect;
                }
            });
        });
    }
    if ($('.purchase-product-submit').length) {
        $(document).on("click", '.purchase-product-submit', function() {
            $("#cover-spin").show();
            $.ajax({
                url: BASE_URL + '/search-purchase-product',
                type: 'POST',
                data: {
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'member': $("#member").val(),
                    'productname': $("#productname").val(),
                    'product_sku': $("#product_sku").val(),
                },
                success: function(response) {
                    $("#cover-spin").hide();
                    $(".search-purchase-cls").html(response.list);
                    $(".purchase-product-cls").hide();
                    $("#seacrch-purchase-productTable").dataTable();
                    $("#purchase-productTable_wrapper").hide();
                }
            });
        });
    }
    $('input[name=ship_to_different_address]:checkbox').click(function() {
        if ($('input[name=ship_to_different_address]:checked').val() == "1") {
            $(".shapping-address").show();
        } else {
            $(".shapping-address").hide();
        }
    });

    $('input[name=otherexperience]:radio').click(function() {
        if ($('input[name=otherexperience]:checked').val() == "Yes") {
            $(".other-exp-cls").show();
        } else {
            $(".other-exp-cls").hide();
        }
    });

    $('input[name=Health_declaration]:radio').click(function() {
        if ($('input[name=Health_declaration]:checked').val() == "1") {
            $(".health-decl-cls").show();
        } else {
            $(".health-decl-cls").hide();
        }
    });
    $(document).on('click', 'input[name=Specialty_Instructor]:radio', function() {
        if ($('input[name=Specialty_Instructor]:checked').val() == "1") {
            $(".speicals-instructor-cls").show();
        } else {
            $(".speicals-instructor-cls").hide();
        }
    });

    // $("select.specialtycls").change(function(){
    //  var selectsepcialty = $(this).children("option:selected").val();
    //  if(selectsepcialty == 'others'){
    //      $(".specialty-cls").show();
    //  }else{
    //      $(".specialty-cls").hide();
    //  }
    // });

    $("select.teamclass").change(function() {
        var team = $(this).children("option:selected").val();
        $(".elite_team-class").remove();
        if (team != '') {
            //$('.elite-team-cls').show();
            $.ajax({
                type: 'GET',
                url: BASE_URL + '/users/elitedata/' + team,
                data: {},
                success: function(response) {
                    $("#cover-spin").css("display", "none");
                    $('.elite-team-cls').html(response.elite_team);
                    $('.rank-team-cls').html(response.rank_team);
                }
            });
        } else {
            $('.elite-team-cls').hide();
            $('.rank-team-cls').hide();
        }
    });

    $("select.relarionshipcls").change(function() {
        var selectsepcialty = $(this).children("option:selected").val();
        if (selectsepcialty == '6') {
            $(".relationship-cls").show();
        } else {
            $(".relationship-cls").hide();
        }
    });
    $("select.highereducation-cls").change(function() {
        var selectsepcialty = $(this).children("option:selected").val();
        if (selectsepcialty == '10') {
            $(".notecls").show();
        } else {
            $(".notecls").hide();
        }
    });

    // if($('.remarkedit-cls').length){
    //  var id = $('.remarkscls').children("option:selected").val();
    //  var user_id = $('.remarkscls').children("option:selected").attr('data-user-id');
    //  $.ajax({
    //      type: 'GET',
    //      url: BASE_URL+'/users/remarkseditData/'+user_id,
    //      data: {},
    //      success: function (response) {
    //          $("#cover-spin").css("display", "none");
    //          $('.remarks_html_cls').html(response);
    //          if($('#remark_date').length){
    //              $("#remark_date").pickadate();
    //          }
    //      }
    //  });
    // }

    $("select.remarkscls").change(function() {
        var id = $(this).children("option:selected").val();
        if (id == '') {
            var id = '0';
        }
        console.log(id)
        $.ajax({
            type: 'GET',
            url: BASE_URL + '/users/remarksData/' + id,
            data: {},
            success: function(response) {
                $("#cover-spin").css("display", "none");
                $('.remarks_html_cls').html(response);
                if ($('#remark_date').length) {
                    $("#remark_date").pickadate();
                }
            }
        });
    });

    var editrelatedactivity = [];
    var related_text = '';
    /*edit relatedactivity*/
    $.each($(".checkboxClass:checked"), function() {
        related_text = $(this).val();
        editrelatedactivity.push(related_text);
    });

    if (editrelatedactivity != '') {
        var length = $('.checkboxClass:checked').length;
        if (length == 4) {
            $('.related_value_selected_cls').text('4 Selected');
        } else if (length == 5) {
            $('.related_value_selected_cls').text('5 Selected');
        } else {
            $('.related_value_selected_cls').text(editrelatedactivity.join(", "));
        }
    } else {
        if ($('html').is(':lang(ch)')) {
            $('.related_value_selected_cls').text('未選中的');
        } else {
            $('.related_value_selected_cls').text('None Selected');
        }
    }

    /*edit relatedactivity*/

    /*Add relatedactivity*/
    $(".checkboxClass").click(function() {
        var relatedactivity = [];
        $.each($(".checkboxClass:checked"), function() {
            related_text = $(this).val();
            relatedactivity.push(related_text);
        });

        if (relatedactivity != '') {
            var length = $('.checkboxClass:checked').length;
            if (length == 4) {
                $('.related_value_selected_cls').text('4 Selected');
            } else if (length == 5) {
                $('.related_value_selected_cls').text('5 Selected');
            } else {
                $('.related_value_selected_cls').text(relatedactivity.join(", "));
            }
        } else {
            if ($('html').is(':lang(ch)')) {
                $('.related_value_selected_cls').text('未選中的');
            } else {
                $('.related_value_selected_cls').text('None Selected');
            }
        }
    });
    /*Add relatedactivity*/

    var edit_specialty = [];
    var specialty_text = '';
    /*edit specialty*/
    $.each($(".specialty-clss:checked"), function() {
        specialty_text = $(this).val();
        edit_specialty.push(specialty_text);
    });

    if (edit_specialty != '') {
        var length = $('.specialty-clss:checked').length;
        if (length == 4) {
            $('.specialty_value_selected_cls').text('4 Selected');
        } else if (length == 5) {
            $('.specialty_value_selected_cls').text('5 Selected');
        } else if (length == 6) {
            $('.specialty_value_selected_cls').text('6 Selected');
        } else {
            $('.specialty_value_selected_cls').text(edit_specialty.join(", "));
        }
    } else {
        if ($('html').is(':lang(ch)')) {
            $('.related_value_selected_cls').text('未選中的');
        } else {
            $('.related_value_selected_cls').text('None Selected');
        }
    }

    /*edit specialty*/

    /*Add specialty*/
    $(".specialty-clss").click(function() {
        var specialty = [];
        $.each($(".specialty-clss:checked"), function() {
            specialty_text = $(this).val();
            specialty.push(specialty_text);
        });
        $('.specialty_value_selected_cls').text(specialty.join(", "));
        if (specialty != '') {
            var length = $('.specialty-clss:checked').length;
            if (length == 4) {
                $('.specialty_value_selected_cls').text('4 Selected');
            } else if (length == 5) {
                $('.specialty_value_selected_cls').text('5 Selected');
            } else if (length == 6) {
                $('.specialty_value_selected_cls').text('6 Selected');
            }
        } else {
            if ($('html').is(':lang(ch)')) {
                $('.related_value_selected_cls').text('未選中的');
            } else {
                $('.related_value_selected_cls').text('None Selected');
            }
        }
    });
    /*Add specialty*/

    /*Add assesment*/
    $('input[name=assessment]:radio').click(function() {
        if ($('input[name=assessment]:checked').val() == "Yes") {
            $(".assesment-decl-cls").show();
        } else {
            $(".assesment-decl-cls").hide();
        }
    });
    /*Add assesment*/

    $(".add-evens-cls").hide();
    scout_app.hideshowhourus();
    if ($('.starttime').val() == '') {
        $('.starttime').timepicker({
            dropdown: true,
            scrollbar: true,
            defaultTime: '09',
            timeFormat: 'H:mm',
            interval: 60,
        });
    } else {
        $('.starttime').timepicker({
            dropdown: true,
            scrollbar: true,
            interval: 60,
            show2400: true,
        });
    }
    if ($('.endtime').val() == '') {
        $('.endtime').timepicker({
            dropdown: true,
            scrollbar: true,
            change: scout_app.hideshowhourus,
            timeFormat: 'H:mm',
            interval: 60,
            show2400: true,
            defaultTime: '10',
        });
    } else {
        $('.endtime').timepicker({
            dropdown: true,
            scrollbar: true,
            change: scout_app.hideshowhourus,
            timeFormat: 'H:mm',
            interval: 60,
            show2400: true,
        });
    }

    $('#inTime').timepicker({
        dropdown: true,
        scrollbar: true,
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        change: scout_app.attendanceHours,
    });

    $('#outTime').timepicker({
        dropdown: true,
        scrollbar: true,
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        change: scout_app.attendanceHours,
    });

    if ($('#calendar').length) {
        scout_app.fullCalendarfun();
        $(".add-evens-cls").show();
        $('#calendar').show();
    }
    /* Add Events*/
    // $('input[name=multiple_event]:radio').click(function() {
    //  if($('input[name=multiple_event]:checked').val() == "recurringevent"){
    //      if(!$('#eventform').valid()){
    //          $("#eventform").valid();
    //          if($('input[name=multiple_event]:checked').val() == "recurringevent"){
    //              $("input[name='multiple_event'][value='recurringevent']").prop('checked', false);
    //          }else{
    //              $("input[name='multiple_event'][value='recurringevent']").prop('checked', false);
    //          }
    //      }else{
    //          $(".dateselect").hide();
    //          $(".add-evens-cls").show();
    //          $(".event-hours-cls").hide();
    //      }
    //  }else{
    //      $(".add-evens-cls").hide();
    //      $('#calendar').hide();
    //      $(".dateselect").show();
    //      $(".event-hours-cls").show();
    //  }
    // });
    /* Add Events*/

    /* Edit Events*/
    // if($('input[name=multiple_event]:checked').val() == "recurringevent"){
    //  if($('#enddate').val() == ''){
    //      scout_app.fullCalendarfun();
    //      $('#calendar').show();
    //      $(".dateselect").hide();
    //      $(".add-evens-cls").show();
    //      $(".event-hours-cls").hide();
    //  }else{
    //      if($('#eventform').valid() == false){
    //          $("#eventform").valid();
    //          if($('input[name=multiple_event]:checked').val() == "recurringevent"){
    //              $("input[name='multiple_event'][value='singleevent']").prop('checked', true);
    //          }else{
    //              $("input[name='multiple_event'][value='recurringevent']").prop('checked', true);
    //          }
    //      }
    //      else{
    //          scout_app.fullCalendarfun();
    //          $('#calendar').show();
    //          $(".dateselect").hide();
    //          $(".add-evens-cls").show();
    //          $(".event-hours-cls").hide();
    //      }
    //  }
    // }else{
    //  $(".add-evens-cls").hide();
    //  $('#calendar').hide();
    //  $(".dateselect").show();
    //  $(".event-hours-cls").show();
    // }
    /* Edit Events*/
    $(document).on('click', '.addevents', function() {
        if (!$('#eventform').valid()) {
            $("#eventform").valid();
            $('#eventmodel').modal('hide');
        } else {
            var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            var eventdate = new Date();
            ///var datetoday = eventdate.format('YYYY-MM-DD');
            var Dayname = weekday[eventdate.getDay()];
            var Monthname = monthNames[eventdate.getMonth()];
            var daydate = eventdate.getDate();
            var Year = eventdate.getFullYear();
            var finaldate = Dayname + ',' + daydate + ' ' + Monthname + ',' + Year;
            $('#eventstartdate').val(finaldate);
            var html = scout_app.changeMonthlyOccurs(finaldate);
            $(".occurs").val('');
            $(".dailyoccurs-cls").hide();
            $(".weeklyoccurs").hide();
            $(".monthlyoccurs").hide();
            $(".occur-monthly-cls").html(html);
            $(".occur-monthly-clsss").remove();
            $('#eventmodel').modal('show');
        }
    });

    $(document).on('click', '.edit-btn-event', function() {
        $('#editeventmodel').modal({
            backdrop: false
        });
        $('#action_event').modal('hide');
    });
    $("#reeventstartdate").pickadate({
        min: true,
    });

    $("#startdate").pickadate({
        min: true,
    });

    $("#enddate").pickadate({
        min: true,
    });
    $('#eventstartdate').pickadate({
        min: true,
        onClose: function() {
            scout_app.totaleventHours();
            var html = scout_app.changeMonthlyOccurs(this.get('select', 'yyyy-mm-dd'));
            $(".occur-monthly-cls").html(html);
            $(".occur-monthly-clsss").remove();
        },
    });

    $('#eventenddate').pickadate({
        min: true,
        onClose: function() {
            scout_app.totaleventHours();
        },
    });

    $('#eventstarttime').timepicker({
        dropdown: true,
        scrollbar: true,
        defaultTime: '09',
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        change: scout_app.totaleventHours,
    });
    $('#eventendtime').timepicker({
        dropdown: true,
        scrollbar: true,
        change: scout_app.totaleventHours,
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        defaultTime: '10',
    });
    $('#editeventstarttime').timepicker({
        dropdown: true,
        scrollbar: true,
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        change: scout_app.totaleventeditHours,
    });
    $('#editeventendtime').timepicker({
        dropdown: true,
        scrollbar: true,
        timeFormat: 'H:mm',
        interval: 60,
        show2400: true,
        change: scout_app.totaleventeditHours,
    });
    $('#editeventstartdate').pickadate({
        min: true,
        onClose: function() {
            scout_app.totaleventeditHours();
            var html = scout_app.changeeditMonthlyOccurs(this.get('select', 'yyyy-mm-dd'));
            $(".occur-monthly-edit-cls").html(html);
            $(".occur-monthly-clsss").remove();
        },
    });
    $('#editeventenddate').pickadate({
        min: true,
        onClose: function() {
            scout_app.totaleventeditHours();
        },
    });
    $(".dailyoccurs-cls").hide();
    $(".monthlyoccurs").hide();
    $(".weeklyoccurs").hide();

    $("select.occurs").change(function() {
        var selectoccurs = $(this).children("option:selected").val();
        if (selectoccurs == 'Once') {
            $(".dailyoccurs-cls").hide();
            $(".monthlyoccurs").hide();
            $('#eventenddate').val('');
            $(".weeklyoccurs").hide();

        } else if (selectoccurs == 'Daily') {
            $(".monthlyoccurs").hide();
            $(".dailyoccurs-cls").show();
            $(".weeklyoccurs").hide();
        } else if (selectoccurs == 'Weekly') {
            $(".weeklyoccurs").show();
            $(".monthlyoccurs").hide();
            $(".dailyoccurs-cls").show();
        } else {
            $(".weeklyoccurs").hide();
            $(".monthlyoccurs").show();
            $(".dailyoccurs-cls").show();
        }
    });
    $("select.editoccurs").change(function() {
        var selectoccurs = $(this).children("option:selected").val();
        if (selectoccurs == 'Once') {
            $(".dailyoccurs-cls").hide();
            $(".monthlyoccurs").hide();
            $('#editeventenddate').val('');
            $(".weeklyoccurs").hide();
            $('#editeventenddate').pickadate({
                min: true,
                onClose: function() {
                    scout_app.totaleventeditHours();
                },
            });
        } else if (selectoccurs == 'Daily') {
            $(".monthlyoccurs").hide();
            $(".dailyoccurs-cls").show();
            $(".weeklyoccurs").hide();
        } else if (selectoccurs == 'Weekly') {
            $(".weeklyoccurs").show();
            $(".monthlyoccurs").hide();
            $(".dailyoccurs-cls").show();
            $('#editeventenddate').pickadate({
                min: true,
                onClose: function() {
                    scout_app.totaleventeditHours();
                },
            });
        } else if (selectoccurs == 'Monthly') {
            $(".weeklyoccurs").hide();
            $(".monthlyoccurs").show();
            $(".dailyoccurs-cls").show();
            $('#editeventenddate').pickadate({
                min: true,
                onClose: function() {
                    scout_app.totaleventeditHours();
                },
            });
        }
    });

    $('#event-Assign-user').multiselect({
        includeSelectAllOption: true,
    });
    $(".close-assignuser").click(function() {
        $(".user-assign-model").hide();
        $(".assign-tq").show();
        $(".modal-backdrop").removeClass();
    });

    $('.event-id-cls').click(function() {
        eventid = $(this).attr('event-id');
        $('#assingeventid').val(eventid)
    });

    $(document).on('change', 'select.status', function() {
        var eventstaus = $(this).children("option:selected").val();
        var id = $(this).children("option:selected").attr('data-id');
        $.ajax({
            type: 'GET',
            url: BASE_URL + '/eventstatusUpdate/' + id,
            data: {
                'eventstaus': eventstaus,
                '_token': $('meta[name="_token"]').attr('content'),
            },
            success: function(response) {
                $("#cover-spin").css("display", "none");
                var object = JSON.parse(JSON.stringify(response));
                if (object.status) {
                    toastr.success(object.message);
                    location.reload();
                    if (eventstaus == '4') {
                        window.location.href = "event-report/" + id;
                    }
                } else {
                    toastr.error(object.message);
                }
            }
        });
    });

    $(document).on('change', '#members', function() {
        var member_code = $(this).children("option:selected").attr('data-code');
        $('#memberCode').val(member_code);
    });

    $(document).on('change', '#eventName', function() {
        var eventType = $(this).children("option:selected").attr('data-event-type');
        $('#eventType').val(eventType);
    });
});

scout_delete = {
    init: function() {
        $(document).on("click", ".deletRole", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").show();
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/roleManagement/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").hide();
                        var object = JSON.parse(JSON.stringify(response));
                        location.reload();
                    }
                });
            } else {
                return false
            }
        });

        $(document).on("click", ".deleteMember", function() {
            $("#cover-spin").css("display", "block");
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/users/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload(true);
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletEvent", function() {
            $("#cover-spin").css("display", "block");
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/eventManagement/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload(true);
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".history-team-rank-cls", function() {
            var id = $(this).attr("data-id");
            var tr = $(this).closest('tr');
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'POST',
                    url: BASE_URL + '/history-team-rank',
                    data: {
                        'id': id,
                        'log': $(this).attr('data-log'),
                        "_token": $('#csrf-token').val(),
                    },
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            tr.fadeOut(500, function() {
                                $(this).remove();
                            });
                            toastr.success(object.message);
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletelog", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/audit-log/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });
        $(document).on("click", ".deletQualification", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/qualification/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletrelatedActivity", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/related-activity-history/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".delete-btn-event", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/eventManagement/deleteEventSchedule/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            $('#action_event').modal('hide');
                            //$('#calendar').fullCalendar('removeEvents', id);
                            window.setTimeout(function(){location.reload()},1000)
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletespecialty", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/specialty/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deleteelite", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/team/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletesubselite", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/rank/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deletesubteam", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/subteam/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deleteremarks", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/remarks/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });
        $(document).on("click", ".delete-event-type", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/event-type/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });

        $(document).on("click", ".deleteproduct", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/product/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });
        $(document).on("click", ".removeImage", function() {
            var dataimage = $(this).attr("data-image");
            var id = $(this).attr("data-id");
            var tr = $(this).closest('div');

            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").show();
                $.ajax({
                    type: 'POST',
                    url: BASE_URL + '/product/removeImage',
                    data: {
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'dataimage': dataimage,
                        'id': id,
                    },
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            tr.fadeOut(500, function() {
                                $(this).remove();
                            });
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });


        $(document).on("click", ".deletepurchaseproduct", function() {
            var id = $(this).attr("data-id");
            if (confirm("Are you sure you want to delete this?")) {
                $("#cover-spin").css("display", "block");
                $.ajax({
                    type: 'GET',
                    url: BASE_URL + '/purchase-product/delete/' + id,
                    data: {},
                    success: function(response) {
                        $("#cover-spin").css("display", "none");
                        var object = JSON.parse(JSON.stringify(response));
                        if (object.status) {
                            toastr.success(object.message);
                            location.reload();
                        } else {
                            toastr.error(object.message);
                        }
                    }
                });
            } else {
                return false;
            }
        });
    },
};


scout_event = {
    init: function() {

       $('#event_dates_cal').datepicker({
        startDate: new Date(),
        multidate: true,
        format: "dd-mm-yyyy",
        language: 'en'
    }).on('changeDate', function(e) {
        $('#event_dates').val(' ' + e.dates);
    });

},
};

scout_datatable = {
    init: function() {
        /*Role Management Datatable START*/
        if ($("#rolemanagemnttable").length) {
            $("#rolemanagemnttable").dataTable();
        }
        /*Role Management Datatable End*/

        /*Assign User Report START*/
        if ($("#assignUserReport").length) {
            $("#assignUserReport").dataTable();
        }
        /*Assign User Report Datatable End*/

        /*Event Management Datatable START*/
        if ($("#eventtable").length) {
            $("#eventtable").dataTable({
                "order": [
                [0, "asc"]
                ],
            });
        }

        if ($("#eventrepotTable").length) {
            $("#eventrepotTable").dataTable();
        }

        if ($("#packagetable").length) {
            $("#packagetable").dataTable();
        }

        if ($("#qualificationstable").length) {
            $("#qualificationstable").dataTable();
        }

        if ($("#attendanceReporttable").length) {
            $("#attendanceReporttable").dataTable();
        }
        if ($("#attendanceSerachReporttable").length) {
            $("#attendanceSerachReporttable").dataTable();
        }
        if ($("#productTable").length) {
            $("#productTable").dataTable();
        }
        if ($("#purchase-productTable").length) {
            var tablepurchase = $("#purchase-productTable").DataTable();
        }
        if ($("#logtable").length) {
            var logtable = $("#logtable").DataTable();
            $("#logFormname").on("change", function() {
                var logFormname = $("#logFormname").val();
                logtable.search(logFormname).draw();
            });

            $("#logdate").on("change", function() {
                var logdate = moment($("#logdate").val()).format('DD/MM/YYYY');
                logtable.search(logdate).draw();
            });

            $(".log-list-clear").on("click", function() {
                logtable.search("").draw();
                $("#logFormname").val('');
                $("#logdate").val('');
            });
        }


        if ($("#attendanceTable").length) {
            var attendanceTable = $("#attendanceTable").DataTable();
            $("#members").on("change", function() {
                var member = $("#members").val();
                attendanceTable.search(member).draw();
            });

            $("#membercodetable").on("keyup", function() {
                var memberCode = $("#membercodetable").val();
                attendanceTable.search(memberCode).draw();
            });

            $("#eventName").on("change", function() {
                var eventName = $("#eventName").val();
                attendanceTable.search(eventName).draw();
            });

            $("#eventdate").on("change", function() {
                var eventdate = moment($("#eventdate").val()).format('DD/MM/YYYY');
                attendanceTable.search(eventdate).draw();
            });
        }

        /*Member Management Datatable START*/
        if ($("#member-table").length) {
            function showCheckboxes() {
                var checkboxes = document.getElementById("checkboxes");
                if (!expanded) {
                    checkboxes.style.display = "block";
                    expanded = true;
                } else {
                    checkboxes.style.display = "none";
                    expanded = false;
                }
            }

            $('#member-table thead tr').clone(true).appendTo('#member-table thead');
            $('#member-table thead tr:eq(1) th').each(function(i) {
                if (i != 0 && i != 41 && i != 42 && i != 43) {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder=" ' + title + '" style="width: 100px;"/>');
                    $('input', this).on('keyup change', function() {
                        if (table.column(i).search() !== this.value) {
                            table.column(i).search(this.value).draw();
                            var info = table.page.info();
                            var rowsshown = info.recordsDisplay;
                            $(".total_filter_users").text(rowsshown);
                        }
                    });
                } else {
                    $(this).html('');
                }
            });
            var useridarr = [];
            $(document).on('click', '.user-id-cls', function() {
                var userid = $(this).closest('.selected').attr('id');
                if (userid != undefined) {
                    useridarr.push(userid);
                } else {
                    useridarr.pop();
                }
            });
            $(document).on('change', '.dt-checkboxes-select-all', function() {
                $('#member-table tr').each(function() {
                    var userid = $(this).closest('.selected').attr('id');
                    if (userid != undefined) {
                        useridarr.push(userid);
                    } else {
                        useridarr.pop();
                    }
                });
            });
            $(document).on('click', '.assign-user-cls', function() {
                var eventid = $("#events_name").val();
                if (eventid != '') {
                    if (useridarr != '') {
                        $("#cover-spin").show();
                        $.ajax({
                            url: BASE_URL + '/event-assign-user',
                            type: 'POST',
                            data: {
                                '_token': $('meta[name="csrf-token"]').attr('content'),
                                'eventid': eventid,
                                'user_id': useridarr,
                            },
                            success: function(response) {
                                $("#cover-spin").hide();
                                var data = JSON.parse(JSON.stringify(response));
                                if (data.status) {
                                    toastr.success(data.message);
                                } else {
                                    toastr.error(data.message);
                                }
                            }
                        });
                    } else {
                        toastr.error("Please select member.");
                    }
                } else {
                    toastr.error("Please select event.");
                }
            });
            if ($('html').is(':lang(ch)')) {
                var export_member = '匯出';
            } else {
                var export_member = 'Export Member';
            }

            var exportArray = [];
            $.each($(".filter-serach-cls:checked"), function() {
             exportArray.push(this.value);
         });

            $(document).on('click', '.buttons-csv', function() {
                var exportArray = [];
                $.each($(".filter-serach-cls:checked"), function() {
                 exportArray.push(this.value);
             });
            });

            var buttonCommon = {
                exportOptions: {
                    //column: [2, 3, 4, 17, 5, 18, 19, 6, 7, 8, 9, 10, 11, 20, 21, 22, 12, 13, 14, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41],
                    //column: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43],
                    column: exportArray,
                    format: {
                        body: function(data, column, row, node) {
                            // if it is select
                            // if (row == 0) {
                            //     return $(data).closest('a').text();
                            // } else if (row == 38) {
                            //     return $(data).find("option:selected").text();
                            // } else return data
                            return data
                        }
                    }
                }
            };

            function exportCSV(){
             //   $('input[name="export_filter"]').val('7,13');
             var val =  $('input[name="export_filter"]').val();

             $.ajax({
                type: 'POST',
                url: BASE_URL + '/exportCSV',
                data: {
                   '_token': $('meta[name="csrf-token"]').attr('content'),
                   'val': val,
                   'formData' : $('#filterDateForm').serialize(),
               },
               success: function (data) {

                var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);                    
                if(!isHTML(data)){  
                    var downloadLink = document.createElement("a");
                    var fileData = ['\ufeff'+data];

                    var blobObject = new Blob(fileData,{
                        type: "text/csv;charset=utf-8;"
                    });

                    var url = URL.createObjectURL(blobObject);
                    downloadLink.href = url;
                    downloadLink.download = "Member.csv";

                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }
            },
        });
         }

         var table = $('#member-table').DataTable({
            'columnDefs': [{
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                },
            }],
            orderCellsTop: true,
            fixedHeader: true,
            bAutoWidth: false,
                //searching: false,
                aLengthMenu: [
                [25, 50, 75, -1],
                [25, 50, 75, "All"]
                ],
                iDisplayLength: 25,
                dom: 'Blfrtip',
               /* buttons: [
                'excel', $.extend(true, {}, buttonCommon, {
                    extend: 'csv',
                    text: export_member,
                    filename: 'Member',
                    bom: true,
                    exportOptions: {
                        //columns: [2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40],
                        columns: $.session.get("dataColumn"),
                        modifier: {
                            search: 'none'
                        }
                    }
                })
                ],*/
                buttons: [
                {
                    text: export_member,
                    action: function () {
                        exportCSV();
                    },
                }
                ],
                // buttons: [
                // 'excel', $.extend(true, {}, buttonCommon, {
                //  extend: "csv"
                // })
                // ],
                select: {
                    style: 'multi',
                },
                scrollX: true,
                scrollCollapse: true,
                fnInitComplete: function() {
                    $('.dataTables_scrollHead').css('overflow', 'auto');
                    $('.dataTables_scrollHead').on('scroll', function() {
                        $('.dataTables_scrollBody').scrollLeft($(this).scrollLeft());
                    });
                },
            });
         $('#member-table').on('select.dt deselect.dt', function() {
            var count = table.rows({
                selected: true
            }).count();
            if ($('html').is(':lang(ch)')) {
                $(".dataTables_info").html('從 1 to ' + table.rows().count() + ' of ' + table.rows().count() + ' 個事項<span class="select-info"><span class="select-item"></span></span>');
                $(".select-item").html(count + ' 選擇行');
            } else {
                $(".dataTables_info").html('Showing 1 to ' + table.rows().count() + ' of ' + table.rows().count() + ' entries<span class="select-info"><span class="select-item"></span></span>');
                $(".select-item").html(count + ' row selected');
            }
            if (count == 0) {
                $(".select-item").hide();
            } else {
                $(".select-item").show();
            }
        });
         $.ajax({
            type: 'GET',
            url: BASE_URL + '/get-all-event',
            data: {},
            success: function(response) {
                $(".dt-buttons").append(response.html);
                if ($(".filter_event").length) {
                    $('.filter_event').daterangepicker({
                        showDropdowns: true,
                        minYear: 1950,
                        maxYear: parseInt(moment().format('YYYY'), 10),
                        ranges: {
                            'Today': [moment(), moment()],
                            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                            'This Month': [moment().startOf('month'), moment().endOf('month')],
                            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                            'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')]
                        },
                        alwaysShowCalendars: true,
                        autoUpdateInput: false,
                    });
                }
                $('.filter_event').on('apply.daterangepicker', function(ev, picker) {
                    var eventDate = $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                    $("#search_event_date").val($(this).val());
                    $.ajax({
                        url: BASE_URL + '/get_event_type',
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'search_event_date': $(this).val(),
                            'search_event_type': $("#search_event_type").val(),
                        },
                        success: function(response) {
                            $("#cover-spin").hide();
                            $(".event_name_cls").hide();
                            $(".events-id-cls").hide();
                            $(".filter_event_cls").html(response);
                        }
                    });
                });

                $(document).on('change', '.event_type_cls', function() {
                    $("#search_event_type").val(this.value);
                    $.ajax({
                        url: BASE_URL + '/get_event_type',
                        type: 'POST',
                        data: {
                            '_token': $('meta[name="csrf-token"]').attr('content'),
                            'search_event_date': $("#search_event_date").val(),
                            'search_event_type': this.value,
                        },
                        success: function(response) {
                            $("#cover-spin").hide();
                            $(".event_name_cls").hide();
                            $(".events-id-cls").hide();
                            $(".filter_event_cls").html(response);
                        }
                    });
                });
            }
        });
         var columns_table = [15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41];
         var disable_column = ["9", "12", "13"];
            // table.columns(columns_table).visible(false);
            $(document).on('click', '.filter-serach-cls-all', function() {
                if ($(this).is(':checked')) {
                    if (this.value == 'all') {
                     /* table.columns(columns_table).visible(true);*/
                        //$(".filter-serach-cls").prop("checked", true);
                        $(".filter-serach-cls-all").prop("checked", true);
                        $.session.set("dataColumnall", 'all');

                        $('input[name="export_filter"]').val('')

                        $("input[name='customfilter[]']").each( function () {
                            var val = $(this).val();
                            var old_val = $('input[name="export_filter"]').val();
                            if(val != "all"){
                                if(old_val == ''){
                                    $('input[name="export_filter"]').val(val);
                                }else{
                                    $('input[name="export_filter"]').val(old_val+','+val);
                                }
                            }      
                            table.columns(val).visible(true);
                            $(this).prop('checked',true);
                            /*if(jQuery.inArray(val, disable_column) !== -1) {
                               table.columns(val).visible(false);
                               $(this).prop('checked',false);
                           } else {
                               table.columns(val).visible(true);
                               $(this).prop('checked',true);
                           }*/

                       });

                    }
                } else {
                   /* $(".filter-serach-cls").prop("checked", false);
                    for (i = 2; i <= 14; i++) {
                        if (i <= 14) {
                            table.columns(i).visible(true);
                            $(".filter-serach-cls[value=" + i + "]").prop("checked", true);
                        } else {
                            $(".filter-serach-cls[value=" + i + "]").prop("checked", false);
                        }
                    }
                    $.session.remove("dataColumnall");

                    table.columns(columns_table).visible(false);*/
                    var sessionValue = $.session.get("dataColumn");
                    $('input[name="export_filter"]').val(sessionValue)
                    var Columnspilt = sessionValue.split(',');
                    $.each(Columnspilt, function(index, value) {
                        table.column(value).visible(true);
                    // var column = table.column( value );
                    // column.visible( ! column.visible() );
                    $(".filter-serach-cls[value=" + value + "]").prop("checked", "true");
                });
                    if (jQuery.inArray("2", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=2]").prop("checked", true);
                        table.columns(2).visible(true);
                    } else {
                        $(".filter-serach-cls[value=2]").prop("checked", false);
                        table.columns(2).visible(false);
                    }
                    if (jQuery.inArray("3", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=3]").prop("checked", true);
                        table.columns(3).visible(true);
                    } else {
                        $(".filter-serach-cls[value=3]").prop("checked", false);
                        table.columns(3).visible(false);
                    }
                    if (jQuery.inArray("4", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=4]").prop("checked", true);
                        table.columns(4).visible(true);
                    } else {
                        $(".filter-serach-cls[value=4]").prop("checked", false);
                        table.columns(4).visible(false);
                    }

                    if (jQuery.inArray("5", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=5]").prop("checked", true);
                        table.columns(5).visible(true);
                    } else {
                        $(".filter-serach-cls[value=5]").prop("checked", false);
                        table.columns(5).visible(false);
                    }
                    if (jQuery.inArray("6", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=6]").prop("checked", true);
                        table.columns(6).visible(true);
                    } else {
                        $(".filter-serach-cls[value=6]").prop("checked", false);
                        table.columns(6).visible(false);
                    }

                    if (jQuery.inArray("7", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=7]").prop("checked", true);
                        table.columns(7).visible(true);
                    } else {
                        $(".filter-serach-cls[value=7]").prop("checked", false);
                        table.columns(7).visible(false);
                    }

                    if (jQuery.inArray("9", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=9]").prop("checked", true);
                        table.columns(9).visible(true);
                    } else {
                        $(".filter-serach-cls[value=9]").prop("checked", false);
                        table.columns(9).visible(false);
                    }

                    if (jQuery.inArray("10", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=10]").prop("checked", true);
                        table.columns(10).visible(true);
                    } else {
                        $(".filter-serach-cls[value=10]").prop("checked", false);
                        table.columns(10).visible(false);
                    }

                    if (jQuery.inArray("11", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=11]").prop("checked", true);
                        table.columns(11).visible(true);
                    } else {
                        $(".filter-serach-cls[value=11]").prop("checked", false);
                        table.columns(11).visible(false);
                    }

                    if (jQuery.inArray("12", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=12]").prop("checked", true);
                        table.columns(12).visible(true);
                    } else {
                        $(".filter-serach-cls[value=12]").prop("checked", false);
                        table.columns(12).visible(false);
                    }

                    if (jQuery.inArray("13", Columnspilt) !== -1) {
                        $(".filter-serach-cls[value=13]").prop("checked", true);
                        table.columns(13).visible(true);
                    } else {
                        $(".filter-serach-cls[value=13]").prop("checked", false);
                        table.columns(13).visible(false);
                    }

                    if (jQuery.inArray("14", Columnspilt) !== -1) {
                     $(".filter-serach-cls[value=14]").prop("checked", true);
                     table.columns(14).visible(true);
                 } else {
                    $(".filter-serach-cls[value=14]").prop("checked", false);
                    table.columns(14).visible(false);
                }

                if (jQuery.inArray("16", Columnspilt) !== -1) {
                 $(".filter-serach-cls[value=16]").prop("checked", true);
                 table.columns(16).visible(true);
             } else {
                $(".filter-serach-cls[value=16]").prop("checked", false);
                table.columns(16).visible(false);
            }
        }
                /*var column = table.column($(this).attr('data-column'));
                column.visible(!column.visible());*/
            });

$(document).on('click', '.filter-serach-cls', function() {
                //Get the column API object
                var checkedAry = [];
                $.each($(".filter-serach-cls:checked"), function() {
                    checkedAry.push(this.value);
                });
                $('input[name="export_filter"]').val(checkedAry)
                var dataColumn = $.session.set("dataColumn", checkedAry);
                var column = table.column($(this).attr('data-column'));
                column.visible(!column.visible());
                // Toggle the visibility
            });


            // if ($.session.get('dataColumnall') != undefined) {

            //     /*$(".filter-serach-cls").prop("checked", true);*/
            //     $(".filter-serach-cls-all").prop("checked", true);
            //     table.columns(columns_table).visible(true);
            // }

            if ($.session.get("dataColumn") != undefined) {
                var sessionValue = $.session.get("dataColumn");
                $('input[name="export_filter"]').val(sessionValue)
                var Columnspilt = sessionValue.split(',');
                $.each(Columnspilt, function(index, value) {
                    table.column(value).visible(true);
                    // var column = table.column( value );
                    // column.visible( ! column.visible() );
                    $(".filter-serach-cls[value=" + value + "]").prop("checked", "true");
                });
                if (jQuery.inArray("2", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=2]").prop("checked", true);
                    table.columns(2).visible(true);
                } else {
                    $(".filter-serach-cls[value=2]").prop("checked", false);
                    table.columns(2).visible(false);
                }
                if (jQuery.inArray("3", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=3]").prop("checked", true);
                    table.columns(3).visible(true);
                } else {
                    $(".filter-serach-cls[value=3]").prop("checked", false);
                    table.columns(3).visible(false);
                }
                if (jQuery.inArray("4", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=4]").prop("checked", true);
                    table.columns(4).visible(true);
                } else {
                    $(".filter-serach-cls[value=4]").prop("checked", false);
                    table.columns(4).visible(false);
                }

                if (jQuery.inArray("5", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=5]").prop("checked", true);
                    table.columns(5).visible(true);
                } else {
                    $(".filter-serach-cls[value=5]").prop("checked", false);
                    table.columns(5).visible(false);
                }
                if (jQuery.inArray("6", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=6]").prop("checked", true);
                    table.columns(6).visible(true);
                } else {
                    $(".filter-serach-cls[value=6]").prop("checked", false);
                    table.columns(6).visible(false);
                }

                if (jQuery.inArray("7", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=7]").prop("checked", true);
                    table.columns(7).visible(true);
                } else {
                    $(".filter-serach-cls[value=7]").prop("checked", false);
                    table.columns(7).visible(false);
                }

                if (jQuery.inArray("9", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=9]").prop("checked", true);
                    table.columns(9).visible(true);
                } else {
                    $(".filter-serach-cls[value=9]").prop("checked", false);
                    table.columns(9).visible(false);
                }

                if (jQuery.inArray("10", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=10]").prop("checked", true);
                    table.columns(10).visible(true);
                } else {
                    $(".filter-serach-cls[value=10]").prop("checked", false);
                    table.columns(10).visible(false);
                }

                if (jQuery.inArray("11", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=11]").prop("checked", true);
                    table.columns(11).visible(true);
                } else {
                    $(".filter-serach-cls[value=11]").prop("checked", false);
                    table.columns(11).visible(false);
                }

                if (jQuery.inArray("12", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=12]").prop("checked", true);
                    table.columns(12).visible(true);
                } else {
                    $(".filter-serach-cls[value=12]").prop("checked", false);
                    table.columns(12).visible(false);
                }

                if (jQuery.inArray("13", Columnspilt) !== -1) {
                    $(".filter-serach-cls[value=13]").prop("checked", true);
                    table.columns(13).visible(true);
                } else {
                    $(".filter-serach-cls[value=13]").prop("checked", false);
                    table.columns(13).visible(false);
                }

                if (jQuery.inArray("14", Columnspilt) !== -1) {
                 $(".filter-serach-cls[value=14]").prop("checked", true);
                 table.columns(14).visible(true);
             } else {
                $(".filter-serach-cls[value=14]").prop("checked", false);
                table.columns(14).visible(false);
            }

            if (jQuery.inArray("16", Columnspilt) !== -1) {
             $(".filter-serach-cls[value=16]").prop("checked", true);
             table.columns(16).visible(true);
         } else {
            $(".filter-serach-cls[value=16]").prop("checked", false);
            table.columns(16).visible(false);
        }
    }else{
       var checkedAry = [];
       $.each($(".filter-serach-cls:checked"), function() {
         checkedAry.push(this.value);
         table.columns(this.value).visible(true);
     });
       var dataColumn = $.session.set("dataColumn", checkedAry);

       /* table.columns(checkedAry).visible(true);*/

       var sessionValue = $.session.get("dataColumn");
       $('input[name="export_filter"]').val(sessionValue)
       var Columnspilt = sessionValue.split(',');
       $.each(Columnspilt, function(index, value) {
        table.column(value).visible(true);
                    // var column = table.column( value );
                    // column.visible( ! column.visible() );
                    $(".filter-serach-cls[value=" + value + "]").prop("checked", "true");
                });
       if (jQuery.inArray("2", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=2]").prop("checked", true);
        table.columns(2).visible(true);
    } else {
        $(".filter-serach-cls[value=2]").prop("checked", false);
        table.columns(2).visible(false);
    }
    if (jQuery.inArray("3", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=3]").prop("checked", true);
        table.columns(3).visible(true);
    } else {
        $(".filter-serach-cls[value=3]").prop("checked", false);
        table.columns(3).visible(false);
    }
    if (jQuery.inArray("4", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=4]").prop("checked", true);
        table.columns(4).visible(true);
    } else {
        $(".filter-serach-cls[value=4]").prop("checked", false);
        table.columns(4).visible(false);
    }

    if (jQuery.inArray("5", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=5]").prop("checked", true);
        table.columns(5).visible(true);
    } else {
        $(".filter-serach-cls[value=5]").prop("checked", false);
        table.columns(5).visible(false);
    }
    if (jQuery.inArray("6", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=6]").prop("checked", true);
        table.columns(6).visible(true);
    } else {
        $(".filter-serach-cls[value=6]").prop("checked", false);
        table.columns(6).visible(false);
    }

    if (jQuery.inArray("7", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=7]").prop("checked", true);
        table.columns(7).visible(true);
    } else {
        $(".filter-serach-cls[value=7]").prop("checked", false);
        table.columns(7).visible(false);
    }

    if (jQuery.inArray("9", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=9]").prop("checked", true);
        table.columns(9).visible(true);
    } else {
        $(".filter-serach-cls[value=9]").prop("checked", false);
        table.columns(9).visible(false);
    }

    if (jQuery.inArray("10", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=10]").prop("checked", true);
        table.columns(10).visible(true);
    } else {
        $(".filter-serach-cls[value=10]").prop("checked", false);
        table.columns(10).visible(false);
    }

    if (jQuery.inArray("11", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=11]").prop("checked", true);
        table.columns(11).visible(true);
    } else {
        $(".filter-serach-cls[value=11]").prop("checked", false);
        table.columns(11).visible(false);
    }

    if (jQuery.inArray("12", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=12]").prop("checked", true);
        table.columns(12).visible(true);
    } else {
        $(".filter-serach-cls[value=12]").prop("checked", false);
        table.columns(12).visible(false);
    }

    if (jQuery.inArray("13", Columnspilt) !== -1) {
        $(".filter-serach-cls[value=13]").prop("checked", true);
        table.columns(13).visible(true);
    } else {
        $(".filter-serach-cls[value=13]").prop("checked", false);
        table.columns(13).visible(false);
    }

    if (jQuery.inArray("14", Columnspilt) !== -1) {
     $(".filter-serach-cls[value=14]").prop("checked", true);
     table.columns(14).visible(true);
 } else {
    $(".filter-serach-cls[value=14]").prop("checked", false);
    table.columns(14).visible(false);
}

if (jQuery.inArray("16", Columnspilt) !== -1) {
 $(".filter-serach-cls[value=16]").prop("checked", true);
 table.columns(16).visible(true);
} else {
    $(".filter-serach-cls[value=16]").prop("checked", false);
    table.columns(16).visible(false);
}
}

$(".clearsorting").click(function() {
    $.session.remove("dataColumnall");
    $.session.remove("dataColumn");
    $(".filter-serach-cls").prop("checked", false);
    for (i = 2; i <= 14; i++) {
        if (i <= 14) {
            $(".filter-serach-cls[value=" + i + "]").prop("checked", true);
        } else {
            $(".filter-serach-cls[value=" + i + "]").prop("checked", false);
        }
    }
    $('input[name="export_filter"]').val('2,3,4,5,6,7,8,9,10,11,12,13,14');
    table.columns(columns_table).visible(false);
});
}
},
};
scout_login = {
    init: function() {
        $('#loginform').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: BASE_URL + "/check-email-not-register",
                        type: "post",
                        data: {
                            "_token": $('#csrf-token').val(),
                        },
                    },
                },
                password: {
                    required: true,
                },
            },
            messages: {
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                    remote: "Email not register"
                },
                password: {
                    required: "Please enter password.",
                },
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + '/loginCheck',
                    type: 'POST',
                    data: $('#loginform').serialize(),
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status) {
                            toastr.success(data.message);
                            window.location = BASE_URL + data.redirecturl;
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });
        $('#forgotpasswordform').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: BASE_URL + "/check-email-not-register",
                        type: "post",
                        data: {
                            "_token": $('#csrf-token').val(),
                        },
                    },
                },
            },
            messages: {
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                    remote: "Email not register"
                },
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + '/forgetPassword',
                    type: 'POST',
                    data: $('#forgotpasswordform').serialize(),
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status) {
                            toastr.success(data.message);
                            $('#forgotpasswordform')[0].reset();
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(response) {
                        $("#cover-spin").hide();
                        toastr.error('Something went wrong.Please try again.');
                    },
                });
            }
        });
        $("#resetPassword").validate({
            rules: {
                new_password: {
                    required: true,
                    minlength: 6,
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#new_password",
                }
            },
            messages: {
                new_password: {
                    required: 'Please enter password',
                    minlength: 'Please enter atleast 6 digits',
                },
                confirm_password: {
                    required: 'Please enter confirm password',
                    minlength: 'Please enter atleast 6 digits',
                    equalTo: "Password and confirm password does not match",
                }
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + '/resetPassword',
                    type: 'POST',
                    data: $('#resetPassword').serialize(),
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status) {
                            toastr.success(data.message);
                            window.location = BASE_URL + data.redirecturl;
                        } else {
                            toastr.error(data.message);
                        }
                    },
                    error: function(response) {
                        $("#cover-spin").hide();
                        toastr.error('Something went wrong.Please try again.');
                    },
                });
            }
        });
    },
};
scout_validation = {
    init: function() {
        scout_validation.validforms();
    },
    validforms: function() {
        $('#registerform').validate({
            rules: {
                username: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: BASE_URL + "/check-email-register",
                        type: "post",
                        data: {
                            "_token": $('#csrf-token').val(),
                        },
                    },
                },
                membercode: {
                    required: true,
                },
                join_date: {
                    required: true,
                },
                qrcode: {
                    required: true,
                },
                address: {
                    required: true,
                },
                hkidnumber: {
                    required: true,
                },
                emergencycontact: {
                    required: true,
                },
            },
            messages: {
                username: {
                    required: "Please enter username.",
                },
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                    remote: "Email already exists."
                },
                membercode: {
                    required: "Please enter membercode.",
                },
                join_date: {
                    required: "Please enter join date.",
                },
                qrcode: {
                    required: "Please enter qrcode.",
                },
                address: {
                    required: "Please enter address.",
                },
                hkidnumber: {
                    required: "Please enter hkidnumber.",
                },
                emergencycontact: {
                    required: "Please enter emergencycontact.",
                },
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                $.ajax({
                    url: BASE_URL + '/registerUser',
                    type: 'POST',
                    data: $('#registerform').serialize(),
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status) {
                            toastr.success(data.message);
                            window.location = BASE_URL + data.redirecturl;
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });
        $('#roleForm').validate({
            rules: {
                rolename: {
                    required: true,
                },
                'permissions[]': {
                    required: true,
                },
            },
            messages: {
                rolename: {
                    required: "Please enter role name."
                },
                'permissions[]': {
                    required: "Please enter module permission.",
                },
            },
        });

        $('#add_member_form').validate({
            rules: {
                team_effiective_date: {
                    required: true,
                },
                team: {
                    required: true,
                },
                elite_team: {
                    required: true,
                },
                Specialty_Instructor: {
                    required: true,
                },
                Specialty_Instructor_text: {
                    required: true,
                },
                rank_effiective_date: {
                    required: true,
                },
                Reference_number: {
                    required: true,
                },
                rank_team: {
                    required: true,
                },
                Chinese_name: {
                    required: true,
                    remote_valid: {
                        url: BASE_URL + "/check-chinese-name",
                        msg: 'Chinese name already exists.',
                        query: {
                            chinese_name: function() {
                                return $("#chinese_name").val();
                            },
                        }
                    },
                },
                English_name: {
                    required: true,
                    remote_valid: {
                        url: BASE_URL + "/check-english-name",
                        msg: 'English name already exists.',
                        query: {
                            english_name: function() {
                                return $("#english_name").val();
                            },
                        }
                    },
                },
                DOB: {
                    required: true,
                },
                Gender: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    remote_valid: {
                        url: BASE_URL + "/check-email",
                        msg: 'Email address already exists.',
                        query: {
                            email_address: function() {
                                return $("#email_address").val();
                            },
                        }
                    },
                },
                Contact_number: {
                    required: true,
                    number: true,
                    remote_valid: {
                        url: BASE_URL + "/check-contact-number",
                        msg: 'Contact number already exists.',
                        query: {
                            contact_number: function() {
                                return $("#contact_number").val();
                            },
                        }
                    },
                },
                // Remarks:{
                //  required:true,
                // },
                Remarks_desc: {
                    required: true,
                },
                JoinDate: {
                    required: true,
                },
                hour_point: {
                    required: true,
                    number: true,
                },
                'Attachment[]': {
                    extension: true,
                    filesize: true,
                },
                Status: {
                    required: true,
                },
            },
            messages: {
                team_effiective_date: {
                    required: "Please select effective date."
                },
                team: {
                    required: "Please select team.",
                },
                elite_team: {
                    required: "Please select team.",
                },
                Specialty_Instructor: {
                    required: "Please select specialty instructor.",
                },
                Specialty_Instructor_text: {
                    required: "Please enter specialty instructor.",
                },
                rank_effiective_date: {
                    required: "Please select effective date.",
                },
                Reference_number: {
                    required: "Please enter reference number.",
                },
                rank_team: {
                    required: "Please select rank.",
                },
                Chinese_name: {
                    required: "Please enter chinese name.",
                },
                English_name: {
                    required: "Please enter english name.",

                },
                DOB: {
                    required: "Please select date of birth.",
                },
                Gender: {
                    required: "Please select gender.",
                },
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                },
                Contact_number: {
                    required: "Please enter contact number.",
                    number: "Please enter valid contact number.",
                },
                // Remarks:{
                //  required:"Please select remark.",
                // },
                Remarks_desc: {
                    required: "Please select remark description.",
                },
                JoinDate: {
                    required: "Please selcet join date.",
                },
                hour_point: {
                    required: "Please enter hour point.",
                    number: "Please enter valid hour point.",
                },
                'Attachment[]': {
                    extension: "Only allowed jpg|jpeg|png|PNG|JPG|JPEG|pdf|csv|xls|xlsx|xlsm|gif|docx|docm|doc|dotx|dotm|dot file upload.",
                    filesize: "Only allow 1mb size attachment."
                },
                Status: {
                    required: "Please select status.",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "Specialty_Instructor") {
                    error.appendTo('.Specialty-Instructor-error-cls');
                } else if (element.attr("name") == "Gender") {
                    error.appendTo('.gender-error-cls');
                } else {
                    error.insertAfter(element);
                }
            },
        });

$('#edit_member_form').validate({
    rules: {
                // team_effiective_date:{
                //  required:true,
                // },
                // team:{
                //  required:true,
                // },
                // elite_team:{
                //  required:true,
                // },
                // Specialty_Instructor:{
                //  required:true,
                // },
                // Specialty_Instructor_text:{
                //  required:true,
                // },
                // rank_effiective_date:{
                //  required:true,
                // },
                // Reference_number:{
                //  required:true,
                // },
                // rank_team:{
                //  required:true,
                // },
                Chinese_name: {
                    required: true,
                    remote_valid: {
                        url: BASE_URL + "/check-chinese-name",
                        msg: 'Chinese name already exists.',
                        query: {
                            edit_chinese_name: function() {
                                return $("#chinese_name").val();
                            },
                            user_id: function() {
                                return $(".user_id").data('id');
                            },
                        }
                    },
                },
                English_name: {
                    required: true,
                    remote_valid: {
                        url: BASE_URL + "/check-english-name",
                        msg: 'English name already exists.',
                        query: {
                            edit_english_name: function() {
                                return $("#english_name").val();
                            },
                            user_id: function() {
                                return $(".user_id").data('id');
                            },
                        }
                    },
                },
                DOB: {
                    required: true,
                },
                Gender: {
                    required: true,
                },
                email: {
                    required: true,
                    email: true,
                    remote_valid: {
                        url: BASE_URL + "/check-email",
                        msg: 'Email address already exists.',
                        query: {
                            edit_email_address: function() {
                                return $("#email_address").val();
                            },
                            user_id: function() {
                                return $(".user_id").data('id');
                            },
                        }
                    },
                },
                Contact_number: {
                    required: true,
                    number: true,
                    remote_valid: {
                        url: BASE_URL + "/check-contact-number",
                        msg: 'Contact number already exists.',
                        query: {
                            edit_contact_number: function() {
                                return $("#contact_number").val();
                            },
                            user_id: function() {
                                return $(".user_id").data('id');
                            },
                        }
                    },
                },
                // Remarks:{
                //  required:true,
                // },
                Remarks_desc: {
                    required: true,
                },
                JoinDate: {
                    required: true,
                },
                hour_point: {
                    required: true,
                    number: true,
                },
                'Attachment[]': {
                    extension: true,
                    filesize: true,
                },
                Status: {
                    required: true,
                },
            },
            messages: {
                // team_effiective_date:{
                //  required:"Please select effective date."
                // },
                // team:{
                //  required:"Please select team.",
                // },
                // elite_team:{
                //  required:"Please select elite team.",
                // },
                // Specialty_Instructor:{
                //  required:"Please select specialty instructor.",
                // },
                // Specialty_Instructor_text:{
                //  required:"Please enter specialty instructor.",
                // },
                // rank_effiective_date:{
                //  required:"Please select effective date.",
                // },
                // rank_team:{
                //  required:"Please select rank.",
                // },
                // Reference_number:{
                //  required:"Please enter reference number.",
                // },
                Chinese_name: {
                    required: "Please enter chinese name.",
                },
                English_name: {
                    required: "Please enter english name.",
                },
                DOB: {
                    required: "Please select date of birth.",
                },
                Gender: {
                    required: "Please select gender.",
                },
                email: {
                    required: "Please enter email address.",
                    email: "Please enter valid email address.",
                },
                Contact_number: {
                    required: "Please enter contact number.",
                    number: "Please enter valid contact number.",
                },
                Remarks: {
                    required: "Please select remark.",
                },
                Remarks_desc: {
                    required: "Please select remark description.",
                },
                JoinDate: {
                    required: "Please selcet join date.",
                },
                hour_point: {
                    required: "Please enter hour point.",
                    number: "Please enter valid hour point.",
                },
                'Attachment[]': {
                    extension: "Only allowed jpg|jpeg|png|PNG|JPG|JPEG|pdf|csv|xls|xlsx|xlsm|gif|docx|docm|doc|dotx|dotm|dot file upload.",
                    filesize: "Only allow 1mb size attachment."
                },
                Status: {
                    required: "Please select status.",
                },
            },
            errorPlacement: function(error, element) {
                if (element.attr("name") == "Specialty_Instructor") {
                    error.appendTo('.specialty-error-cls');
                } else if (element.attr("name") == "Gender") {
                    error.appendTo('.gender-error-cls');
                } else {
                    error.insertAfter(element);
                }
            },
        });

$('#eventform_add').validate({
    ignore: ':hidden:not(.event_dates)',
    rules: {
        event_name: {
            required: true,
        },
        event_type: {
            required: true,
        },
        assessment: {
            required: true,
        },
        // event_token: {
        //     required: true,
        //     number:true,
        // },
        // event_money: {
        //     required: true,
        //     //number:true,
        // },
        assessment_text: {
            required: true,
        },
        startdate: {
            required: true,
        },
        start_time: {
            required: true,
        },
        enddate: {
            required: true,
            greaterThan: "#startdate",
        },
        end_time: {
            required: true,
        },
        event_dates:{
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        event_name: {
            required: "Please enter event name.",
        },
        event_type: {
            required: "Please select event type.",
        },
        assessment: {
            required: "Please select assesment requied?.",
        },
        // event_token: {
        //     required: "Please enter event token.",
        //     number: "Please enter only number.",
        // },
        // event_money: {
        //     required: "Please enter event money.",
        //    // number: "Please enter only number.",
        // },
        assessment_text: {
            required: "Please enter assesment required.",
        },
        startdate: {
            required: "Please select startdate.",
        },
        start_time: {
            required: "Please select starttime.",
        },
        enddate: {
            required: "Please select enddate.",
        },
        event_dates: {
            required: "Please select date.",
        },
        end_time: {
            required: "Please select endtime.",
        },
        status: {
            required: "Please select status.",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "event_dates") {
            $('.date-error-cls').text("Please select date");
        }
        else if (element.attr("name") == "assessment") {
            error.appendTo('.assesment-error-cls');
        } else {
            error.insertAfter(element);
        }
    },
});

$('#recurringeventform').validate({
    rules: {
        eventstartdate: {
            required: true,
        },
        starttime: {
            required: true,
        },
        eventenddate: {
            required: true,
            greaterThan: "#eventstartdate",
        },
        endtime: {
            required: true,
        },
        occurs: {
            required: true,
        },
        'weekly_occurs[]': {
            required: true,
        },
        monthly_occurs: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        eventstartdate: {
            required: "Please select startdate.",
        },
        starttime: {
            required: "Please select starttime.",
        },
        eventenddate: {
            required: "Please select enddate.",
        },
        endtime: {
            required: "Please select endtime.",
        },
        occurs: {
            required: "Please select occurs.",
        },
        'weekly_occurs[]': {
            required: "Please select week.",
        },
        monthly_occurs: {
            required: "Please select monthly event.",
        },
        status: {
            required: "Please select status.",
        },
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        startdate = $('#eventstartdate').val();
        enddate = $('#eventenddate').val();
        var result = '';
        var weeklydates = '';
        var dailyevents = '';
        var monthdates = [];
        if ($('#occurs').val() == 'Monthly') {
            var monthly_occurs = $('#monthly_occurs').val();
            if (monthly_occurs != '') {
                var explode_monthly = monthly_occurs.split("/");
                if (explode_monthly[1] == 'weekday') {
                    var weekname = explode_monthly[0];
                    if (weekname == 'first') {
                        monthday = 0;
                    } else if (weekname == 'second') {
                        monthday = 7;
                    } else if (weekname == 'third') {
                        monthday = 14;
                    } else if (weekname == 'fourth') {
                        monthday = 21;
                    } else if (weekname == 'fifth') {
                        monthday = 28;
                    }
                    var day = explode_monthly[2];
                    var result = scout_app.monthweekday(startdate, enddate, day, monthday);
                } else {
                    var date = new Date(startdate)
                    var date2 = new Date(enddate)
                    var diff = new Date(date2 - date);

                    let years = date2.getFullYear() - date.getFullYear();
                    let months1 = (years * 12) + (date2.getMonth() - date.getMonth());
                    var day = date.getDate();

                    for (let i = 0; i < months1; i++) {
                        let newdate = new Date(date.setMonth(date.getMonth() + 1));
                        if (newdate.getDate() != day) {
                            newdate = new Date(date.setDate(day));
                        }
                        var datemonthly = moment(new Date(newdate)).format('MM/DD/YYYY');
                        monthdates[i] = datemonthly;
                    }
                    monthdates.push(moment(startdate).format('MM/DD/YYYY'))
                }
            }
        }
        if ($('#occurs').val() == 'Weekly') {
            var weeklydates = scout_app.weeklydateget(startdate, enddate);
        }
        if ($('#occurs').val() == 'Daily') {
            var start = new Date(startdate),
            end = new Date(enddate),
            currentDate = new Date(start),
            between = [];

            while (currentDate <= end) {
                between.push(moment(new Date(currentDate)).format('MM/DD/YYYY'));
                currentDate.setDate(currentDate.getDate() + 1);
            }
            var dailyevents = between.join(",");
        }
        $.ajax({
            url: BASE_URL + '/recurringevent',
            type: 'POST',
            data: {
                '_token': $('#csrf-tokens').val(),
                "eventname": $('#eventname').val(),
                "eventtype": $('#eventtype').val(),
                "eventnumber": $('#eventnumber').val(),
                "assessment": $('input[name=assessment]:checked').val(),
                "experience": $('#experience').val(),
                "startdate": startdate,
                "starttime": $('#eventstarttime').val(),
                "eventenddate": enddate,
                "endtime": $('#eventendtime').val(),
                "eventselect": 'recurringevent',
                "eventhours": $('#totaleventhours').val(),
                "event_money": $('#event_money').val(),
                "event_token": $('#event_token').val(),
                "occurs": $('#occurs').val(),
                "weekly_occurs": val,
                'monthweekdate': result,
                'monthdates': monthdates,
                "monthly_occurs": monthly_occurs,
                "weekly_date": weeklydates,
                "daily_dates": dailyevents,
                "status": $('#status').val(),
            },
            success: function(response) {
                $("#cover-spin").hide();
                $('#eventstartdate').val('');
                $('#eventstarttime').val('');
                $('#eventenddate').val('');
                $('#eventendtime').val('');
                $('#eventhours').val('');
                $('#occurs').val('');
                $('#totaleventhours').val('0');
                $('#status').val('');
                $('.dailyoccurs-cls').hide();
                var data = JSON.parse(JSON.stringify(response));
                var eventData = data.data;
                if (data.status) {
                    $("#editeventmodel").attr('data-event-id', eventData.id);
                    $('#calendar').fullCalendar('refetchEvents', {
                        height: 600,
                        id: eventData.id,
                        title: eventData.event_name + '~' + eventData.start_time + '-' + eventData.end_time,
                        start: eventData.startdate,
                        end: eventData.enddate,
                        color: "#FF6600",
                        textColor: "#fff",
                    });
                    $('#eventmodel').modal('hide');
                    toastr.success(data.message);
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
});
$('#editRecurringEventform').validate({
    rules: {
        eventstartdate: {
            required: true,
        },
        starttime: {
            required: true,
        },
        eventenddate: {
            required: true,
            greaterThan: "#editeventstartdate",
        },
        endtime: {
            required: true,
        },
        occurs: {
            required: true,
        },
        'weekly_occurs[]': {
            required: true,
        },
        monthly_occurs: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        eventstartdate: {
            required: "Please select startdate.",
        },
        starttime: {
            required: "Please select starttime.",
        },
        eventenddate: {
            required: "Please select enddate.",
        },
        endtime: {
            required: "Please select endtime.",
        },
        occurs: {
            required: "Please select occurs.",
        },
        'weekly_occurs[]': {
            required: "Please select week.",
        },
        monthly_occurs: {
            required: "Please select monthly event.",
        },
        status: {
            required: "Please select status.",
        },
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        var id = $('#editRecurringEventform').attr('data-id');
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        var monthdates = [];
        var startdate = $('#editeventstartdate').val();
        var enddate = $('#editeventenddate').val();
        if ($('.editoccurs').val() == 'Monthly') {
            var explode_monthly = $('select.editmonthlycls').val().split("/");
            $(".monthlyoccurs").show();
            $(".dailyoccurs-cls").show();
            if (explode_monthly != '' && explode_monthly[1] == 'weekday') {
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
            } else {
                var date = new Date(startdate)
                var date2 = new Date(enddate)
                var diff = new Date(date2 - date);

                let years = date2.getFullYear() - date.getFullYear();
                let months1 = (years * 12) + (date2.getMonth() - date.getMonth());
                var day = date.getDate();

                for (let i = 0; i < months1; i++) {
                    let newdate = new Date(date.setMonth(date.getMonth() + 1));
                    if (newdate.getDate() != day) {
                        newdate = new Date(date.setDate(day));
                    }
                    var datemonthly = moment(new Date(newdate)).format('MM/DD/YYYY');
                    monthdates[i] = datemonthly;
                }
                monthdates.push(moment(startdate).format('MM/DD/YYYY'))
                $(".monthly_dates_cls").val(monthdates);
            }
        }
        if ($('.editoccurs').val() == 'Weekly') {
            var weeklydates = '';
            var weeklydates = scout_app.weeklydateget(startdate, enddate);
            $(".weeklydates").val(weeklydates);
        }
        if ($('.editoccurs').val() == 'Daily') {
            var start = new Date(startdate),
            end = new Date(enddate),
            currentDate = new Date(start),
            between = [];

            while (currentDate <= end) {
                between.push(moment(new Date(currentDate)).format('MM/DD/YYYY'));
                currentDate.setDate(currentDate.getDate() + 1);
            }
            $(".dailydates").val(between.join(","));
        }
        $.ajax({
            url: BASE_URL + '/recurringeventUpdate',
            type: 'POST',
            data: $('#editRecurringEventform').serialize(),
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                var eventData = data.data;
                if (data.status) {
                    $('#editeventmodel').modal('hide');
                    toastr.success(data.message);
                    $("#editeventmodel").attr('data-event-id', eventData.id);
                    $(".weeklydates").val('');
                    $(".dailydates").val('');
                    $(".weekmonthday").val('');
                    $(".monthly_dates_cls").val('');
                    $('#calendar').fullCalendar('refetchEvents', {
                        height: 600,
                        id: eventData.id,
                        title: eventData.event_name + '~' + eventData.start_time + '-' + eventData.end_time,
                        start: eventData.startdate,
                        end: eventData.enddate,
                        color: "#FF6600",
                        textColor: "#fff",
                    });
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
});

$("#event-assign-user").validate({
    rules: {
        'eventAssignuser[]': {
            required: true,
        },
    },
    messages: {
        'eventAssignuser[]': {
            required: "Please select assign user.",
        },
    },
    errorPlacement: function(error, element) {
        if (element.attr("name") == "eventAssignuser[]") {
            error.appendTo('.assing-user-cls-error');
        } else {
            error.insertAfter(element);
        }
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        $.ajax({
            url: BASE_URL + '/event-assign-user',
            type: 'POST',
            data: $('#event-assign-user').serialize(),
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.status) {
                    toastr.success(data.message);
                    location.reload();
                            // $('.user-assign-model').modal('hide');
                            // $('.assign-user-tq').modal('show');
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
    }
});

$("#languageForm").validate({
    rules: {
        enlanguage: {
            extension: "php",
        },
        chlanguage: {
            extension: "php",
        },
    },
    messages: {
        enlanguage: {
            extension: "Only allowed php file upload.",
        },
        chlanguage: {
            extension: "Only allowed php file upload.",
        },
    },
});

$("#attendanceForm").validate({
    rules: {
        members: {
            required: true,
        },
        eventName: {
            required: true,
        },
        inTime: {
            required: true,
        },
        outTime: {
            required: true,
        },
        date: {
            required: true,
        },
    },
    messages: {
        members: {
            required: "Please select member.",
        },
        eventName: {
            required: "Please select event name.",
        },
        inTime: {
            required: "Please select in time.",
        },
        outTime: {
            required: "Please select out time.",
        },
        date: {
            required: "Please select date.",
        },
    },
});

$("#specialtyForm").validate({
    rules: {
        chinesespecialty: {
            required: true,
        },
        englishspecialty: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chinesespecialty: {
            required: "Please enter chinese specialty.",
        },
        englishspecialty: {
            required: "Please enter english specialty.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#qualificationForm").validate({
    rules: {
        chinesequalification: {
            required: true,
        },
        englishqualification: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chinesequalification: {
            required: "Please enter chinese qualification.",
        },
        englishqualification: {
            required: "Please enter english qualification.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#relatedactivityForm").validate({
    rules: {
        chineserelatedhistroy: {
            required: true,
        },
        englishrelatedhistroy: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chineserelatedhistroy: {
            required: "Please enter chinese related activity history.",
        },
        englishrelatedhistroy: {
            required: "Please enter english related activity history.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#relatedactivityForm").validate({
    rules: {
        chineserelatedhistroy: {
            required: true,
        },
        englishrelatedhistroy: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chineserelatedhistroy: {
            required: "Please enter chinese related activity history.",
        },
        englishrelatedhistroy: {
            required: "Please enter english related activity history.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

/** record a event login **/
$('#attendEvent').validate({
    rules: {
        event_id: {
            required: true,
        }
    },
    messages: {
        event_id: {
            required: 'Please select event',
        }
    },
});
$("#remarksForm").validate({
    rules: {
        chineseremarks: {
            required: true,
        },
        englishremarks: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chineseremarks: {
            required: "Please enter chinese remarks.",
        },
        englishremarks: {
            required: "Please enter english remarks.",
        },
        status: {
            required: "Please select status.",
        },
    },
});
$("#eliteForm").validate({
    rules: {
        chineseelite: {
            required: true,
        },
        englishelite: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chineseelite: {
            required: "Please enter chinese elite.",
        },
        englishelite: {
            required: "Please enter english elite.",
        },
        status: {
            required: "Please select status.",
        },
    },
});
$("#subeliteForm").validate({
    rules: {
        elite: {
            required: true,
        },
        chinesesubelite: {
            required: true,
        },
        englishsubelite: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        elite: {
            required: "Please enter select elite.",
        },
        chinesesubelite: {
            required: "Please enter chinese sub elite.",
        },
        englishsubelite: {
            required: "Please enter english sub elite.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#subteamForm").validate({
    rules: {
        elite: {
            required: true,
        },
        chinesesubteam: {
            required: true,
        },
        englishsubteam: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        elite: {
            required: "Please enter select elite.",
        },
        chinesesubteam: {
            required: "Please enter chinese sub team.",
        },
        englishsubteam: {
            required: "Please enter english sub team.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#eventtypeForm").validate({
    rules: {
        chineseeventtype: {
            required: true,
        },
        englisheventtpye: {
            required: true,
        },
        status: {
            required: true,
        },
    },
    messages: {
        chineseeventtype: {
            required: "Please enter chinese event type.",
        },
        englisheventtpye: {
            required: "Please enter english event type.",
        },
        status: {
            required: "Please select status.",
        },
    },
});

$("#changepasswordform").validate({
    rules: {
        old_password: {
            required: true,
        },
        new_password: {
            required: true,
            minlength: 6,
        },
        confirm_password: {
            required: true,
            minlength: 6,
            equalTo: "#new_password",
        },
    },
    messages: {
        old_password: {
            required: "Please enter old password.",
        },
        new_password: {
            required: "Please enter new password.",
            minlength: 'Please enter atleast 6 digits.',
        },
        confirm_password: {
            required: "Please enter confirm password.",
            minlength: 'Please enter atleast 6 digits.',
            equalTo: "Password and confirm password does not match.",
        },
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        $.ajax({
            url: BASE_URL + '/changepassword',
            type: 'POST',
            data: $('#changepasswordform').serialize(),
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.status) {
                    toastr.success(data.message);
                    $('#changepasswordform')[0].reset();
                            //window.location = BASE_URL + data.redirecturl;
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
    }
});
$("#profileform").validate({
    rules: {
        image: {
            required: true,
            extension: "jpg|png|jpeg|PNG|JPG|JPEG",
        },
        Chinese_name: {
            required: true,
        },
        English_name: {
            required: true,
        },
        Chinese_address: {
            required: true,
        },
        English_address: {
            required: true,
        },
    },
    messages: {
        image: {
            required: "Please select image.",
            extension: "Only allowed jpg|png|jpeg file upload.",
        },
        Chinese_name: {
            required: "Please enter chinese name.",
        },
        English_name: {
            required: "Please enter english name.",
        },
        Chinese_address: {
            required: "Please enter chinese address.",
        },
        English_address: {
            required: "Please enter english address.",
        },
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        var formData = new FormData($('#profileform')[0]);
        $.ajax({
            url: BASE_URL + '/profile',
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData: false,
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.status) {
                    toastr.success(data.message);
                    location.reload();
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
});

$("#settingsForm").validate({
    rules: {
        image: {
                    //required: true,
                    extension: "jpg|png|jpeg|PNG|JPG|JPEG",
                },
                HKD: {
                    required: true,
                    number: true,
                },
                SiteName: {
                    required: true,
                },
                min_hour: {
                    required: true,
                    number: true,
                },
                tokenExpireDay: {
                    required: true,
                    number: true,
                },
            },
            messages: {
                image: {
                    //required: "Please select image.",
                    extension: "Only allowed jpg|png|jpeg file upload.",
                },
                HKD: {
                    required: "Please enter HKD.",
                    number: "Please enter valid HKD.",
                },
                SiteName: {
                    required: "Please enter sitename.",
                },
                min_hour: {
                    required: "Please enter early/late margin.",
                    number: "Please enter valid early/late margin.",
                },
                tokenExpireDay: {
                    required: "Please enter token expire day.",
                    number: "Please enter days in numbers.",
                },
            },
            submitHandler: function(form) {
                $("#cover-spin").show();
                var formData = new FormData($('#settingsForm')[0]);
                $.ajax({
                    url: BASE_URL + '/settings/update',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(response) {
                        $("#cover-spin").hide();
                        var data = JSON.parse(JSON.stringify(response));
                        if (data.status) {
                            toastr.success(data.message);
                            location.reload();
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
            }
        });
$(".membershipnumberForm").validate({
    rules: {
        MemberCode: {
            required: true,
        },
    },
    messages: {
        MemberCode: {
            required: "Please enter membercode.",
        },
    },
    submitHandler: function(form) {
        if ($('input.useCoin').is(':checked')) {
            //user want to used coin
            var usedCoin = '1';
        }else{
            //user does not want to used coin
            var usedCoin = '0';
        }
        $("#cover-spin").show();
        var membercode = $('#MemberCodelogout').val();
        if ($("#MemberCode").val() != '') {
            var membercode = $('#MemberCode').val();
        } else {
            var membercode = $('#MemberCodelogout').val();
        }
        $.ajax({
            url: BASE_URL + '/recordMemberCodeAttendance',
            type: 'POST',
            data: {
                '_token': $('meta[name="csrf-token"]').attr('content'),
                'event_id': $('.currentevent').val(),
                'type': $(".attendances_type").val(),
                'scheduleID': $(".scheduleID").val(),
                'MemberCode': membercode,
                'usedCoin' : usedCoin,
            },
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.status) {
                    toastr.success(data.message);
                    $('#eventAttend').modal('hide');
                    $('#eventLogoutModal').modal('hide');
                    $.ajax({
                        type: "GET",
                        url: BASE_URL + "/getEventAttenderList/" + $('.currentevent').val(),
                        data: {},
                        success: function(response) {
                            var data = JSON.parse(JSON.stringify(response));
                            if (data.list) {
                                var list = data.list;
                                $("#tbodyid").empty();
                                $('#attendanceTable tbody').html(list);
                            } else {
                                $("#tbodyid").empty();
                                $('#attendanceTable tbody').html('<tr class="odd"><td valign="top" colspan="8" class="dataTables_empty">No data available in table</td></tr></tr>');
                            }
                        }
                    });
                } else {
                    toastr.error(data.message);
                }
            },
            error: function(error) {
                toastr.error("Something went wrong.");
            }
        });
    }
});
$("#productForm").validate({
    rules: {
        product_name: {
            required: true,
        },
        product_sku: {
            required: true,
        },
        uniformType: {
            required: true,
        },
        size: {
            required: true,
        },
        product_amount: {
            required: true,
            number: true,
        },
        'product_image[]': {
            required: true,
            extension: "jpg|png|jpeg|PNG|JPG|JPEG",
        },
    },
    messages: {
        product_name: {
            required: "Please enter product name.",
        },
        product_sku: {
            required: 'Please enter product sku.',
        },
        uniformType: {
            required: "Please select uniform type.",
        },
        size: {
            required: "Please select size.",
        },
        product_amount: {
            required: 'Please enter amount.',
            number: 'Please enter valid amount.',
        },
        'product_image[]': {
            required: 'Please select product image.',
            extension: "Only allowed jpg|png|jpeg file upload.",
        },
    },
});
$("#editproductForm").validate({
    rules: {
        product_name: {
            required: true,
        },
        product_sku: {
            required: true,
        },
        uniformType: {
            required: true,
        },
        size: {
            required: true,
        },
        product_amount: {
            required: true,
            number: true,
        },
        'product_image[]': {
            extension: "jpg|png|jpeg|PNG|JPG|JPEG",
        },
    },
    messages: {
        product_name: {
            required: "Please enter product name.",
        },
        product_sku: {
            required: 'Please enter product sku.',
        },
        uniformType: {
            required: "Please select uniform type.",
        },
        size: {
            required: "Please select size.",
        },
        product_amount: {
            required: 'Please enter amount.',
            number: 'Please enter valid amount.',
        },
        'product_image[]': {
            extension: "Only allowed jpg|png|jpeg file upload.",
        },
    },
});

$("#orderForm").validate({
    rules: {
        firstname: {
            required: true,
        },
        lastname: {
            required: true,
        },
        street_address: {
            required: true,
        },
        city: {
            required: true,
        },
        country: {
            required: true,
        },
        postcode: {
            required: true,
            number: true,
        },
        phname: {
            required: true,
            number: true,
        },
        email: {
            required: true,
            email: true,
        },

        ship_first_name: {
            required: true,
        },
        ship_last_name: {
            required: true,
        },
        ship_street_address: {
            required: true,
        },
        ship_city: {
            required: true,
        },
        ship_country: {
            required: true,
        },
        ship_postcode: {
            required: true,
            number: true,
        },
        ship_phone_number: {
            required: true,
            number: true,
        },
        ship_email: {
            required: true,
            email: true,
        },
    },
    messages: {
        firstname: {
            required: "Please enter first name.",
        },
        lastname: {
            required: "Please enter last name.",
        },
        street_address: {
            required: "Please enter street address.",
        },
        city: {
            required: "Please enter city.",
        },
        country: {
            required: "Please enter country.",
        },
        postcode: {
            required: "Please enter postcode.",
            number: "Please enter valid postcode.",
        },
        phname: {
            required: "Please enter phone number.",
            number: "Please enter valid phone number.",
        },
        email: {
            required: "Please enter email address.",
            email: "Please enter valid email address.",
        },

        ship_first_name: {
            required: "Please enter first name.",
        },
        ship_last_name: {
            required: "Please enter last name.",
        },
        ship_street_address: {
            required: "Please enter street address.",
        },
        ship_city: {
            required: "Please enter city.",
        },
        ship_country: {
            required: "Pleas enter country.",
        },
        ship_postcode: {
            required: "Please enter postcode.",
            number: "Please enter valid postcode.",
        },
        ship_phone_number: {
            required: "Please enter phone number.",
            number: "Please enter valid phone number.",
        },
        ship_email: {
            required: "Please enter email address.",
            email: "Please enter valid email address.",
        },
    },
    submitHandler: function(form) {
        $("#cover-spin").show();
        $.ajax({
            url: BASE_URL + '/add-order',
            type: 'POST',
            data: $('#orderForm').serialize(),
            success: function(response) {
                $("#cover-spin").hide();
                var data = JSON.parse(JSON.stringify(response));
                if (data.status) {
                    toastr.success(data.message);
                            //window.location = BASE_URL + data.redirecturl;
                        } else {
                            toastr.error(data.message);
                        }
                    }
                });
    }
});
},
};

scout_app = {
    init: function() {},

    homecountdata: function($data, $color, $id) {
        var homeSuccessoptions = {
            chart: {
                height: 40,
                width: 40,
                type: "radialBar"
            },
            grid: {
                show: false,
                padding: {
                    left: -30,
                    right: -30,
                    top: 0,
                }
            },
            series: [$data],
            colors: [$color],
            plotOptions: {
                radialBar: {
                    hollow: {
                        size: "30%"
                    },
                    dataLabels: {
                        showOn: "always",
                        name: {
                            show: false
                        },
                        value: {
                            show: false,
                        }
                    }
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: "horizontal",
                    gradientToColors: [$color],
                    opacityFrom: 1,
                    opacityTo: 0.8,
                    stops: [0, 70, 100]
                }
            },
            stroke: {
                lineCap: "round",
            }
        };
        var homeCountChart = new ApexCharts(
            document.querySelector($id),
            homeSuccessoptions
            );
        homeCountChart.render();
    },

    hideshowhourus: function() {
        $(".event-hours-cls").show();
        var time1 = $("#start_time").val();
        var time2 = $("#endtime").val();

        var starttime = $('#startdate').val();
        var endtime = $('#startdate').val();
        var hrs = scout_app.timediffernt(time1, time2, starttime, endtime);
        $(".totaleventhours1").val(hrs);
    },

    attendanceHours: function() {
        var intime = $('#inTime').val();
        var outtime = $('#outTime').val();
        var startTime = moment(intime, "HH:mm:ss a");
        var endTime = moment(outtime, "HH:mm:ss a");
        var duration = moment.duration(endTime.diff(startTime));
        var hours = parseInt(duration.asHours());
        var totalhrs = hours;
        $('#hours').val(totalhrs);
    },

    totaleventHours: function() {
        var time1 = $("#eventstarttime").val();
        var time2 = $("#eventendtime").val();
        var starttime = $('#eventstartdate').val();
        var endtime = $('#eventstartdate').val();
        var hrs = scout_app.timediffernt(time1, time2, starttime, endtime);
        $(".totaleventhours").val(hrs);
        var startDate = new Date($('#eventstartdate').val());
        var endDate = new Date($('#eventenddate').val());
        var occurs = $("#occurs").val();
        if (occurs == 'Daily') {
            var diff = new Date(endDate - startDate);
            var days = diff / 1000 / 60 / 60 / 24;
            var totaldays = ((days + 1) * hrs);
            $(".totaleventhours").val(totaldays);
        }
        if (occurs == 'Weekly') {
            var val = [];
            $(':checkbox:checked').each(function(i) {
                val[i] = $(this).val();
            });

            function getweekCountBetweenDates(startDate, endDate) {
                var totalWeeks = 0;
                for (var i = startDate; i <= endDate; i.setDate(i.getDate() + 1)) {
                    $.each(val, function(index, val) {
                        if (i.getDay() == val) {
                            totalWeeks++;
                        }
                    });
                }
                return totalWeeks;
            }
            var startDate = new Date($('#eventstartdate').val());
            var endDate = new Date($('#eventenddate').val());
            var weekcnt = getweekCountBetweenDates(startDate, endDate);
            if (weekcnt != 0) {
                var totalweekhours = ((weekcnt) * hrs);
                $(".totaleventhours").val(totalweekhours);
            } else {
                $(".totaleventhours").val(hrs);
            }
        }

        if (occurs == 'Monthly') {
            var monthly_occur = $('select.selectmonthlycls').children("option:selected").val();
            var explodedata = monthly_occur.split("/");
            if (explodedata != '' && explodedata[1] == 'month') {
                var date = new Date($('#eventstartdate').val())
                var date2 = new Date($('#eventenddate').val())
                var diff = new Date(date2 - date);

                let years = date2.getFullYear() - date.getFullYear();
                let months1 = (years * 12) + (date2.getMonth() - date.getMonth());
                var day = date.getDate();
                monthdates = [];
                for (let i = 0; i < months1; i++) {
                    let newdate = new Date(date.setMonth(date.getMonth() + 1));
                    if (newdate.getDate() != day) {
                        newdate = new Date(date.setDate(day));
                    }
                    var datemonthly = moment(new Date(newdate)).format('MM/DD/YYYY');
                    monthdates[i] = datemonthly;
                }
                monthdates.push(moment(startDate).format('MM/DD/YYYY'))
                if (monthdates.length != '') {
                    $(".totaleventhours").val(monthdates.length);
                }
            }
            if (explodedata != '' && explodedata[1] == 'weekday') {
                var montly_date = explodedata[0];
                var montly_day = explodedata[2];
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
                var result = scout_app.monthweekday(startDate, endDate, montly_day, monthday);
                if (result != '') {
                    var totalhours = ((result.length + 1) * hrs);
                    $(".totaleventhours").val(totalhours);
                } else {
                    $(".totaleventhours").val(hrs);
                }
            }
        }
    },

    weeklydateget: function(startdate, enddate) {
        var startDate = new Date(startdate);
        var endDate = new Date(enddate);
        var val = [];
        $(':checkbox:checked').each(function(i) {
            val[i] = $(this).val();
        });
        var weeklydate = [];
        for (var i = startDate; i <= endDate; i.setDate(i.getDate() + 1)) {
            $.each(val, function(index, val) {
                if (i.getDay() == val) {
                    month = i.getMonth() + 1;
                    result = month + '/' + i.getDate() + '/' + i.getFullYear();
                    weeklydate.push(moment(result).format('MM/DD/YYYY'));
                }
            });
        }
        var unique = weeklydate.filter(function(itm, i, a) {
            return i == a.indexOf(itm);
        });
        return unique;
    },

    totaleventeditHours: function() {
        var time1 = $("#editeventstarttime").val();
        var time2 = $("#editeventendtime").val();
        var starttime = $('#editeventstartdate').val();
        var endtime = $('#editeventstartdate').val();
        var hrs = scout_app.timediffernt(time1, time2, starttime, endtime);
        var startDate = new Date($('#editeventstartdate').val());
        var endDate = new Date($('#editeventenddate').val());
        var occurs = $('select.editoccurs').children("option:selected").val();
        $("#edittotaleventhours").val(hrs);
        if (occurs == 'Once') {
            $("#edittotaleventhours").val(hrs);
        }
        if (occurs == 'Daily') {
            var diff = new Date(endDate - startDate);
            var days = diff / 1000 / 60 / 60 / 24;
            var totaldays = ((days + 1) * hrs);
            $("#edittotaleventhours").val(totaldays);
        }
        if (occurs == 'Weekly') {
            var val = [];
            $(':checkbox:checked').each(function(i) {
                val[i] = $(this).val();
            });

            function getweekCountBetweenDates(startDate, endDate) {
                var totalWeeks = 0;
                for (var i = startDate; i <= endDate; i.setDate(i.getDate() + 1)) {
                    $.each(val, function(index, val) {
                        if (i.getDay() == val) {
                            totalWeeks++;
                        }
                    });
                }
                return totalWeeks;
            }
            var weekcnt = getweekCountBetweenDates(startDate, endDate);
            if (weekcnt != 0) {
                var totalweekhours = ((weekcnt) * hrs);
                $("#edittotaleventhours").val(totalweekhours);
            } else {
                $("#edittotaleventhours").val(hrs);
            }
        }

        if (occurs == 'Monthly') {
            var monthly_occur = $('select.editmonthlycls').children("option:selected").val();
            var explodedata = monthly_occur.split("/");
            if (explodedata != '' && explodedata[1] == 'month') {
                endDate.setDate(endDate.getDate() - startDate.getDate());
                var months;
                months = (endDate.getFullYear() - startDate.getFullYear()) * 12;
                months -= startDate.getMonth() + 1;
                months += endDate.getMonth();
                months <= 0 ? 0 : months;
                if (months != -1) {
                    var totalmonths = months + 1;
                    var totaldays = ((totalmonths + 1) * hrs);
                    $("#edittotaleventhours").val(totaldays);
                } else {
                    $("#edittotaleventhours").val(hrs);
                }
            }
            if (explodedata != '' && explodedata[1] == 'weekday') {
                var montly_date = explodedata[0];
                var montly_day = explodedata[2];
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
                var result = scout_app.monthweekday(startDate, endDate, montly_day, monthday);
                if (result != '') {
                    var totalhours = ((result.length + 1) * hrs);
                    $("#edittotaleventhours").val(totalhours);
                } else {
                    $("#edittotaleventhours").val(hrs);
                }
            }
        }
    },

    totalReeventhours: function() {
        var time1 = $("#editeventstarttime").val();
        var time2 = $("#editeventendtime").val();

        var starttime = $('#editeventstartdate').val();
        if ($('#editeventenddate').val() != '') {
            var endtime = $('#editeventenddate').val();
        } else {
            var endtime = $('#editeventstartdate').val();
        }
        var hrs = scout_app.timediffernt(time1, time2, starttime, endtime);
        $(".totaleventhours").val(hrs);
    },

    timediffernt: function(time1, time2, starttime, endtime) {
        var time1 = time1;
        var time2 = time2;
        if (time1 == '0:00') {
            var time1 = '24:00';
        }
        if (time2 == '0:00') {
            var time2 = '24:00';
        }
        var date = new Date(starttime);
        // start_yr      = date.getFullYear(),
        // start_month   = date.getMonth(),
        // start_day     = date.getDate(),
        // startnewDate = start_yr + '-' + start_month + '-' + start_day;
        var startnewDate = moment(date).format('YYYY-MM-DD');

        var enddate = new Date(endtime);
        // end_yr      = enddate.getFullYear(),
        // end_month   = enddate.getMonth(),
        // end_day     = enddate.getDate(),
        // endnewDate = end_yr + '-' + end_month + '-' + end_day;
        var endnewDate = moment(enddate).format('YYYY-MM-DD');
        var date1 = new Date(startnewDate + " " + time1).getTime();
        var date2 = new Date(endnewDate + " " + time2).getTime();
        var msec = date2 - date1;
        var mins = Math.floor(msec / 60000);
        var hrs = Math.floor(mins / 60);
        return hrs;
    },

    changeMonthlyOccurs: function(startdate) {
        var startdate = new Date(startdate);
        var days = ['0', '1', '2', '3', '4', '5', '6'];
        var date = startdate.getDate() < 10 ? '0' + startdate.getDate() : startdate.getDate();
        var day = startdate.toLocaleDateString('en-us', {
            weekday: 'long'
        });
        var dates = date + ((31 - date >= 0) ? "th" : ["st", "nd", "rd"][(date % 10) - 1] || "th");
        prefixes = ['首先', '第二', '第三', '第四', '第五'];
        var weekofday_ch = prefixes[Math.floor(startdate.getDate() / 7)]
        var weekofday = scout_app.weekAndDay(startdate);
        if ($('html').is(':lang(ch)')) {
            html = '<select class="form-control selectmonthlycls" id="monthly_occurs" name="monthly_occurs"><option value="">選擇每月</option><option value="' + date + '/month">在 ' + dates + '</option><option value="' + weekofday + '/weekday/' + days[startdate.getDay()] + '">在 ' + weekofday_ch + ' ' + day + '</option></select>';
        } else {
            html = '<select class="form-control selectmonthlycls" id="monthly_occurs" name="monthly_occurs"><option value="">Select Monthly</option><option value="' + date + '/month">On the ' + dates + '</option><option value="' + weekofday + '/weekday/' + days[startdate.getDay()] + '">On the ' + weekofday + ' ' + day + '</option></select>';
        }
        return html;
    },

    changeeditMonthlyOccurs: function(startdate) {
        var startdate = new Date(startdate);
        var days = ['0', '1', '2', '3', '4', '5', '6'];
        var date = startdate.getDate() < 10 ? '0' + startdate.getDate() : startdate.getDate();
        var day = startdate.toLocaleDateString('en-us', {
            weekday: 'long'
        });
        var dates = date + ((31 - date >= 0) ? "th" : ["st", "nd", "rd"][(date % 10) - 1] || "th");
        prefixes = ['首先', '第二', '第三', '第四', '第五'];
        var weekofday_ch = prefixes[Math.floor(startdate.getDate() / 7)]
        var weekofday = scout_app.weekAndDay(startdate);
        if ($('html').is(':lang(ch)')) {
            html = '<select class="form-control editmonthlycls" id="monthly_occurs" name="monthly_occurs" disabled><option value="">選擇每月</option><option value="' + date + '/month">在 ' + dates + '</option><option value="' + weekofday + '/weekday/' + days[startdate.getDay()] + '">在 ' + weekofday_ch + ' ' + day + '</option></select>';
        } else {
            html = '<select class="form-control editmonthlycls" id="monthly_occurs" name="monthly_occurs" disabled><option value="">Select Monthly</option><option value="' + date + '/month">On the ' + dates + '</option><option value="' + weekofday + '/weekday/' + days[startdate.getDay()] + '">On the ' + weekofday + ' ' + day + '</option></select>';
        }
        return html;
    },

    weekAndDay: function(date) {
        prefixes = ['first', 'second', 'third', 'fourth', 'fifth'];
        return prefixes[Math.floor(date.getDate() / 7)];
    },
    update_cart_amounts: function() {
        var grandtotal = 0.0;
        $('.products-cart-list').each(function() {
            var qty = parseFloat($(this).find('span.productAmount-cls').attr('data-amount') || 0, 10);
            var price = parseFloat($(this).find('.qty-cls').val() || 0, 10);
            var amount = (qty * price)
            grandtotal += amount;
            $('.totalamount-cls').text(grandtotal);
            $('.totalsubamount-cls').text(grandtotal);
        });
    },
    monthweekday: function(startdate, enddate, day, monthday) {
        var startdate = moment(startdate).format('MM/DD/YYYY');
        var endatedate = moment(enddate).format('MM/DD/YYYY');
        var start = moment(startdate),
        end = moment(endatedate),
            day = parseInt(day); // Friday
            var result = [];
            var current = start.clone();

            while (current.day(7 + day).isBefore(end)) {
                result.push(current.clone());
            }
            var allweeekday = result.map(m => m.format('LLLL'));
            var result = [];
            $.each(allweeekday, function(key, value) {
                date = new Date(value);
                weekDate = ((date.getMonth() > 8) ? (date.getMonth() + 1) : ('0' + (date.getMonth() + 1))) + '/' + ((date.getDate() > 9) ? date.getDate() : ('0' + date.getDate())) + '/' + date.getFullYear();
                var parts = weekDate.split('/');
                var monthStartDate = parts[0] + '/01/' + parts[2];
                var monthDate = new Date(monthStartDate);
                var weekCount = 0;
                while (weekCount < 3) {
                    if (monthDate.getDay() === day) {
                        break;
                    }
                    monthDate = new Date(monthDate.getYear() + 1900, monthDate.getMonth(), (monthDate.getDate() + 1));
                }
                var checkweek = new Date(monthDate.getYear() + 1900, monthDate.getMonth(), (monthDate.getDate() + parseInt(monthday)));
                checkDate = new Date(checkweek);
                var checkweekDate = ((checkDate.getMonth() > 8) ? (checkDate.getMonth() + 1) : ('0' + (checkDate.getMonth() + 1))) + '/' + ((checkDate.getDate() > 9) ? checkDate.getDate() : ('0' + checkDate.getDate())) + '/' + checkDate.getFullYear();
                if (weekDate == checkweekDate) {
                    result.push(weekDate);
                }
            });
            return result;
        },

        fullCalendarfun: function() {
            var fullCalendar = $('#calendar').fullCalendar({
                showNonCurrentDates: false,
                height: 600,
                defaultView: 'month',
                /*dayClick: function(date, jsEvent, view) {
                    var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    var eventdate = new Date(date.format('YYYY-MM-DD'));
                    var Dayname = weekday[eventdate.getDay()];
                    var Monthname = monthNames[eventdate.getMonth()];
                    var daydate = moment(eventdate).format('DD');
                    var Year = eventdate.getFullYear();
                    var finaldate = Dayname + ',' + daydate + ' ' + Monthname + ',' + Year;
                    $('#eventstartdate').val(finaldate);
                    var html = scout_app.changeMonthlyOccurs(finaldate);
                    $(".occurs").val('');
                    $(".dailyoccurs-cls").hide();
                    $(".weeklyoccurs").hide();
                    $(".monthlyoccurs").hide();
                    $(".occur-monthly-cls").html(html);
                    $(".occur-monthly-clsss").remove();
                    if (!$('#eventform').valid()) {
                        $("#eventform").valid();
                        $('#eventmodel').modal('hide');
                        $("html, body").animate({
                            scrollTop: 0
                        }, "slow");
                        return false;
                    } else {
                        if (moment().format('YYYY-MM-DD') === date.format('YYYY-MM-DD') || date.isAfter(moment())) {
                            $('#eventmodel').modal({
                                backdrop: false
                            });
                        } else {
                            $('#eventmodel').modal('hide');
                        }
                    }
                },*/
               /* dayRender: function(date, cell) {
                    if (moment().format('YYYY-MM-DD') === date.format('YYYY-MM-DD') || date.isAfter(moment())) {
                        if ($('html').is(':lang(ch)')) {
                            cell.append("<span class='hoverEffect' style='display:none;'>+ 添加日期</span>");
                        } else {
                            cell.append("<span class='hoverEffect' style='display:none;'>+ Add Dates</span>");
                        }
                        cell.mouseenter(function() {
                            cell.find(".hoverEffect").show();
                            cell.css("background", "#ffff");
                        }).mouseleave(function() {
                            $(".hoverEffect").hide();
                            cell.removeAttr('style');
                        });
                    } else {
                        $(".hoverEffect").hide();
                        cell.removeAttr('style');
                    }
                },*/
                eventRender: function(event, element, view) {
                    element.find(".fc-title").remove();
                    var events = event.title.split("~");
                    var new_description = events[0] + '</br>' + events[1];
                    element.append(new_description);
                // if(event.occurs != 'undefined'){
                //  element.find(".fc-title").remove();
                //  var events = event.title.split("~");
                //  var new_description =   events[0] + ' - '+ events[1] +'</br>'+ events[2];
                //  element.append(new_description);
                //  if ((event.ranges.filter(function(range) {
                //      return (event.start.isBefore(range.end) &&
                //      event.end.isAfter(range.start));
                //      }).length) > 0) {
                //      if (event.frequency == "m") { //check whether repetition is monthly
                //          return ($.inArray(event.start.date(), event.dom) > -1); //show the event if the date of the month is in the defined days of the month for this event
                //      } else {
                //        return true;
                //      }
                //  } else {
                //      return false;
                //  };
                // }else{
                //  element.find(".fc-title").remove();
                //  var events = event.title.split("~");
                //  var new_description =   events[0] + ' - '+ events[1] +'</br>'+ events[2];
                //  element.append(new_description);
                // }
                // if(event.occurs != 'undefined' && event.occurs == 'Monthly'){
                //  if ((event.ranges.filter(function(range) {
                //      return (event.start.isBefore(range.end) &&
                //      event.end.isAfter(range.start));
                //  }).length) > 0) {
                //      if (event.frequency == "m") { //check whether repetition is monthly
                //          return ($.inArray(event.start.date(), event.dom) > -1); //show the event if the date of the month is in the defined days of the month for this event
                //      } else {
                //          return true;
                //      }
                //  } else {
                //      return false;
                //  };
                // }
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
                 if(action2 == "create"){
                    return false;
                }
                if(data.updatePublish == '0'){
                    return false;
                }
                if (eventcode == $("#eventnumber").val() && data.updatePublish == '1'|| action1 == "eventManagement" && action2 == "create") {

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
},

getEventfunc: function(callback) {
    var events = [];
    $.ajax({
        url: BASE_URL + "/event-get",
        type: 'GET',
        dataType: 'json',
        data: '',
        success: function(doc) {
            if (doc != null) {
                var colorgreyCode = '#D3D3D3';
                var eventnumber = $('#eventnumber').val();
                $.each(doc, function(index, value) {
                   var eventScheduleData = value.scheduleData;

                   $.each(eventScheduleData, function( key, value1 ) {
                    var todayDate = moment(new Date).format('YYYY-MM-DD');
                    var momentstartdate = moment(value1.date).format('YYYY-MM-DD');

                    var segments = location.pathname.split('/');
                    if (segments != undefined) {
                        var action1 = segments[1];
                        var action2 = segments[2];
                    }

                    if (eventnumber == value.event_code) {
                        if(value.status == '1'){
                            var dt = new Date();
                            var input = value1.date;
                            var schedule_date = input.replace(/(\d\d)\/(\d\d)\/(\d{4})/, "$3-$1-$2");
                            var time = dt.getHours() + ":" + dt.getMinutes();
                            var diff = ( new Date(schedule_date+" "+  value1.start_time) - new Date(todayDate+" "+  time) ) / 1000 / 60 / 60;

                            if(diff > 0){
                                var eventcolor = '#FF6600'; // orange color
                                if (action1 == "eventManagement" && action2 == "create") {
                                    var classname = 'orage_event_add_cls';
                                } else {
                                    var classname = 'orage_event_cls';
                                }
                                var updatePublish = '1';
                            }else{
                                var eventcolor = '#FF6600'; // Blue color
                                if (action1 == "eventManagement" && action2 == "create") {
                                //var classname = 'blue_event_add_cls';
                                var classname = 'orage_event_add_cls';
                                } else {
                                    var classname = 'blue_event_cls';
                                }
                                var updatePublish = '0';
                                var eventCode = value.event_code;
                             }
                        }else{
                              var updatePublish = '0';
                            if (action1 == "eventManagement" && action2 == "create") {
                                var classname = 'orage_event_add_cls';
                            } else {
                                var classname = 'orage_event_cls';
                            }
                        }
                    var eventCode = value.event_code;

                } else {
                      var updatePublish = '0';
                                var eventcolor = '#2c6de9'; // Blue color
                                if (action1 == "eventManagement" && action2 == "create") {
                                //var classname = 'blue_event_add_cls';
                                var classname = 'blue_event_cls';
                            } else {
                                var classname = 'blue_event_cls';
                            }
                            var eventCode = value.event_code;
                        }
                        
                        /* if (todayDate <= momentstartdate) {*/
                            if ($('#editeventmodel').attr('data-event-id') == value.id) {
                                events.push({
                                    id: value.id,
                                    //title: value.event_name + '~' + 'Once' + '~' + value1.start_time + '-' + value1.end_time,
                                    title: value.event_name + '~' + value1.start_time + '-' + value1.end_time,
                                    start: momentstartdate,
                                    end: momentstartdate,
                                    color: '#FF6600',
                                    textColor: "#fff",
                                    occurs: value.occurs,
                                    eventcode: eventCode,
                                    updatePublish:updatePublish,
                                });
                            } else {
                                events.push({
                                    id: value.id,
                                    //title: value.event_name + '~' + 'Once' + '~' + value1.start_time + '-' + value1.end_time,
                                    title: value.event_name + '~' + value1.start_time + '-' + value1.end_time,
                                    start: momentstartdate,
                                    end: momentstartdate,
                                    color: eventcolor,
                                    textColor: "#fff",
                                    occurs: value.occurs,
                                    className: classname,
                                    eventcode: eventCode,
                                    updatePublish:updatePublish,
                                });
                            }
                       /* } else {
                            events.push({
                                id: value.id,
                                title: value.event_name + '~' + 'Once' + '~' + value1.start_time + '-' + value1.end_time,
                                start: momentstartdate,
                                end: momentstartdate,
                                    color: colorgreyCode, // Grey color
                                    textColor: "#000000",
                                    occurs: value.occurs,
                                    className: 'grey_event_cls',
                                    eventcode: eventCode,
                                });
                            }*/
                         }); //endforeach
                    }); //endforeach
}

                callback(events); //you have to pass the list of events to fullCalendar!
            }
        });
},
};
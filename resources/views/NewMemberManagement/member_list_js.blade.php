<script>
document.getElementById('pagination').onchange = function() { 
	window.location = "{!! $userData->url(1) !!}&items=" + this.value; 
};
</script>
<script>
var useridarr = [];
$(function () {
    // Always show Member-list page first checkbox column and Last Action columns
    $('.member-list-table .action-option, .member-list-table .member-ids-cls').show();

	$(document).on('click','.change-password-lock',function(){
		$userId = $(this).data('userid');
		$("#changePasswordUserId").val($userId);
		$("#changeUserPwd").modal("show");
	});
	$(document).on('click','.close,.close-userChangePassword-popup',function(){
		$("#newPassword").val("");
		$("#confirmPassword").val("");
	});


	$(".dt-buttons").append(
		"<div class='event_product_assign_dropdown ml-1'><select class='form-control event_product_assign_select' name='event_product_assign_select'><option value='assign_event'>" +
			EVENT_LAN +
			"</option><option value='assign_product'>" +
			PRODUCT_LAN +
			"</option><option value='assign_award'>" +
			AWARD +
			"</option><option value='assign_badge'>" +
			BADGE +
			"</option><option value='assign_member_to_enrollment_product'>"+ASSIGN_MEMBERS_TO_PRODUCT_ORDER+"</option></select></div>"
	);
	$(".dt-buttons").append("<div class='badges_main_cls'></div>");

	// Get All Products
	$.ajax({
		type: "GET",
		url: BASE_URL + "/get-all-product",
		data: {},
		success: function (response) {
			$(".dt-buttons").append(response.html);
			$(".product_main_cls").hide();
		},
	});

	// Find All Award List
	$.ajax({
		type: "GET",
		//url: BASE_URL + '/get-all-awardlist',
		url: BASE_URL + "/getAwardCategoriesList",
		data: {},
		success: function (response) {
			var data = JSON.parse(JSON.stringify(response));
			$(".dt-buttons").append(data.html);
			$(".award_main_cls").hide();
		},
	});

	// List of all existing enrollment product order
	$.ajax({
		type: "GET",
		url: BASE_URL + "/product/enrollment-order-list",
		data: {},
		success: function (response) {
			var data = JSON.parse(JSON.stringify(response));
			$(".dt-buttons").append(data.html);
			$(".product_enrollment_order_main_cls").hide();
		},
	});

	$(document).on("change",".event_product_assign_select",function () {
			var value = this.value;
			if (value == "assign_event") {
				$('.product_enrollment_order_main_cls').hide();
				$(".event_main_clss").show();
				$(".product_main_cls").hide();
				$(".award_main_cls").hide();
				$(".badges_main_cls").hide();
			}
			if (value == "assign_product") {
				$('.product_enrollment_order_main_cls').hide();
				$(".event_main_clss").hide();
				$(".product_main_cls").show();
				$(".award_main_cls").hide();
				$(".badges_main_cls").hide();
			}
			// Awards
			if (value == "assign_award") {
				$('.product_enrollment_order_main_cls').hide();
				$(".event_main_clss").hide();
				$(".product_main_cls").hide();
				$(".award_main_cls").show();
				$(".badges_main_cls").hide();

			}
			// Badge
			if (value == "assign_badge") {
				$(".event_main_clss").hide();
				$(".product_main_cls").hide();
				$(".award_main_cls").hide();
				$('.product_enrollment_order_main_cls').hide();
				$html =
					'<div class="col-md-3">\
						<select class="form-control" id="badges_select_team_mentor" name="current_team_member">\
							<option value="">' +
					SELECT_MEMBER_TYPE +
					'</option>\
							<option value="mentor_team">' +
					MENTOR_TEAM +
					'</option>\
							<option value="not_mentor_team">' +
					NOT_MENTOR_TEAM +
					'</option>\
						</select>\
						<span class="error badges_select_team_mentor"></span>\
					</div>';
				$(".badges_main_cls").html($html);
				$(".badges_main_cls").show();
			}

			// assign_member_to_existing_enrollment_product
			if(value == "assign_member_to_enrollment_product"){
				$(".event_main_clss").hide();
				$(".product_main_cls").hide();
				$(".award_main_cls").hide();
				$(".badges_main_cls").hide();
				$('.product_enrollment_order_main_cls').show();
			}
		}
	);

	// Get badges categories based on select
	$(document).on(
		"change",
		"#badges_select_team_mentor",
		function () {
			if (this.value) {
				$.ajax({
					url: BASE_URL + "/getBadgesCategoriesList",
					type: "get",
					data: {
						mentor_type: this.value,
					},
					success: function (response) {
						var data = JSON.parse(
							JSON.stringify(response)
						);
						$(".badges_main_cls").html(data.html);
					},
				});
			} else {
				$html =
					'<div class="col-md-3">\
						<select class="form-control" id="badges_select_team_mentor" name="current_team_member">\
							<option value="">' +
					SELECT_MEMBER_TYPE +
					'</option>\
							<option value="mentor_team">' +
					MENTOR_TEAM +
					'</option>\
							<option value="not_mentor_team">' +
					NOT_MENTOR_TEAM +
					'</option>\
						</select>\
						<span class="error badges_select_team_mentor"></span>\
					</div>';
				$(".badges_main_cls").html($html);
				$(".badges_main_cls").show();
				$(".badges_select_team_mentor").text(
					PLEASE_SELECT_OPTION
				);
			}
		}
	);

	$(document).on("change", ".event_type_cls", function () {
		$("#search_event_type").val(this.value);
		$.ajax({
			url: BASE_URL + "/get_event_type",
			type: "POST",
			data: {
				_token: $('meta[name="csrf-token"]').attr(
					"content"
				),
				search_event_date:
					$("#search_event_date").val(),
				search_event_type: this.value,
			},
			success: function (response) {
				$("#cover-spin").hide();
				$(".event_name_cls").hide();
				$(".event_post_cls").hide();
				$(".events-id-cls").hide();
				$(".filter_event_cls").html(response);
			},
		});
	});

	$(document).on("change", ".event_name_select", function () {
		//$("#search_event_type").val(this.value);
		$.ajax({
			url: BASE_URL + "/get_event_post_type",
			type: "POST",
			data: {
				_token: $('meta[name="csrf-token"]').attr(
					"content"
				),
				search_event_date:
					$("#search_event_date").val(),
				search_event_type: this.value,
				event_code: $(this).val(),
				event_id: $("option:selected", this).attr(
					"data-id"
				),
			},
			success: function (response) {
				$("#cover-spin").hide();
				$(".event_post_cls").html("");
				$(".event_post_cls").html(response);
			},
		});
	});

	$(document).on("change", ".product_id", function () {
		$.ajax({
			url: BASE_URL + "/get_product_cost_type",
			type: "POST",
			data: {
				_token: $('meta[name="csrf-token"]').attr(
					"content"
				),
				product_id: $(this).val(),
			},
			success: function (response) {
				$("#cover-spin").hide();
				$(".product_cost_cls").html("");
				$(".product_cost_cls").html(response);
			},
		});
	});

	// Get the selected product using child product prefix & Suffix
	$(document).on("change", ".product_id", function () {
		$.ajax({
			url: BASE_URL + "/get_child_product_prefix_suffix",
			type: "POST",
			data: {
				_token: $('meta[name="csrf-token"]').attr(
					"content"
				),
				product_id: $(this).val(),
			},
			success: function (response) {
				$("#cover-spin").hide();
				$(".child_product_select_cls").html("");
				$(".child_product_select_cls").html(response);
			},
		});
	});

	$(document).on("change", ".events_post_cls", function () {
		$("#postTypeid").val($(this).val());
	});

	// Get All Events
	$.ajax({
		type: "GET",
		url: BASE_URL + "/get-all-event",
		data: {},
		success: function (response) {
			$(".dt-buttons").append(response.html);
			if ($(".filter_event").length) {
				$(".filter_event").daterangepicker({
					showDropdowns: true,
					minYear: 1950,
					maxYear: parseInt(moment().format("YYYY"), 10),
					ranges: {
						Today: [moment(), moment()],
						Yesterday: [
							moment().subtract(1, "days"),
							moment().subtract(1, "days"),
						],
						"Last 7 Days": [
							moment().subtract(6, "days"),
							moment(),
						],
						"Last 30 Days": [
							moment().subtract(29, "days"),
							moment(),
						],
						"This Month": [
							moment().startOf("month"),
							moment().endOf("month"),
						],
						"Last Month": [
							moment()
								.subtract(1, "month")
								.startOf("month"),
							moment()
								.subtract(1, "month")
								.endOf("month"),
						],
						"Last Year": [
							moment()
								.subtract(1, "year")
								.startOf("year"),
							moment().subtract(1, "year").endOf("year"),
						],
					},
					alwaysShowCalendars: true,
					autoUpdateInput: false,
				});
			}
			$(".filter_event").on(
				"apply.daterangepicker",
				function (ev, picker) {
					var eventDate = $(this).val(
						picker.startDate.format("DD/MM/YYYY") +
							" - " +
							picker.endDate.format("DD/MM/YYYY")
					);
					$("#search_event_date").val($(this).val());
					$.ajax({
						url: BASE_URL + "/get_event_type",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							search_event_date: $(this).val(),
							search_event_type:
								$("#search_event_type").val(),
						},
						success: function (response) {
							$("#cover-spin").hide();
							$(".event_name_cls").hide();
							$(".event_post_cls").hide();
							$(".events-id-cls").hide();
							$(".filter_event_cls").html(response);
						},
					});
				}
			);
		},
	});
	

	$(document).on("click", ".dt-checkboxes", function (){
		if($('.dt-checkboxes').length === $('.dt-checkboxes:checked').length){
			$(".dt-checkboxes-select-all").prop('checked',true);
		}else{
			$(".dt-checkboxes-select-all").prop('checked',false);
		}
		var userid = $(this).closest(".user-id-cls").attr("id");
		if ($(this).is(":checked")) {
			if (useridarr.indexOf(userid) !== -1) {
				// Current value is exists in array
			} else {
				useridarr.push(userid);
			}
		} else {
			useridarr = $.grep(useridarr, function (value) {
				return value != userid;
			});
		}
	});

	/**
	 * Select all member options
	 */
	$(document).on("change", ".dt-checkboxes-select-all", function () {
	 	useridarr = [];
		if (this.checked) {
			$(".dt-checkboxes").each(function () {
				$(this).prop("checked", true);
				useridarr.push($(this).val());
			});
		}else{
			$(".dt-checkboxes").each(function () {
				$(this).prop("checked", false);
			});
		}
	});

	/**
	 * Member Assign to Events or products
	 */
	$(document).on("click", ".assign-user-cls", function () {
		$(".error").html("");
		var eventid = $("#events_name").val();
		var productid = $("#product_id").val();
		var type = $(this).data("type");
		var postTypeId = $("#events_post_type").val();
		var postType = $("#events_post_type option:selected").attr(
			"data-id"
		);
		var remarks = $("#remarksEvent").val();
		if (useridarr != "") {
			// If selected is events
			if (type == "assign_event") {
				if (eventid != "") {
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/event-assign-user",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							eventid: eventid,
							user_id: useridarr,
							postType: postType,
							posttypeId: postTypeId,
							remarks: remarks,
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(
								JSON.stringify(response)
							);
							if (data.status) {
								toastr.success(data.message);
							} else {
								toastr.error(data.message);
							}
						},
					});
				} else {
					toastr.error("Please select event.");
				}
			}

			// If selected is Products
			if (type == "assign_product") {
				var costTypeId = $("#product_cost_type").val();
				var childProductIds = $("#child_product_select").val();
				if (productid != "" && costTypeId != "") {
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/product-assign-user",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							productid: productid,
							user_id: useridarr,
							costTypeId: costTypeId,
							remarks: remarks,
							childProductId: childProductIds,
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(
								JSON.stringify(response)
							);
							if (data.status) {
								toastr.success(data.message);
							} else {
								toastr.error(data.message);
							}
						},
					});
				} else {
					if (productid == "" && costTypeId == "") {
						toastr.error(
							VALIDATIONS.PLEASE_SELECT_PRODUCT_AND_COST_TYPE
						);
						return false;
					} else if (productid == "") {
						toastr.error(
							VALIDATIONS.PLEASE_SELECT_PRODUCT_
						);
					} else if (costTypeId == "") {
						toastr.error(
							VALIDATIONS.PLEASE_SELECT_PRODUCT_COST_TYPE
						);
					}
				}
			}

			// Assign Award
			if (type == "assign_award") {
				var awardId = $("#award_id").val();
				var reference_number = $("#reference_number").val();
				var issue_date = $("#issue_date").val();
				if (awardId == "") {
					$(".award_select_error").text(
						AWARD_ASSIGN_VALIDATIONS.PLEASE_SELECT_AWARD_CATEGORY
					);
				}
				if (reference_number == "") {
					$(".reference_number_error").text(
						AWARD_ASSIGN_VALIDATIONS.PLEASE_ENTER_REFERENCE_NUMBER
					);
				}
				if (issue_date == "") {
					$(".issue_date_error").text(
						AWARD_ASSIGN_VALIDATIONS.PLEASE_SELECT_ISSUE_DATE
					);
				}
				if (awardId && reference_number && issue_date) {
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/award-assign-user",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							award_id: awardId,
							user_id: useridarr,
							reference_number: reference_number,
							issue_date: issue_date,
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(
								JSON.stringify(response)
							);
							if (data.status) {
								toastr.success(data.message);
							} else {
								toastr.error(data.message);
							}
						},
					});
				}
			}

			// Badge Assigne to user
			if (type == "assign_badge") {
				var badgeId = $("#badge_id").val();
				var reference_number = $("#reference_number").val();
				var issue_date = $("#issue_date").val();
				if (badgeId == "") {
					$(".badges_select_error").text(
						BADGES_ASSIGN_VALIDATIONS.PLEASE_SELECT_BADGES_CATEGORY
					);
				}
				if (reference_number == "") {
					$(".reference_number_error").text(
						BADGES_ASSIGN_VALIDATIONS.PLEASE_ENTER_REFERENCE_NUMBER
					);
				}
				if (issue_date == "") {
					$(".issue_date_error").text(
						BADGES_ASSIGN_VALIDATIONS.PLEASE_SELECT_ISSUE_DATE
					);
				}
				if (badgeId && reference_number && issue_date) {
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/badge-assign-user",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							badge_id: badgeId,
							user_id: useridarr,
							reference_number: reference_number,
							issue_date: issue_date,
							member_type: $(
								"#badges_select_team_mentor"
							).val(),
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(
								JSON.stringify(response)
							);
							if (data.status) {
								toastr.success(data.message);
							} else {
								toastr.error(data.message);
							}
						},
					});
				}
			}

			// If assigned selected product enrollment order to members
			if (type == "assign_enrollment_product_order") {
				var EnrollmentProductId = $("#enrollment_product_id").val();
				if (EnrollmentProductId != "") {
					$("#cover-spin").show();
					$.ajax({
						url: BASE_URL + "/product/enrollment-order/assigned-member",
						type: "POST",
						data: {
							_token: $('meta[name="csrf-token"]').attr(
								"content"
							),
							EnrollmentProductId: EnrollmentProductId,
							user_id: useridarr
						},
						success: function (response) {
							$("#cover-spin").hide();
							var data = JSON.parse(
								JSON.stringify(response)
							);
							if (data.status) {
								toastr.success(data.message);
							} else {
								toastr.error(data.message);
							}
						},
					});
				} else {
					toastr.error(VALIDATIONS.PLEASE_SELECT_PRODUCT_ORDER);
				}
			}
		} else {
			toastr.error(PLEASE_SELECT_MEMBER);
		}
	});

	/**
	 * USE : Update Selected Multiple User status
	 */
	$(document).on("change", "#multiple_status_member", function () {
		if (this.value == "") {
			toastr.error("Please select status");
			return false;
		}
		if (useridarr != "") {
			$("#cover-spin").show();
			$.ajax({
				type: "POST",
				url: BASE_URL + "/multiple-user-update-status",
				data: {
					_token: $('meta[name="csrf-token"]').attr(
						"content"
					),
					userIds: useridarr,
					status: this.value,
				},
				success: function (response) {
					$("#cover-spin").hide();
					var object = JSON.parse(JSON.stringify(response));
					if (object.status) {
						toastr.success(object.message);
						location.reload();
					} else {
						toastr.error(object.message);
					}
				},
			});
		} else {
			toastr.error("Please select member");
		}
	});

	/**
	 * USE : Export qr code url into csv file
	 */
	$(document).on("click", ".export-qrcodes-btn", function () {
		if (useridarr != "") {
			$("#cover-spin").show();
			$.ajax({
				type: "POST",
				url: BASE_URL + "/export/member-qrcodes-url",
				data: {
					_token: $('meta[name="csrf-token"]').attr(
						"content"
					),
					userIds: useridarr,
				},
				success: function (data) {
					$("#cover-spin").hide();
					var isHTML =
						RegExp.prototype.test.bind(/(<([^>]+)>)/i);
					if (!isHTML(data)) {
						var downloadLink = document.createElement("a");
						var fileData = ["\ufeff" + data];
						var blobObject = new Blob(fileData, {
							type: "text/csv;charset=utf-8;",
						});
						var url = URL.createObjectURL(blobObject);
						downloadLink.href = url;
						downloadLink.download = "MemberQRCode.csv";
						document.body.appendChild(downloadLink);
						downloadLink.click();
						document.body.removeChild(downloadLink);
					}
				},
			});
		} else {
			toastr.error("Please select member");
		}
	});

    /**
     * USE : Select one by one display column
     */
    var columns_table = [
                5, 6, 7, 9, 10, 13, 16, 17, 19, 20, 21, 22, 23, 24, 25, 26, 27,
                28, 32, 33, 34, 36, 37, 38, 40, 41, 42, 43,
            ];
	$(document).on("click", ".filter-serach-cls", function () {
        if ($(this).is(":checked")) {
            $('.member-list-table th:nth-child('+(parseInt(this.value)+1)+')').show();
            $('.member-list-table td:nth-child('+this.value+')').show();
        }else{
            $('.member-list-table th:nth-child('+(parseInt(this.value)+1)+')').hide();
            $('.member-list-table td:nth-child('+this.value+')').hide();
        }
		var checkedAry = [];
		$.each($(".filter-serach-cls:checked"), function () {
			checkedAry.push(this.value);
		});
		$('input[name="export_filter"]').val(checkedAry);
		var dataColumn = $.session.set("dataColumn", checkedAry);
	});

    /**
     * USE : Select All display column
     */
    $(document).on("click", ".filter-serach-cls-all", function () {
        if($(this).is(":checked")){
            if (this.value == "all") {
                $(".filter-serach-cls-all").prop("checked", true);
                $.session.set("dataColumnall", "all");
                $('input[name="export_filter"]').val("");
                $("input[name='customfilter[]']").each(function () {
                    $(this).prop("checked", true);
                    var val = this.value;
                    var old_val = $('input[name="export_filter"]').val();
                    if(val != "all"){
                        if(old_val == ""){
                            $('input[name="export_filter"]').val(val);
                        }else{
                            $('input[name="export_filter"]').val(old_val + "," + val);
                        }
                        $('.member-list-table th:nth-child('+(parseInt(val)+1)+')').show();
                        $('.member-list-table td:nth-child('+val+')').show();
                    }
                });
            }
        }else{
            $.session.remove("dataColumnall");
            $.session.remove("dataColumn");
            $(".filter-serach-cls").prop("checked", false);
            for(i = 2; i <= 14; i++){
                if(i <= 14){
                    $(".filter-serach-cls[value=" + i + "]").prop("checked",true);
                }else{
                    $(".filter-serach-cls[value=" + i + "]").prop("checked",false);
                }
            }
            $('input[name="export_filter"]').val("2,3,4,8,11,12,14,15,18,30,31,35,39");
            $.each(columns_table, function (key, val) {
                $('.member-list-table th:nth-child('+(parseInt(val)+1)+')').hide();
                $('.member-list-table td:nth-child('+val+')').hide();
            });
        }

    });

    /**
     * USE : Clear Sorting Columns
     */
    $(".clearsorting").click(function () {
        $.session.remove("dataColumnall");
        $.session.remove("dataColumn");
        $(".filter-serach-cls-all").prop("checked", false);
        $(".filter-serach-cls").prop("checked", false);
        var DefaultCol = [2, 3, 4, 8, 11, 12, 14, 15, 18, 30, 31, 35, 39,];
        for (i = 2; i <= 43; i++) {
            if (jQuery.inArray(i, DefaultCol) !== -1) {
                $(".filter-serach-cls[value="+i+"]").prop("checked",true);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').show();
                $('.member-list-table td:nth-child('+i+')').show();
            } else {
                $(".filter-serach-cls[value="+i+"]").prop("checked",false);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').hide();
                $('.member-list-table td:nth-child('+i+')').hide();
            }
        }        
        $('input[name="export_filter"]').val("2,3,4,8,11,12,14,15,18,30,31,35,39");
    });

    /**
     * Check On page load column array list and display based on selected columns into list page
     */
    if ($.session.get("dataColumn") != undefined) {
        var sessionValue = $.session.get("dataColumn");
        $('input[name="export_filter"]').val(sessionValue);
        var Columnspilt = sessionValue.split(",");
        $.each(Columnspilt, function (index, value) {
            $(".filter-serach-cls[value="+value+"]").prop("checked",true);
            HideShowColumn(value,Columnspilt);
        });
        
        for(i = 2; i <= 43; i++){
            var ColumnSplitArray = Columnspilt.map(function (x) { 
                return parseInt(x, 10);
            });
            if (jQuery.inArray(i, ColumnSplitArray) !== -1) {
                $(".filter-serach-cls[value="+i+"]").prop("checked",true);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').show();
                $('.member-list-table td:nth-child('+i+')').show();
            } else {
                $(".filter-serach-cls[value="+i+"]").prop("checked",false);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').hide();
                $('.member-list-table td:nth-child('+i+')').hide();
            }
        }
    } else {
        var checkedAry = [];
        $.each($(".filter-serach-cls:checked"), function () {
            checkedAry.push(this.value);
        });
        var dataColumn = $.session.set("dataColumn", checkedAry);
        var sessionValue = $.session.get("dataColumn");
        $('input[name="export_filter"]').val(sessionValue);
        var Columnspilt = sessionValue.split(",");
        $.each(Columnspilt, function (index, value) {
            $(".filter-serach-cls[value="+value+"]").prop("checked",true);
            HideShowColumn(value,Columnspilt);
        });
        for (i = 2; i <= 43; i++) {
            var ColumnSplitArray = Columnspilt.map(function (x) { 
                return parseInt(x, 10);
            });
            if (jQuery.inArray(i, ColumnSplitArray) !== -1) {
                $(".filter-serach-cls[value="+i+"]").prop("checked",true);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').show();
                $('.member-list-table td:nth-child('+i+')').show();
            } else {
                $(".filter-serach-cls[value="+i+"]").prop("checked",false);
                $('.member-list-table th:nth-child('+(parseInt(i)+1)+')').hide();
                $('.member-list-table td:nth-child('+i+')').hide();
            }
        }
    }

	$(document).on("change", ".product_id", function () {
		$.ajax({
			url: BASE_URL + "/get_product_cost_type",
			type: "POST",
			data: {
				_token: $('meta[name="csrf-token"]').attr(
					"content"
				),
				product_id: $(this).val(),
			},
			success: function (response) {
				$("#cover-spin").hide();
				$(".product_cost_cls").html("");
				$(".product_cost_cls").html(response);
				//$("#child_product_select").multiselect();
				$("#child_product_select").multiselect('rebuild');
			},
		});
	});

	/** USE : On change products */
	$(document).on("change", "#child_product_select", function () {
		var SelectedOption = $('#child_product_select').val();
		//var MainProductId = ;
		var MainProductId = [];
		$('#child_product_select  option').each(function() {
			if($(this).is(':selected')){
				MainProductId.push($(this).attr('mainproductid'));
			}
		});
		
		if(MainProductId==undefined){
			$('#child_product_select  option').each(function() {
				if(SelectedOption.length !== 0){
					if(SelectedOption.includes($(this).val())){
						$(this).attr('disabled', false);
					}else{
						$(this).attr('disabled', true);
					}
				}else{
					$(this).attr('disabled', false);
				}
			});
		}else{
			$('#child_product_select  option').each(function() {
				if(MainProductId.includes($(this).attr('mainproductid'))){
					if(SelectedOption.includes($(this).val())){
					}else{
						$(this).attr('disabled', true);
					}
				}else{
					$(this).attr('disabled', false);
				}
			});
		}
		//$("#child_product_select").multiselect('destroy');
		$("#child_product_select").multiselect('rebuild');
	});
});

/**
 * Export member details CSV
 */
function exportCSV() {
	$('input[name="export_filter"]').val('2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43');
	var val = $('input[name="export_filter"]').val();
	$.ajax({
		type: "POST",
		url: BASE_URL + "/exportCSV",
		data: {
			_token: $('meta[name="csrf-token"]').attr("content"),
			val: val,
			formData: $("#filterDateForm").serialize(),
			selectedUserIds: useridarr,
		},
		success: function (data) {
			var isHTML = RegExp.prototype.test.bind(/(<([^>]+)>)/i);
			if (!isHTML(data)) {
				var downloadLink = document.createElement("a");
				var fileData = ["\ufeff" + data];

				var blobObject = new Blob(fileData, {
					type: "text/csv;charset=utf-8;",
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

/**
 * USE : Hide show column based on selected filter columns
 */
function HideShowColumn(ColumnNumber, ColumnArray){
    if (jQuery.inArray(ColumnNumber, ColumnArray) !== -1) {
        $(".filter-serach-cls[value="+ColumnNumber+"]").prop("checked", true);
        $('.member-list-table th:nth-child('+(parseInt(ColumnNumber)+1)+')').show();
        $('.member-list-table td:nth-child('+ColumnNumber+')').show();
    } else {        
        $(".filter-serach-cls[value="+ColumnNumber+"]").prop("checked", false);
        $('.member-list-table th:nth-child('+(parseInt(ColumnNumber)+1)+')').hide();
        $('.member-list-table td:nth-child('+ColumnNumber+')').hide();
    }   
}
</script>
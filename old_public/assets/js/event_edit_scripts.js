$( document ).ready(function() {

	//Event add dates form submit
	$('.edit_new_event_dates').on('click', function(e) {
		if (!$('#eventform_edit').valid()) {

		}else{
			
			var dates = $('input[name="event_dates"]').val();
			if(dates != ''){
				var formData = $("#eventform_edit").serialize();
				var form=$("#eventform_edit");
				$.ajax({
					type:"POST",
					url:BASE_URL+"/editNewEventDates",
					data:{ 
						'_token': $('#csrf-tokens').val(),
						'formData' :formData
					},
					success: function(response){

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
						$('#calendar').fullCalendar( 'refetchEvents' );
						
						$('input[name="event_dates"]').val('');
						$( "#event_edit_dates_cal" ).datepicker("destroy");
						$('#event_edit_dates_cal').datepicker({
							defaultDate: null,
							startDate: new Date(),
							multidate: true,
							format: "dd/mm/yyyy",
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

	$('#edit_event_status').on('change', function() {
		if(this.value == '1'){
			$('.event_start_end_dropdown').hide();
			$('.event_date_main_cls').hide();
			$('.edit_new_event_dates').hide();
		}else{
			$('.event_start_end_dropdown').show();
			$('.event_date_main_cls').show();
			$('.edit_new_event_dates').show();
		}

	});

});
<html>
	<body width="100%" style='-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; height: 100%; width: 100%; font-family: "Helvetica Neue", sans-serif; margin: 0 auto; padding: 0;' bgcolor="#f0f0f0">
		<table class="email-canvas  " cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; margin: 0 auto;" bgcolor="#f0f0f0">
			<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
				<td valign="top" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
					<center style="width: 100%; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
						<div class="email-wrapper" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; max-width: 600px; margin: auto;">
							<table class="email-body" cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; max-width: 600px; margin: 0 auto;" bgcolor="#ffffff">
								<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
									<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
										<table border="0" cellpadding="30" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
											<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
												<td valign="top" class="email-body-content" style='-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-family: "Helvetica Neue", sans-serif; color: #444; font-size: 14px; line-height: 150%;'>
													<div id="greeting" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin-bottom: 20px; padding-bottom: 25px; border-bottom-width: 1px; border-bottom-color: #eee; border-bottom-style: solid;">
														<table border="0" cellpadding="0" cellspacing="0" width="100%" style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0; border-collapse: collapse; margin: 0 auto;">
															<tr style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
																<td style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
																	<h1 class="greeting" style='-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-family: "Helvetica Neue", sans-serif; color: #444; display: block; font-size: 18px; font-weight: 500; line-height: 1.3; margin: 0;'>
																		Hi, {{ $data['name'] }},
																	</h1>
																</td>
															</tr>
														</table>
													</div>
													<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin-top: 15px; margin-bottom: 15px; font-size: 16px; line-height: 1.5; color: #555;">You Invited for this event <b>{{ $data['event_name'] }}</b> and event type is <b>{{ $data['event_type'] }}</b>.This event start date is <b>{{ date('l,dS F,Y',strtotime($data['start_date'])) }}</b> 
													@if(!empty($end_date))
														and end date is <b>{{ date('l,dS F,Y',strtotime($data['end_date'])) }}</b>.
													@endif
													</p>
													<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin-top: 15px; margin-bottom: 15px; font-size: 16px; line-height: 1.5; color: #555;">This event start time is <b>{{ date('h:i a',strtotime($data['start_time'])) }}</b> and end time is <b>{{ date('h:i a',strtotime($data['end_time'])) }}</b>.</p>
													<p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin-top: 15px; margin-bottom: 15px; font-size: 16px; line-height: 1.5; color: #555;">Thank you,<br style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
														Team Scout
													</p>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</div>
					</center>
				</td>
			</tr>
		</table>
	</body>
</html>
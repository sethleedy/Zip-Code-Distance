<?php
	

?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Seth Leedy's Distance Calculator</title>

		<!-- Bootstrap -->
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<!--		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<!-- Include all compiled plugins (below), or include individual files as needed -->
		<script src="js/bootstrap.min.js"></script>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
		<script>
			$(function() {

				$("#zipSubmitBtn").click(function() {

					//alert($('#frmZipCode').serialize());

					$.ajax({
						method: "POST",
						async: true,
						url: "processing.php",
						data: $('#frmZipCode').serialize(),
						dataType: 'json',
						success: function(returnedData, textStatus) {
							// Fill variables
							//var returnedDataJSON = JSON.parse(returnedData); // Parse the returned data into an Object for use. // ERROR: Already an Object. - Token o error.
							var returnedDataJSON = returnedData;

							// Fill a span with the zip code used in the Form.
							var dataFillZipCodeTargetValue = returnedDataJSON.targetZipCode;
							$("#dataFillZipCodeTarget").text(dataFillZipCodeTargetValue);

							// Test if we have a good response or an error in submission
							if (textStatus == "success") {
								
								// Check for JSON returned Error messages
								if (returnedDataJSON.error == true) {
									$("#submissionInfoErr").html(returnedDataJSON.errorMessage);									
								} else {
									// Setup HTML
									rowCode =  "<table class='table'>";
									console.log(returnedData);
					
									$.each(returnedData["addresses"], function(key1, value1) {
										$.each(value1, function(key2, value2) {
											// Loop only on the Numeric. Array contains Numeric and Associative indices.
											if ($.isNumeric(key2)) {
												rowCode += "<tr>";
												rowCode += "<td class=''>" + value2 + "</td>";
												rowCode += "</tr>";
												
											}
										});
										
										// Space the results
										rowCode += "<tr><td><hr style='border-color:black;'></td></tr>";
									});
									
									
									rowCode +=  "</table>";

									$("#submissionInfoErr").html(""); // Clear errors
									$("#divResults").html(rowCode); // Add results to page for display
									$("#zipInterface").collapse();
									$("#zipTargetResults").css("display", "none").removeClass("hidden").fadeIn(900);
								}
								
								
								
							}

							if (textStatus == "parsererror") {
								$("#submissionInfoErr").html("Please try submitting again. Error in processing.");
								
							}

						},
						error: function(jqXHR, exception) {
							if (jqXHR.status === 0) {
								alert('Not connect.\n Verify Network.');
							} else if (jqXHR.status == 404) {
								alert('Requested page not found. [404]');
							} else if (jqXHR.status == 500) {
								alert('Internal Server Error [500].');
							} else if (exception === 'parsererror') {
								alert('Requested JSON parse failed.');
							} else if (exception === 'timeout') {
								alert('Time out error.');
							} else if (exception === 'abort') {
								alert('Ajax request aborted.');
							} else {
								alert('Uncaught Error.\n' + jqXHR.responseText);
							}
						},
						complete: function(jqXHR, textStatus) { // ("success", "notmodified", "nocontent", "error", "timeout", "abort", or "parsererror")

						}
					});
					return false; // Stop from navigating to another page.
				});
			});

		</script>
	</head>

	<body>
		<div>
			<div id="header">
				<div class="container">
					<div class="jumbotron">
						<h1>Seth Leedy's Distance Calculator</h1>
						<p>Getting you from home to your destination.</p>
					</div>
				</div>

			</div>



			<div class="container fade in">
				<div class="col-sm-6 col-sm-offset-3 col-xs-12">

					<div class="panel-group">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<!--								<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
								<button type="button" class="btn btn-default" aria-label="Search Zip Code" data-toggle="collapse" data-target="#zipInterface">
									<span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
								</button>
							</div>
							<div id="zipInterface" class="panel-collapse collapse">
								<div class="panel-body">
									<form id="frmZipCode" name="frmZipCode" class="form-inline">
										<div class="form-group col-sm-9 col-xs-12 col-md-8">
											<div id="submissionInfoErr" class="alert-danger">
											</div>
											<input type="text" class="form-control" name="zipCodeTarget" id="zipCodeTarget" placeholder="44718">

										</div>
										<button id="zipSubmitBtn" name="zipSubmitBtn" type="submit" class="btn btn-default col-sm-3 col-xs-12 col-md-4">Submit</button>
									</form>
								</div>
								<div class="panel-footer">Search for a zip code.</div>
							</div>
						</div>
					</div>



				</div>
			</div>


			<div id="zipTargetResults" class="container hidden">
				<div class="col-sm-6 col-sm-offset-3 col-xs-12">
					

					<div class="panel panel-primary">
						<div class="panel-heading">
							Nearest stores to <span id="dataFillZipCodeTarget"></span>
						</div>
						<div id="divResults" class="panel-body">



						</div>
					</div>
				</div>
			</div>



		</div>

	</body>

	</html>

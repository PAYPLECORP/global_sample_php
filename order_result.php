<?php
include $_SERVER['DOCUMENT_ROOT'] . '/payple/inc/config.php';

// 해외카드 결제결과 파라미터 
$type = (isset($_POST['type'])) ? $_POST['type'] : "";
$result = (isset($_POST['result'])) ? $_POST['result'] : "";
$message = (isset($_POST['message'])) ? $_POST['message'] : "";
$resultUrl = (isset($_POST['resultUrl'])) ? $_POST['resultUrl'] : "";
$api_id = (isset($_POST['api_id'])) ? $_POST['api_id'] : "";
$api_date = (isset($_POST['api_date'])) ? $_POST['api_date'] : "";
$service_oid = (isset($_POST['service_oid'])) ? $_POST['service_oid'] : "";
$comments = (isset($_POST['comments'])) ? $_POST['comments'] : "";
$pay_type = (isset($_POST['pay_type'])) ? $_POST['pay_type'] : "";
$card_number = (isset($_POST['card_number'])) ? $_POST['card_number'] : "";
$totalAmount = (isset($_POST['totalAmount'])) ? $_POST['totalAmount'] : "";
$currency = (isset($_POST['currency'])) ? $_POST['currency'] : "";
$firstName = (isset($_POST['firstName'])) ? $_POST['firstName'] : "";
$lastName = (isset($_POST['lastName'])) ? $_POST['lastName'] : "";
$email = (isset($_POST['email'])) ? $_POST['email'] : "";
$phoneNumber = (isset($_POST['phoneNumber'])) ? $_POST['phoneNumber'] : "";
$billing_key = (isset($_POST['billing_key'])) ? $_POST['billing_key'] : "";
$submitTimeUtc = (isset($_POST['submitTimeUtc'])) ? $_POST['submitTimeUtc'] : "";

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<title>Payple Global Payment</title>
	<link rel="icon" href="/common/images/favicon.ico">
	<link rel="stylesheet" href="/common/stylesheets/style.css" />
	<!-- mobile setting -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
	<meta name="theme-color" content="#7852e8" />
	<meta name="msapplication-navbutton-color" content="#7852e8" />
	<meta name="apple-mobile-web-app-status-bar-style" content="#7852e8" />
	<!-- mobile setting end-->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<script type="text/javascript">
	$(document).ready(function() {

		const result = '<?= $result ?>';
		const api_id = '<?= $api_id ?>';
		const service_oid = '<?= $service_oid ?>';
		const comments = '<?= $comments ?>';
		const totalAmount = '<?= $totalAmount ?>';
		const currency = '<?= $currency ?>';
		const resultUrl = '<?= $resultUrl ?>';

		// 결제 성공
		if (result === 'A0000') {
			$('#payConfirmCancel').css('display', 'inline');
		}

		const payCancelAction = function gpayCancelConfirmAction () {
			const con = "승인취소요청을 전송합니다. \n 진행하시겠습니까? ";
			if (confirm(con) == true) {
				// 버튼 중복클릭 방지
				$('#payConfirmCancel').unbind('click');

				let formData = new FormData();
				formData.append('comments', comments);
				formData.append('service_oid', service_oid);
				formData.append('pay_id', api_id);
				formData.append('totalAmount', totalAmount);
				formData.append('currency', currency);
				formData.append('resultUrl', resultUrl);
          
				$.ajax({
					type: 'POST',
					cache: false,
					processData: false,
					contentType: false,
					async: false,
					url: '/paypleApi/cancel.php',
					dataType: 'json',
					data: formData,
					success: function(res) {
						console.log(res);

						if (res.result === 'A0000') {
							alert(res.message);
							$('#payConfirmCancel').css('display', 'none');

						} else {
							// 결제취소 실패시, 취소버튼 클릭 가능하게
							$('#payConfirmCancel').bind('click', function() {
								payCancelAction();
							});
							if (res.message) {
								alert(res.message)
							} else {
								alert('승인취소 요청 실패');
							}
						}
						let table_data = "";

						$.each(res, function (key, value) {
								table_data += '<tr><td>'+key+'</td><td> '+value+'</td><tr>';
						});

						$('#payRefundResult').append(table_data);
					},
					error: function(err) {
						console.log(err);
						// 결제취소 실패시, 취소버튼 클릭 가능하게
						$('#payConfirmCancel').bind('click', function() {
							payCancelAction();
						});
					}
				});
			}
		}

		$('#payConfirmCancel').on('click', function() {
			payCancelAction();
		});
	});

</script>

<body>
	<div class="device__layout w-600" id="responseBody">
		<div class="line_setter">
			<h4 class="tit__device mb-32">
				<img class="logo_in_text__md" src="/common/images/logo_full.svg" alt="" />
				<b>해외결제 API - 결제결과</b>
			</h4>
			<br /><br />
			<div id="payResTable">
				<b>Response (일반결제 결과)</b><br /><br />
				<div class="table-outter" id="payResult">
					<table class="model-01">
						<colgroup>
							<col style="width:50%;">
							<col style="width:50%;">
						</colgroup>
						<tr>
							<th>파라미터 항목</th>
							<th>파라미터 값</th>
						</tr>
						<tr>
							<td>type</td>
							<td><?= $type ?></td>
						</tr>
						<tr>
							<td>result</td>
							<td><?= $result ?></td>
						</tr>
						<tr>
							<td>message</td>
							<td><?= $message ?></td>
						</tr>
						<tr>
							<td>resultUrl</td>
							<td><?= $resultUrl ?></td>
						</tr>
						<tr>
							<td>api_id</td>
							<td><?= $api_id ?></td>
						</tr>
						<tr>
							<td>api_date</td>
							<td><?= $api_date ?></td>
						</tr>
						<tr>
							<td>service_oid</td>
							<td><?= $service_oid ?></td>
						</tr>
						<tr>
							<td>comments</td>
							<td><?= $comments ?></td>
						</tr>
						<tr>
							<td>pay_type</td>
							<td><?= $pay_type ?></td>
						</tr>
						<tr>
							<td>card_number</td>
							<td><?= $card_number ?></td>
						</tr>
						<tr>
							<td>totalAmount</td>
							<td><?= $totalAmount ?></td>
						</tr>
						<tr>
							<td>currency</td>
							<td><?= $currency ?></td>
						</tr>
						<tr>
							<td>firstName</td>
							<td><?= $firstName ?></td>
						</tr>
						<tr>
							<td>lastName</td>
							<td><?= $lastName ?></td>
						</tr>
						<tr>
							<td>email</td>
							<td><?= $email ?></td>
						</tr>
						<tr>
							<td>phoneNumber</td>
							<td><?= $phoneNumber ?></td>
						</tr>
						<tr>
							<td>billing_key</td>
							<td><?= $billing_key ?></td>
						</tr>
						<tr>
							<td>submitTimeUtc</td>
							<td><?= $submitTimeUtc ?></td>
						</tr>
					</table>
				</div>
				<div class="btn_box has_space align_center">
					<button class="btn cl_main btn_rounded btn_md" type="button" id="payConfirmCancel" style="display:none">
						결제승인취소
					</button>
				</div>
			</div>
			<b>Response (취소 결과)</b><br /><br />
			<div class="table-outter">
				<table class="model-01" id="payRefundResult">
					<colgroup>
						<col style="width:50%;">
						<col style="width:50%;">
					</colgroup>
					<tr>
						<th>파라미터 항목</th>
						<th>파라미터 값</th>
					</tr>
				</table>
			</div>
		</div>
	</div>
</body>

</html>
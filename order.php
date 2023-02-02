<?php
// 해외카드 결제요청 파라미터
$service_oid = preg_replace("/([^0-9a-zA-Z]+)/", "", "PaypleGpayTest" . microtime());
$comments = "Payple global payments";
$totalAmount = "1.00";
$firstName = "Payple";
$lastName = "Inc";
$currency = "USD";
$email = "test@payple.kr";

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

<script>
	$(document).ready(function() {
		const orderFormSubmit = function gpayOrderFormSubmit() {
			// 버튼 중복클릭 방지
			$('#orderSubmit').unbind('click');

			var fm = $("#orderForm")[0];
			var action_url = "";

			const $isBillingPay = $('#isBillingPay').val();
			const $billing_key = $('#billing_key').val();

			// 빌링키 결제(REST API) 요청
			if ($isBillingPay === 'Y' && $billing_key) {
				action_url = "/order_billingKey.php";
			} else {
				// 해외결제창 요청
				action_url = "/order_confirm.php";
			}
			fm.method = "POST";
			fm.action = action_url;
			fm.submit();
			event.preventDefault();
		}

		$('#orderSubmit').on('click', function(event) {
			orderFormSubmit();
		});

		// 뒤로가기 이벤트 발생하면 버튼 Click Bind
		window.onpageshow = function(event) {
			if (event.persisted || (window.performance && window.performance.navigation.type == 2)) {
				$('#orderSubmit').bind('click', function() {
					orderFormSubmit();
				});
			}
		}

		$('#isBillingPay').change(function() {
			const $this_val = $(this).val();
			if ($this_val == 'Y') {
				$('#inputBillingKey').css('display', 'block');
				$('#inputCVC').css('display', 'block');
				$('#selectIsDirect').css('display', 'none');
			} else {
				$('#inputBillingKey').css('display', 'none');
				$('#inputCVC').css('display', 'none');
				$('#selectIsDirect').css('display', 'block');
			}
		});
	});
</script>

<body>
	<div class="device__layout w-600">
		<div class="line_setter">
			<form id="orderForm" name="orderForm" method="post" action="/order_confirm.php">
				<h4 class="tit__device">
					<img class="logo_in_text__md" src="/common/images/logo_full.svg" alt="" />
					<b>해외결제 API</b>
				</h4>
				<!-- 결제창 관련 파라미터 -->
				<div class="tit--by-page">
					<h3 class="tit_component">결제창 설정</h3>
					<div class="icon">
						<img src="/common/images/icon--arrow-up.svg" alt="" class="res" />
					</div>
				</div>
				<div class="ctn--by-page">
					<div class="form_box has_border w240 form-box-index">
						<div class="tit__form_box">항목</div>
						<div class="tit__form_box">요청변수</div>
						<div class="ctn__form_box fsz_10">값</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">빌링키 결제</div>
						<div class="tit__form_box fsz_08">
							<div>isBillingPay</div>
						</div>
						<div class="ctn__form_box">
							<div class="select">
								<select id="isBillingPay" name="isBillingPay">
									<option value="" selected>일반결제</option>
									<option value="Y">간편 빌링키 결제 (결제자 정보 입력안함)</option>
								</select>
							</div>
						</div>
					</div>
					<div class="form_box has_border w240" id="inputBillingKey" style="display: none;">
						<div class="tit__form_box fcl_txt fw_bd">빌링키</div>
						<div class="tit__form_box fsz_08">billing_key</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" id="billing_key" name="billing_key" value="" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240" id="inputCVC" style="display: none;">
						<div class="tit__form_box fcl_txt fw_bd">CVC/CVV</div>
						<div class="tit__form_box fsz_08">securityCode</div>
							<div class="ctn__form_box">
								<div class="input">
									<input class="ipt" type="text" id="securityCode" name="securityCode" value="" maxlength="3" />
								</div>
							</div>
					</div>
					<div class="form_box has_border w240" id="selectIsDirect" style="display: block;">
						<div class="tit__form_box fcl_txt fw_bd">결제창 호출 방식</div>
						<div class="tit__form_box fsz_08">
							<div>isDirect</div>
						</div>
						<div class="ctn__form_box">
							<div class="select">
								<select name="isDirect">
									<option value="N" selected>팝업</option>
									<option value="Y">다이렉트</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="tit--by-page">
					<h3 class="tit_component">결제정보 설정</h3>
					<div class="icon">
						<img src="/common/images/icon--arrow-up.svg" alt="" class="res" />
					</div>
				</div>
				<div class="ctn--by-page">
					<div class="form_box has_border w240 form-box-index">
						<div class="tit__form_box">항목</div>
						<div class="tit__form_box">요청변수</div>
						<div class="ctn__form_box fsz_10">값</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">주문번호</div>
						<div class="tit__form_box fsz_08">service_oid</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" name="service_oid" value="<?= $service_oid ?>" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">상품명</div>
						<div class="tit__form_box fsz_08">comments</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" name="comments" value="<?= $comments ?>" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">결제금액</div>
						<div class="tit__form_box fsz_08">totalAmount</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" id="totalAmount" name="totalAmount" value="<?= $totalAmount ?>" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">결제통화</div>
						<div class="tit__form_box fsz_08">currency</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" id="currency" name="currency" value="<?= $currency ?>" readonly />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">결제고객 성</div>
						<div class="tit__form_box fsz_08">lastName</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" name="lastName" value="<?= $lastName ?>" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">결제고객 이름</div>
						<div class="tit__form_box fsz_08">firstName</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" name="firstName" value="<?= $firstName ?>" />
							</div>
						</div>
					</div>
					<div class="form_box has_border w240">
						<div class="tit__form_box fcl_txt fw_bd">결제고객 이메일</div>
						<div class="tit__form_box fsz_08">email</div>
						<div class="ctn__form_box">
							<div class="input">
								<input class="ipt" type="text" name="email" value="<?= $email ?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="btn_box has_space align_center">
					<button class="btn cl_main btn_rounded btn_md" type="button" id="orderSubmit">다음단계</button>
				</div>
			</form>
		</div>
	</div>
</body>

</html>
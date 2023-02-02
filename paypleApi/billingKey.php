<?php
include $_SERVER['DOCUMENT_ROOT'] . '/payple/inc/config.php';
header("Expires: Mon 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d, M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0; pre-check=0", false);
header("Pragma: no-cache");
header("Content-type: application/json; charset=utf-8");

try {
  /**
   * 토큰 인증 Request
   */
	// 발급받은 비밀키. 유출에 주의하시기 바랍니다.
	// 실제 서비스(REAL)에 붙이실 때는 발급받은 운영 계정 키를 넣어주세요.
	$post_data = array(
		"service_id" => $service_id,
		"service_key" => $service_key,
		"code" => "as12345678"
	);

	$ch = curl_init($tokenUrl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));

	ob_start();
	$authRes = curl_exec($ch);
	$authBuffer = ob_get_contents();
	ob_end_clean();

	// Converting To Object
	$authResult = json_decode($authBuffer);

	if (!isset($authResult->result)) throw new Exception("파트너 인증요청 실패");

	if ($authResult->result != 'T0000') throw new Exception($authResult->result_msg);

	$access_token = $authResult->access_token;     // 인증 토큰

  /**
   * 빌링키 결제 요청
   */
  $service_oid = (isset($_POST['service_oid'])) ? $_POST['service_oid'] : "";
  $comments = (isset($_POST['comments'])) ? $_POST['comments'] : ""; 
  $billing_key = (isset($_POST['billing_key'])) ? $_POST['billing_key'] : ""; 
  $securityCode = (isset($_POST['securityCode'])) ? $_POST['securityCode'] : ""; 
  $totalAmount = (isset($_POST['totalAmount'])) ? $_POST['totalAmount'] : ""; 
  $currency = (isset($_POST['currency'])) ? $_POST['currency'] : ""; 
  $firstName = (isset($_POST['firstName'])) ? $_POST['firstName'] : ""; 
  $lastName = (isset($_POST['lastName'])) ? $_POST['lastName'] : ""; 
  $email = (isset($_POST['email'])) ? $_POST['email'] : "";
  $resultUrl = (isset($_POST['resultUrl'])) ? $_POST['resultUrl'] : "";

  $BILLING_CURLOPT_HTTPHEADER = array(
    "cache-control: no-cache",
    "content-type: application/json; charset=UTF-8",
    "referer: $SERVER_NAME",
    "Authorization: Bearer $access_token"             // [필수] 발급받은 Access Token
  ); 

  $payParams = array (
		"service_id" => $service_id,                      // [필수] 파트너 ID
		"service_oid" => $service_oid,                    // [선택] 주문번호
    "comments" => $comments,                          // [필수] 상품명
    "billing_key" => $billing_key,                    // [필수] 빌링키 (카드정보를 암호화 한 키 값)
		"securityCode" => $securityCode,                  // [필수] 카드 CVC/CVV 번호
		"totalAmount" => $totalAmount,                    // [필수] 결제 요청금액
		"currency" => $currency,                          // [필수] 통화
		"firstName" => $firstName,                        // [선택] 카드소유주 이름 (보내지 않을 경우, 최초 결제시 입력한 카드소유주 이름으로 결제요청이 됩니다.)
		"lastName" => $lastName,                          // [선택] 카드소유주 성 (보내지 않을 경우, 최초 결제시 입력한 카드소유주 성으로 결제요청이 됩니다.)
		"email" => $email,                                // [선택] 이메일 주소  (보내지 않을 경우, 최초 결제시 입력한 이메일 주소로 결제요청이 됩니다.)
		"resultUrl" => $resultUrl                         // [선택] 해당 파라미터(resultUrl)는 별도의 기능은 하지 않으나, 파트너사에서 빌링키 결제 성공시 리다이렉트 하는 등 활용할 수 있는 파라미터입니다.
  );

  $post_data = json_encode($payParams);

	$ch = curl_init($bilingKeyUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $BILLING_CURLOPT_HTTPHEADER);
	
	ob_start();
	$bilingRes = curl_exec($ch);
	$bilingBuffer = ob_get_contents();
  $bilingResult = json_decode($bilingBuffer);
	ob_end_clean();

  if ($bilingResult->result !== 'A0000') {
    $resultData = array (
      "result" => $bilingResult->result,
      "message" => $bilingResult->message
    );  
  } else {
    $resultData = array (
      "type" => $bilingResult->type,
      "result" => $bilingResult->result,
      "message" => $bilingResult->message,
      "resultUrl" => $bilingResult->resultUrl,
      "api_id" => $bilingResult->api_id,
      "api_date" => $bilingResult->api_date,
      "service_oid" => $bilingResult->info->service_oid,
      "comments" => $bilingResult->info->comments,
      "pay_type" => $bilingResult->info->pay_type,
      "billing_key" => $bilingResult->info->billing_key,
      "totalAmount" => $bilingResult->info->totalAmount,
      "currency" => $bilingResult->info->currency,
      "firstName" => $bilingResult->info->firstName,
      "lastName" => $bilingResult->info->lastName,
      "email" => $bilingResult->info->email,
      "card_number" => $bilingResult->info->card_number,
      "submitTimeUtc" => $bilingResult->info->submitTimeUtc
    );
  }

  $jsonData = json_encode($resultData, JSON_UNESCAPED_UNICODE);
	echo $jsonData;
  exit;

} catch (Exception $e) {
  $errMsg = $e->getMessage();
	
	$message = ($errMsg != '') ? $errMsg : "빌링키 결제 요청 에러";
	
	$DATA = "{\"result\":\"error\", \"message\":\"$message\"}";
	
	echo $DATA;
}
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
   * 토큰 인증 요청
   */
	$postData = array (
    "service_id" => $service_id,
    "service_key" => $service_key,
    "code" => "as12345678"
  );

  $CURLOPT_HTTPHEADER = array(
      "cache-control: no-cache",
      "content-type: application/json; charset=UTF-8",
      "referer: $SERVER_NAME"
  );

  $ch = curl_init($tokenUrl);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
  curl_setopt($ch, CURLOPT_HTTPHEADER, $CURLOPT_HTTPHEADER);

  ob_start();
  $AuthRes = curl_exec($ch);
  $AuthBuffer = ob_get_contents();
  ob_end_clean();

  // Converting To Object
  $authResult = json_decode($AuthBuffer);

  if (!isset($authResult->result)) throw new Exception("파트너 인증요청 실패");
  if ($authResult->result !== 'T0000') throw new Exception($authResult->message);

  $access_token = $authResult->access_token; // 인증 토큰

  /**
   * 해외카드 결제취소(cancel) 요청
   */
	$comments = (isset($_POST['comments'])) ? $_POST['comments'] : "";
	$service_oid = (isset($_POST['service_oid'])) ? $_POST['service_oid'] : "";
	$pay_id = (isset($_POST['pay_id'])) ? $_POST['pay_id'] : ""; 
  $totalAmount = (isset($_POST['totalAmount'])) ? $_POST['totalAmount'] : "";
	$currency = (isset($_POST['currency'])) ? $_POST['currency'] : "";
	$resultUrl = (isset($_POST['resultUrl'])) ? $_POST['resultUrl'] : "";

  $CANCEL_CURLOPT_HTTPHEADER = array(
    "cache-control: no-cache",
    "content-type: application/json; charset=UTF-8",
    "referer: $SERVER_NAME",
    "Authorization: Bearer $access_token"   // [필수] 발급받은 Access Token
  ); 

  $cancelParams = array (
		"service_id" => $service_id,            // [필수] 파트너 ID
		"comments" => $comments,                // [필수] 상품명
		"service_oid" => $service_oid,          // [필수] 주문번호
		"pay_id" => $pay_id,                    // [필수] 취소할 결제건의 api_id
		"totalAmount" => $totalAmount,          // [필수] 결제 취소 요청금액
		"currency" => $currency,                // [필수] 통화
		"resultUrl" => $resultUrl               // [선택] 그대로 응답 파라미터로 반환
  );

  $post_data = json_encode($cancelParams);
	
	$ch = curl_init($cancelUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $CANCEL_CURLOPT_HTTPHEADER);
	
	ob_start();
	$cancelRes = curl_exec($ch);
	$cancelBuffer = ob_get_contents();
  $cancelResult = json_decode($cancelBuffer);
	ob_end_clean();

  if ($cancelResult->result !== 'A0000') {
    $resultData = array (
      "result" => $cancelResult->result,
      "message" => $cancelResult->message
    );  
  } else {
    $resultData = array (
      "type" => $cancelResult->type,
      "result" => $cancelResult->result,
      "message" => $cancelResult->message,
      "resultUrl" => $cancelResult->resultUrl,
      "api_date" => $cancelResult->api_date,
      "service_oid" => $cancelResult->info->service_oid,
      "totalAmount" => $cancelResult->info->totalAmount,
      "currency" => $cancelResult->info->currency,
      "submitTimeUtc" => $cancelResult->info->submitTimeUtc
    );
  }

  $jsonData = json_encode($resultData, JSON_UNESCAPED_UNICODE);
	echo $jsonData;
  exit;

} catch (Exception $e) {
  $errMsg = $e->getMessage();
	
	$message = ($errMsg != '') ? $errMsg : "승인취소 요청 에러";
	
	$DATA = "{\"result\":\"error\", \"message\":\"$message\"}";
	
	echo $DATA;
}
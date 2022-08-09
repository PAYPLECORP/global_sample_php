<?php
// Payple Request URL과 페이플 계정(service_id, service_key) , SERVERNAME 관리

/* 파트너 인증 Request URL */
//$tokenUrl = "https://api.payple.kr/gpay/oauth/1.0/token";      // REAL
$tokenUrl = "https://demo-api.payple.kr/gpay/oauth/1.0/token";   // TEST

/* 해외카드 결제취소 Request URL */
//$cancelUrl = "https://api.payple.kr/gpay/cancel";      // REAL
$cancelUrl = "https://demo-api.payple.kr/gpay/cancel";   // TEST

/* 해외카드 빌링키 결제 Request URL */
//$bilingKeyUrl = "https://api.payple.kr/gpay/billingKey";      // REAL
$bilingKeyUrl = "https://demo-api.payple.kr/gpay/billingKey";   // TEST

/* 테스트 계정 */
$service_id = "demo";
$service_key = "abcd1234567890";

/* Server Name */
$SERVER_NAME = $_SERVER['HTTP_HOST'];

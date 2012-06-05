<?php
$sstitle = @$_REQUEST['data'];
$user = @$_REQUEST['user'];
$pass = @$_REQUEST['pass'];
define("SCHEDULE_TITLE", $sstitle);
define('BASE_URL', 'hogehoge/grn.cgi');
define('USER_NAME', $user);
define('PASSWORD', $pass);
 
// 月間予定表ページを取得
$url = getMonthlyPageUrl(time());
$page = getContents($url);
// iCalenderで出力
showCalender($page);
 
/**
 * ページのコンテンツを得る
 */
function getContents($pUrl)
{
    // HTMLデータ取得
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $pUrl );
    curl_setopt( $ch, CURLOPT_POST, 1);
    $post = "_system='1'&_account=" . USER_NAME . "&_password=" . PASSWORD;
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER,FALSE);
    curl_setopt( $ch, CURLOPT_TIMEOUT_MS, 1300 );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    $contents = curl_exec( $ch );
    curl_close();
    return $contents;
}
 
/**
 * 月間予定ページのURL
 */
function getMonthlyPageUrl($pDate)
{
    $result = BASE_URL . '/schedule/command_personal_month_icalexport?bdate=' . date('Y-m', $pDate) . '-01';
    return $result;
}
 
/**
 * iCalender形式で出力
 */
function showCalender($page)
{
    header('Content-Type: text/calendar; charset=utf-8');
    // そのままだと標準時になってしまうので、日本時間に変換
    $page = str_replace("VERSION:2.0","VERSION:2.0\nX-WR-TIMEZONE:Asia/Tokyo\nBEGIN:VTIMEZONE\nTZID:Asia/Tokyo\nX-LIC-LOCATION:Asia/Tokyo\nBEGIN:STANDARD\nTZOFFSETFROM:+0900\nTZOFFSETTO:+0900\nTZNAME:JST\nDTSTART:19700101T000000\nEND:STANDARD\nEND:VTIMEZONE",$page);
    $page = preg_replace("/DTSTART:[0-9A-Z]+/","$0+0900",$page);
    $page = preg_replace("/DTEND:[0-9A-Z]+/","$0+0900",$page);
    echo $page;
}

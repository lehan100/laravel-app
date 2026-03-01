<?php

namespace App\Helpers;

class Format
{
    public static function formatPhone($phone)
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phoneNumber) == 10) {
            $areaCode = substr($phoneNumber, -10, 4);
            $nextThree = substr($phoneNumber, -6, 3);
            $lastFour = substr($phoneNumber, -3, 3);
        }
        if (strlen($phoneNumber) > 10) {

            $areaCode = substr($phoneNumber, -11, 5);
            $nextThree = substr($phoneNumber, -6, 3);
            $lastFour = substr($phoneNumber, -3, 3);
        }
        $phoneNumber = $areaCode . " " . $nextThree . ' ' . $lastFour;
        return $phoneNumber;
    }
    public static function dateToString($date)
    {
        $time = \Illuminate\Support\Carbon::now('Asia/Ho_Chi_Minh');
        $date = \Illuminate\Support\Carbon::createFromDate($date, 'Asia/Ho_Chi_Minh');
        $time_vote =   $time->diffInSeconds($date);
        if ($time_vote < 60) {
            $result = $time_vote . " giây trước";
        } else if ($time_vote < 60 * 60) {
            $result = ceil($time_vote / 60) . " phút trước";
        } else if ($time_vote < 60 * 60 * 24) {
            $result = ceil($time_vote / (60 * 60)) . " giờ trước";
        } else if ($time_vote < (60 * 60 * 24 * 30)) {
            $result = ceil($time_vote / (60 * 60 * 24)) . " ngày trước";
        } else if ($time_vote < (60 * 60 * 24 * 30 * 12)) {
            $result = "Khoảng " . ceil($time_vote / (60 * 60 * 24 * 30)) . " tháng trước";
        } else {
            $result = "Khoảng " . ceil($time_vote / (60 * 60 * 24 * 365)) . " năm trước";
        }
        return $result;
    }
}

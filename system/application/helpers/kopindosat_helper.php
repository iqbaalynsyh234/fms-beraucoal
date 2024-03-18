<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * yyyy-mm-dd to dd-mm-yyyy
 *
 * @access	public
 * @return	date
 */
if (!function_exists('kopindosatformatdate')) {
    function kopindosatformatdate($d) {
        $new = strtotime($d);
        return date('d/m/Y', $new);
    }
}

/**
 * yyyy-mm-dd hh:ii:ss to dd-mm-yyyy hh:ii:ss
 *
 * @access	public
 * @return	datetime
 */
if (!function_exists('kopindosatformatdatetime')) {
    function kopindosatformatdatetime($dt) {
        $new = strtotime($dt);
        return date('d/m/Y H:i:s', $new);
    }
}

if (!function_exists('get_time_difference')) {
    function get_time_difference($date1, $date2) {
        $date1 = strtotime($date1);
        $date2 = strtotime($date2);
        if ($date1 !== - 1 && $date2 !== - 1) {
            if ($date2 > $date1) {
                $diff = $date2 - $date1;
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                
                return array($days, $hours, $minutes, intval($diff));
            }
        }
        
        return false;
    }
}

function xlsBOF() {
    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
    return;
}

// Function penanda akhir file (End Of File) Excel

function xlsEOF() {
    echo pack("ss", 0x0A, 0x00);
    return;
}

// Function untuk menulis data (angka) ke cell excel

function xlsWriteNumber($Row, $Col, $Value) {
    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
    echo pack("d", $Value);
    return;
}

// Function untuk menulis data (text) ke cell excel

function xlsWriteLabel($Row, $Col, $Value) {
    $L = strlen($Value);
    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
    echo $Value;
    return;
}


?>

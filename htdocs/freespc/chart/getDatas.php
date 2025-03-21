<?php
// 在开始时启动输出缓冲，而不是尝试清除不存在的缓冲区
ob_start();
// 设置内容类型头
header('Content-type: text/xml; charset=utf-8');
require_once('../load.php');

$id = $_COOKIE["chart_id"];
$type = $_COOKIE["chart_type"];
$minDate = $_COOKIE["chart_minTime"];
$maxDate = $_COOKIE["chart_maxTime"];

require_once('../includes/chart.class.php');
$chart = new Chart($id,true);	
if( $chart->checkExist() ){
	$parameters = $chart->getParameters();
}

global $wpdb;
require_db();

try {
    // 使用 DOMDocument 创建标准的 XML 文档
    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;
    
    // 创建根元素
    $chartElement = $dom->createElement('chart');
    $chartElement->setAttribute('id', $id);
    $chartElement->setAttribute('type', $type);
    
    $datas = $wpdb->get_results("SELECT * FROM chart_$id WHERE data_time BETWEEN '$minDate' AND '$maxDate' ORDER BY data_time ASC LIMIT 1000", ARRAY_A);
    
    $chartElement->setAttribute('count', count($datas));
    $chartElement->setAttribute('sampleSize', $parameters['sample_size']);
    $chartElement->setAttribute('usl', $parameters['usl']);
    $chartElement->setAttribute('lsl', $parameters['lsl']);
    
    $dom->appendChild($chartElement);
    
    // 创建数据元素
    $datasElement = $dom->createElement('datas');
    $chartElement->appendChild($datasElement);
    
    if(is_array($datas)){
        switch($type){
            case TYPE_XR:
            case TYPE_XS:			
            case TYPE_IMR:
                $valueType = 'xbar';
                if($type == TYPE_IMR)
                    $valueType = 'x_1';
                foreach($datas as $data){
                    $dataElement = $dom->createElement('data');
                    $dataElement->setAttribute('id', $data['id']);
                    
                    $valueElement = $dom->createElement('value', $data[$valueType]);
                    $dataElement->appendChild($valueElement);
                    
                    $uclElement = $dom->createElement('ucl', $data['ucl']);
                    $dataElement->appendChild($uclElement);
                    
                    $lclElement = $dom->createElement('lcl', $data['lcl']);
                    $dataElement->appendChild($lclElement);
                    
                    $value2Element = $dom->createElement('value2', $data['stat_value']);
                    $dataElement->appendChild($value2Element);
                    
                    $ucl2Element = $dom->createElement('ucl_2', $data['ucl_2']);
                    $dataElement->appendChild($ucl2Element);
                    
                    $statusElement = $dom->createElement('status', $data['status']);
                    $dataElement->appendChild($statusElement);
                    
                    $againstElement = $dom->createElement('against', $data['against']);
                    $dataElement->appendChild($againstElement);
                    
                    $dataTimeElement = $dom->createElement('data_time', $data['data_time']);
                    $dataElement->appendChild($dataTimeElement);
                    
                    $datasElement->appendChild($dataElement);
                }
            break;
            case TYPE_P:
            case TYPE_NP:			
            case TYPE_U:
            case TYPE_C:
                $valueType = 'rate';
                if($type == TYPE_NP || $type == TYPE_C)
                    $valueType = 'ng_count';
                foreach($datas as $data){
                    $dataElement = $dom->createElement('data');
                    $dataElement->setAttribute('id', $data['id']);
                    
                    $valueElement = $dom->createElement('value', $data[$valueType]);
                    $dataElement->appendChild($valueElement);
                    
                    $uclElement = $dom->createElement('ucl', $data['ucl']);
                    $dataElement->appendChild($uclElement);
                    
                    $lclElement = $dom->createElement('lcl', $data['lcl']);
                    $dataElement->appendChild($lclElement);
                    
                    $clElement = $dom->createElement('cl', $data['cl']);
                    $dataElement->appendChild($clElement);
                    
                    $statusElement = $dom->createElement('status', $data['status']);
                    $dataElement->appendChild($statusElement);
                    
                    $againstElement = $dom->createElement('against', $data['against']);
                    $dataElement->appendChild($againstElement);
                    
                    $dataTimeElement = $dom->createElement('data_time', $data['data_time']);
                    $dataElement->appendChild($dataTimeElement);
                    
                    $datasElement->appendChild($dataElement);
                }
            break;
        }
    }
    
    // 在输出 XML 之前清除所有缓冲区内容
    ob_end_clean();
    
    // 输出 XML
    echo $dom->saveXML();
    
} catch (Exception $e) {
    // 清除之前的输出
    ob_end_clean();
    error_log("Error in getDatas.php: " . $e->getMessage());
    
    // 使用 DOMDocument 创建错误 XML
    $dom = new DOMDocument('1.0', 'utf-8');
    $chartElement = $dom->createElement('chart');
    $errorElement = $dom->createElement('error', $e->getMessage());
    $chartElement->appendChild($errorElement);
    $dom->appendChild($chartElement);
    
    echo $dom->saveXML();
}
?>
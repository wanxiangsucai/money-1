<?php
header('Content-Type: application/json');

class AmountConverter {
    private $chnNumChar = ["零","壹","贰","叁","肆","伍","陆","柒","捌","玖"];
    private $chnUnitSection = ["","万","亿","万亿"];
    private $chnUnitChar = ["","拾","佰","仟"];
    
    public function numberToChn($num) {
        $num = round($num, 2);
        $integer = floor($num);
        $fraction = round(($num - $integer) * 100);
        
        $result = $this->integerToChn($integer);
        
        if ($fraction > 0) {
            $result .= "元" . $this->fractionToChn($fraction);
        } else {
            $result .= "元整";
        }
        
        return $result;
    }
    
    private function integerToChn($num) {
        if($num === 0) {
            return $this->chnNumChar[0];
        }
        
        $unitPos = 0;
        $strIns = "";
        $needZero = false;
        
        while($num > 0) {
            $section = $num % 10000;
            if($needZero) {
                $strIns = $this->chnNumChar[0] . $strIns;
            }
            $strIns = $this->sectionToChinese($section) . 
                      ($section !== 0 ? $this->chnUnitSection[$unitPos] : '') . 
                      $strIns;
            $needZero = ($section < 1000) && ($section > 0);
            $num = floor($num / 10000);
            $unitPos++;
        }
        
        return $strIns;
    }
    
    private function fractionToChn($fraction) {
        $result = "";
        $jiao = floor($fraction / 10);
        $fen = $fraction % 10;
        
        if ($jiao > 0) {
            $result .= $this->chnNumChar[$jiao] . "角";
        }
        
        if ($fen > 0) {
            $result .= $this->chnNumChar[$fen] . "分";
        }
        
        return $result;
    }
    
    private function sectionToChinese($section) {
        $strIns = "";
        $unitPos = 0;
        $zero = true;
        
        while($section > 0) {
            $v = $section % 10;
            if($v === 0) {
                if(!$zero) {
                    $zero = true;
                    $strIns = $this->chnNumChar[0] . $strIns;
                }
            } else {
                $zero = false;
                $strIns = $this->chnNumChar[$v] . 
                          ($unitPos > 0 ? $this->chnUnitChar[$unitPos] : '') . 
                          $strIns;
            }
            $unitPos++;
            $section = floor($section / 10);
        }
        
        return $strIns;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $amount = isset($data['amount']) ? $data['amount'] : 0;
    
    $converter = new AmountConverter();
    $result = $converter->numberToChn($amount);
    
    echo json_encode(['result' => $result]);
} else {
    echo json_encode(['error' => 'Invalid request method']);
}
?>
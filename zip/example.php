<?php

   /**
     * 普通模板
     * */
    private function voucherCustomer($arrLists)
    {
        if (!is_dir($saveDir = SITE_PATH.'/tmp/zip/'.date('ym/dH/is/'))) {
            mkdir($saveDir, 0777, true);
        }
        $arrFileLists = [];

        $strZip = $saveDir.'image.zip'; // 压缩包所在的位置路径

        foreach($arrLists as $k => $row){
            if ($image = $row['uploadVoucher']) {
                if($image){
                    $strFileName = $saveDir.$row['plateNumber'].'-'.date('Ymd-His', $row['violationTime']).'.jpg';
                    @copy(SITE_PATH.$image, $strFileName);
                    $arrFileLists[] = $strFileName;
                }
            }
        }

        $objZip = new ZipArchive();
        $objZip -> open($strZip,ZipArchive::CREATE);   //打开压缩包
        foreach($arrFileLists as $file){
            $objZip -> addFile($file,basename($file));
        }
        $objZip->close();
        return $strZip;
    }

    /**
     * 特殊客户模板
     * */
    private function voucherCustomerVip($arrLists)
    {
        $objZip = _lib('Zip');


        if (!is_dir($saveDir = SITE_PATH.'/tmp/zip/'.date('ym/dH/is/'))) {
            mkdir($saveDir, 0777, true);
        }
        $strZip = $saveDir.'image.zip'; // 压缩包所在的位置路径
        $arrFileName = [];
        # 按照车牌号创建文件夹
        foreach($arrLists as $k => $row){
            if($arrFileName[$row['plateNumber']]){
                $arrFileName[$row['plateNumber']]['fraction'] += $row['violationPoint'];
                $arrFileName[$row['plateNumber']]['actualFine'] += $row['actualFine'];
            }else{
                $arrFileName[$row['plateNumber']]['fraction'] = $row['violationPoint'];
                $arrFileName[$row['plateNumber']]['actualFine'] += $row['actualFine'];
            }
        }
        foreach($arrFileName as $k => $v){
            if (!is_dir($strFilePath = $saveDir.$k.'-'.$v['fraction'].'分-'.$v['actualFine'].'罚款/')) {
                mkdir($strFilePath, 0777, true);
            }
            $arrFileName[$k]['file_path'] = $strFilePath;
        }
        $arrSameName = [];
        foreach($arrLists as $k => $row){
            if ($image = $row['uploadVoucher']) {
                if($image){
                    $strTempFilePath = $arrFileName[$row['plateNumber']]['file_path'];
                    $strTempFileName = $strTempFilePath.date('Ymd', $row['violationTime']).'-'.$row['violationPoint'].'分-'.$row['actualFine'].'罚款.jpg';
                    if(in_array($strTempFileName, $arrSameName)){
                        $strTempFileName = $strTempFilePath.date('Ymd_H_i_s', $row['violationTime']).'-'.$row['violationPoint'].'分-'.$row['actualFine'].'罚款.jpg';
                    }else{
                        $arrSameName[] = $strTempFileName;
                    }
                    @copy(SITE_PATH.$image, $strTempFileName);
                }
            }
            $intKey = $k+1;
            if ($strImgBefore = $row['upload_voucher_before']) {
                if($strImgBefore){
                    $strTempFilePath = $arrFileName[$row['plateNumber']]['file_path'];
                    @copy(SITE_PATH.$strImgBefore, $strTempFilePath.$intKey.'处理前.jpg');
                }
            }
            if ($strImgAfter = $row['upload_voucher_after']) {
                if($strImgAfter){
                    $strTempFilePath = $arrFileName[$row['plateNumber']]['file_path'];
                    @copy(SITE_PATH.$strImgAfter, $strTempFilePath.$intKey.'处理后.jpg');
                }
            }
        }
        $objZip -> zipInit($saveDir,$strZip);
        return $strZip;
    }
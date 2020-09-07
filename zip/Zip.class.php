<?php
class Zip{

    /**
     * description:主方法：生成压缩包
     * @author: MY
     * @param $dir_path  想要压缩的目录：如 './demo/'
     * @param $zipName   压缩后的文件名：如 './folder/demo.zip'
     * @return string
     */
    function zipInit($dir_path, $zipName)
    {
        $relationArr = array(
            $dir_path => array(
                'originName' => $dir_path,
                'is_dir' => true,
                'children' => array()
            )
        );



        $this->modifiyFileName($dir_path, $relationArr[$dir_path]['children']);
        $key = array_keys($relationArr);
        $val = array_values($relationArr);
        $zip = new ZipArchive();
        //ZIPARCHIVE::CREATE没有即是创建
        $zip->open($zipName, ZipArchive::CREATE);

        $this->zipDir($key[0], '', $zip, $val[0]['children']);
        $zip->close();
        #$this->restoreFileName($key[0], $val[0]['children']);
        return true;
    }

    function zipDir($real_path, $zip_path, &$zip, $relationArr)
    {
        $sub_zip_path = empty($zip_path) ? '' : $zip_path . '/';

        if (is_dir($real_path)) {
            foreach ($relationArr as $k => $v) {

                if ($v['is_dir']) {  //是文件夹
                    $zip->addEmptyDir($sub_zip_path . $v['originName']);
                    $this->zipDir($real_path . $k, $sub_zip_path . $v['originName'], $zip, $v['children']);
                } else { //不是文件夹

                    $zip->addFile($real_path . '/' . $k, $sub_zip_path . $k);
/*                    $zip->deleteName($sub_zip_path . $v['originName']);
                    $zip->renameName($sub_zip_path . $k, $sub_zip_path . $v['originName']);*/
                }
            }
        }
    }

    function modifiyFileName($path, &$relationArr)
    {
        if (!is_dir($path) || !is_array($relationArr)) {
            return false;
        }
        if ($dh = opendir($path)) {
            $count = 0;
            while (($file = readdir($dh)) !== false) {
                if(in_array($file,array('.', '..', null))) continue; //无效文件，重来

                if (is_dir($path . '/' . $file)) {
                    $newName = $file;
                    $relationArr[$newName] = array(
                        'originName' => $file,
                        'is_dir' => true,
                        'children' => array()
                    );
                    #rename($path . '/' . $file, $path . '/' . iconv('GBK', 'UTF-8', $file));
                    $this->modifiyFileName($path  . $newName, $relationArr[$newName]['children']);
                    $count++;
                } else {
                    $arrName = explode('.',$file);
                    $extension = $arrName[1];
                    $newName = $arrName[0];
                    $relationArr[$file] = array(
                        'originName' => $file,
                        'is_dir' => false,
                        'children' => array()
                    );
                   # rename($path . '/' . $file, $path . '/' . $file);
                    $count++;
                }
            }
        }
    }

    function restoreFileName($path, $relationArr)
    {
        foreach ($relationArr as $k => $v) {
            if (!empty($v['children'])) {
                $this->restoreFileName($path . '/' . $k, $v['children']);
                rename($path . '/' . $k, $path . '/' . $v['originName']);
            } else {
                #rename($path . '/' . $k, $path . '/' . $v['originName']);
            }
        }
    }
}
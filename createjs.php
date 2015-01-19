
<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once './PHPExcel/PHPExcel.php';
require_once './PHPExcel/PHPExcel/IOFactory.php';
require_once './PHPExcel/PHPExcel/Reader/Excel5.php';
define('MAX_ROW' , 550);//EXCEL最大行
$type;
$row_init = 2;
$filename;
if($_POST['leadExcel'] == "true")
{
    $filename = $_FILES['inputExcel']['name'];
    $tmp_name = $_FILES['inputExcel']['tmp_name'];
    $type =  "lan";
    $msg = uploadFile($filename,$tmp_name);
    
}
if($_POST['leadExcelHelp'] == "true")
{
    $filename = $_FILES['inputExcelHelp']['name'];
    $tmp_name = $_FILES['inputExcelHelp']['tmp_name'];
    $type =  "help";
    $msg = uploadFile($filename,$tmp_name);
    
}


echo "type:";
echo $type;
echo "\r\n";
function uploadFile($file,$filetempname) 
{
    global $row_init; 
    global $type; 
    $filePath = $_POST['filepath'] == ''?'excels/':$_POST['filepath'];  
    $time = date("y-m-d-H-i-s");//当前上传的时间 
    $extend = strrchr ($file,'.');//获取上传文件的扩展名
    $name = $time . $extend;//上传后的文件名
    $uploadfile = $filePath . $name;//上传后的文件名地址
    if($type == "lan")
    {

        $outFile = $_POST['filename'] == ''?"lan.js":$_POST['filename'];
        $outFile_other = $_POST['filename_other'] == ''?"lan_other.js":$_POST['filename_other'];
    }
    else
    {

        $outFile = $_POST['filename'] == ''?"help.js":$_POST['filename'];
        $outFile_other = $_POST['filename_other'] == ''?"help_other.js":$_POST['filename_other'];
    }
    
    
    // $outFile_log = $_POST['filename_log'] == ''?"log.txt":$_POST['filename_log'];
    //move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    $result = move_uploaded_file($filetempname,$uploadfile);
    if($result) //如果上传文件成功，就执行导入excel操作
    {
        $fp = openFile($outFile,'a');
        
        
        $fp_other = openFile($outFile_other,'a');
        
        
        // $fp_log = openFile($outFile_log,'a');
        
        
        
        $fileType = PHPExcel_IOFactory::identify($uploadfile);//excel类型

        /*$PHPExcel = new PHPExcel();
        $PHPReader = new PHPExcel_Reader_Excel2007();
        $PHPExcel = $PHPReader->load($uploadfile);
        $currentSheet = $PHPExcel->getSheet(0);
        $highestRow = $currentSheet->getHighestRow();
        $highestColumn = $currentSheet->getHighestColumn();
        //$currentRow = 2;
        //$content['usernum'] = $currentSheet->getCell('C'.$currentRow)->getValue();*/
        $PHPReader = PHPExcel_IOFactory::createReader($fileType);
        $PHPExcel = $PHPReader->load($uploadfile); 
        //$currentSheet = $PHPExcel->getSheet(0);
        //$PHPExcel->getSheetCount();
        //$highestRow = $currentSheet->getHighestRow();      //取得总行数 
        //$highestColumn = $currentSheet->getHighestColumn(); //取得总列数
        if($type=="lan")
        {
            


            fwrite($fp,"(function($){\n\n    $.su = $.su || {};    $.su.CHAR = {\n");
            fwrite($fp_other,"(function($){\n\n    $.su = $.su || {};    $.su.CHAR = {\n");

            $sheetCount = $PHPExcel->getSheetCount();
            for($indexSheet=0; $indexSheet<$sheetCount; ++$indexSheet){
                $currentSheet = $PHPExcel->getSheet($indexSheet);//获取当前工作薄对象
                $currentSheetName = $currentSheet->getTitle();
                if($currentSheetName == "Worksheet")
                {
                    continue;
                }
                fwrite($fp, "       $currentSheetName :{ \n");
                fwrite($fp_other, "       $currentSheetName :{ \n");
                excelToJsonLan($currentSheet,$indexSheet,$fp,$fp_other);
            }
            fwrite($fp, "       \"\" :     \"\"\n");
            fwrite($fp,"    };\n})(jQuery);");
            fclose($fp);
            
            fwrite($fp_other, "       \"\" :     \"\"\n");
            fwrite($fp_other,"    };\n})(jQuery);");
            fclose($fp_other);
        }
        else
        {

            fwrite($fp,"(function($){\n\n    $.su = $.su || {};    $.su.CHAR = ||{};\n $.su.CHAR.HELP ={\n");
            fwrite($fp_other,"(function($){\n\n    $.su = $.su || {};   $.su.CHAR = ||{};\n $.su.CHAR.HELP ={\n");
            $sheetCount = $PHPExcel->getSheetCount();
            for($indexSheet=0; $indexSheet<$sheetCount; ++$indexSheet){
                $currentSheet = $PHPExcel->getSheet($indexSheet);//获取当前工作薄对象
                $currentSheetName = $currentSheet->getTitle();
                if($currentSheetName == "Worksheet")
                {
                    continue;
                }
                fwrite($fp, "       $currentSheetName :{ \n");
                fwrite($fp_other, "       $currentSheetName :{ \n");
                excelToJsonHelp($currentSheet,$indexSheet,$fp,$fp_other);
            }
            fwrite($fp, "       \"\" :     \"\"\n");
            fwrite($fp,"    };\n})(jQuery);");
            fclose($fp);
            
            fwrite($fp_other, "       \"\" :     \"\"\n");
            fwrite($fp_other,"    };\n})(jQuery);");
            fclose($fp_other);
        }
        
  //       for($indexSheet=0; $indexSheet<$sheetNumber; ++$indexSheet){
  //           $currentSheet = $PHPExcel->getSheet($indexSheet);//获取当前工作薄对象
  //           excelToJson($excelType,$arr,$currentSheet,$indexSheet,$fp,$fp_other,$fp_log);
  //       }
  //       fwrite($fp, "       \"\" :     \"\"\n");
  //       fwrite($fp,"    };\n})(jQuery);");
  //       fclose($fp);
        
        // fwrite($fp_other, "       \"\" :     \"\"\n");
  //       fwrite($fp_other,"    };\n})(jQuery);");
  //       fclose($fp_other);
    }
}


function excelToJsonLan($currentSheet,$indexSheet,$fp,$fp_other){
    $nameCol = "A";
    $enCol = "B";
    $otherCol = "C";
    $highestRow = $currentSheet->getHighestRow();      //取得总行数 
    $highestColumn = $currentSheet->getHighestColumn(); //取得总列数

    $str_name = "";
    $str_en = "";
    $str_other = "";
    $result_num = 0;
  
    global $row_init;
    global $name;
    global $filename;
    for($currentRow=$row_init; $currentRow<$highestRow; ++$currentRow){
            $str_name    = $currentSheet->getCell($nameCol . $currentRow)->getValue();
            $str_en     = $currentSheet->getCell($enCol . $currentRow)->getValue();
            $str_other  = $currentSheet->getCell($otherCol . $currentRow)->getValue();

            //LOGIN: {

            // fwrite($fp,         "           $str_name:", 28);
            // fwrite($fp_other,   "           $str_name:", 28);
            fwrite($fp,         "           $str_name:");
            fwrite($fp_other,   "           $str_name:");


           

            if($currentRow == $highestRow)
            {
                fwrite($fp, "\"$str_en\"\n");
                fwrite($fp_other, "\"$str_other\"\n");

                fwrite($fp, "},\n");
                fwrite($fp_other, "},\n");
            }
            else
            {
                fwrite($fp, "\"$str_en\",\n");
                fwrite($fp_other, "\"$str_other\",\n");
            }
    }
}


function excelToJsonHelp($currentSheet,$indexSheet,$fp,$fp_other){
    $TITLECol           = "A";
    $typeCol            = "B";
    $startCol           = "C";
    $endCol             = "D";
    $titleCol           = "E";
    $titleTransCol      = "F";
    $contentCol         = "G";
    $contentTransCol    = "H";
    $idCol              = "I";
    $childCol          = "J";
    $highestRow = $currentSheet->getHighestRow();      //取得总行数 
    $highestColumn = $currentSheet->getHighestColumn(); //取得总列数

    $str_name = "";
    $str_en = "";
    $str_other = "";
    $result_num = 0;
  
    global $row_init;
    global $name;
    global $filename;
    for($currentRow=$row_init; $currentRow<$highestRow+1; ++$currentRow){
            $str_type    = $currentSheet->getCell($typeCol . $currentRow)->getValue();
            $str_title = $currentSheet->getCell($titleCol . $currentRow)->getValue();
            $str_content = $currentSheet->getCell($contentCol . $currentRow)->getValue();
            $str_children = $currentSheet->getCell($childCol . $currentRow)->getValue();

            $str_start = $currentSheet->getCell($startCol . $currentRow)->getValue();
            $str_end = $currentSheet->getCell($endCol . $currentRow)->getValue();
            
            if($currentRow == $row_init)  //第一行，读取TITLE
            {
                $TITLE =  $currentSheet->getCell("A2")->getValue();
                fwrite($fp, "               TITLE:");
                fwrite($fp, "\"$TITLE\",\n");
                fwrite($fp, "               CONTENT: [");
            }
            else
            {
                if($currentRow == ($row_init+1))
                {
                    fwrite($fp, "{\n");
                }
                else
                {
                    if($str_type)
                    {
                        if($str_start)
                        {
                            fwrite($fp, "{                      \n");
                        }
                        else
                        {
                            fwrite($fp, " ,{                      \n");
                        }
                    }
                    // else if($str_children)
                    // {
                    //       // fwrite($fp, " \"$str_content\", \n");
                    //       // continue;
                    // }
                    else if(!$str_children)  //step没有child
                    {
                        // if($str_end)
                        // {
                        //     fwrite($fp, " \"$str_content\" \n");
                        // }
                        // else
                        // {
                        //     fwrite($fp, " \"$str_content\",\n");
                        // }
                        if($str_start)
                        {
                            fwrite($fp, " \n \"$str_content\"");
                        }
                        else
                        {
                            fwrite($fp, ",\n \"$str_content\"");
                        }
                        
                        if($str_end)
                        {
                            $end_arr = explode(',', $str_end);
                            echo "str_content:";echo $str_content;echo "\r\n";
                            foreach ($end_arr as $key => $value) {
                                echo $key;echo "\r\n";
                                fwrite($fp, "]\n}");
                            }
                        }
                        continue;
                    }
                    else
                    {
                        fwrite($fp, " ,\n{                      \n");
                    }
                    // if(($str_type!="step")&&$str_start)
                    // {
                    //     fwrite($fp, "                           children: [{\n");
                    // }
                    // else
                    // {
                    //     fwrite($fp, ",{                      \n");
                    // }
                    
                }
                // if(($str_type=="step")&&$str_children)
                // {
                //     fwrite($fp, "                           type: \"$str_type\",\n");
                //     if($str_title)
                //     {
                //         fwrite($fp, "                           title: \"$str_title\",\n");
                //     }
                //     if($str_title)
                //     {
                //         fwrite($fp, "                           title: \"$str_title\",\n");
                //     }
                // }
                
                


               
                if($str_title && $str_content)
                {
                    if($str_children)
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        
                        fwrite($fp, "                           title: \"$str_title\",\n");
                        // if( ($str_type=="step") && $str_start) 
                        // {
                        //     fwrite($fp, "                           content: [\"$str_content\"],\n");
                        // }
                        // else
                        // {
                        //     fwrite($fp, "                           content: \"$str_content\",\n");
                        // } 
                        fwrite($fp, "                           content: \"$str_content\",\n");
                        // fwrite($fp, "},\n");
                        if($str_type!="step")
                        {
                            fwrite($fp, "       children: [");
                        }
                        else
                        {
                            fwrite($fp, "       content: [\n");
                        }

                    }
                    else
                    {
                        fwrite($fp, "                           type: \"$str_type\",\n");
                        // fwrite($fp, "},");

                        // fwrite($fp, "{\n");
                        fwrite($fp, "                           title: \"$str_title\",\n");
                        // fwrite($fp, "},\n");


                        // fwrite($fp, "{\n");
                        fwrite($fp, "                           content: \"$str_content\"");
                        fwrite($fp, "\n");
                    }
                }
                if(!$str_title && !$str_content)
                {
                    if($str_children)
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        // fwrite($fp, "},");

                        if($str_type!="step")
                        {
                            fwrite($fp, "       children: [");
                        }
                        else
                        {
                            fwrite($fp, "       content: [\n");
                        }
                    }
                    else
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        fwrite($fp, "\n");
                    }
                }
                if(!$str_title && $str_content)
                {
                    if($str_children)
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        // fwrite($fp, "},");


                        // fwrite($fp, "{\n");
                        fwrite($fp, "                           content: \"$str_content\",\n");
                        // fwrite($fp, "\n");

                        if($str_type!="step")
                        {
                            fwrite($fp, "       children: [");
                        }
                        else
                        {
                            fwrite($fp, "       content: [\n");
                        }
                    }
                    else
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        // fwrite($fp, "},");


                        // fwrite($fp, "{\n");
                        fwrite($fp, "                           content: \"$str_content\"");
                        fwrite($fp, "\n");
                    }
                }
                if($str_title && !$str_content)
                {
                    if($str_children)
                    {
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        // fwrite($fp, "},");

                        // fwrite($fp, "{\n");
                        fwrite($fp, "                           title: \"$str_title\",\n");
                        // fwrite($fp, "},\n");

                       

                        if($str_type!="step")
                        {
                            fwrite($fp, "       children: [");
                        }
                        else
                        {
                            fwrite($fp, "       content: [\n");
                        }
                    }
                    else
                    { 
                        if($str_type)
                        {
                            fwrite($fp, "                           type: \"$str_type\",\n");
                        }
                        // fwrite($fp, "},");

                        // fwrite($fp, "{\n");
                        fwrite($fp, "                            title: \"$str_title\"\n");
                        // fwrite($fp, "},\n");
                    }
                }
                if(!$str_children)
                {
                    fwrite($fp, "                       }");
                }
                if($str_end)
                {
                    $end_arr = explode(',', $str_end);
                    echo "str_content:";echo $str_content;echo "\r\n";
                    foreach ($end_arr as $key => $value) {
                        echo $key;echo "\r\n";
                        fwrite($fp, "]\n}");
                    }
                }
               
                // if($str_end)
                // {
                //      fwrite($fp, "]\n}");
                // }
             
            }
    }
    fwrite($fp, "]\n},\n");
}

function openFile($filename,$type){
    $fp = fopen($filename,$type);
    if(!$fp){
        echo "open failed";
        exit;
    }
    return $fp;
}



?>
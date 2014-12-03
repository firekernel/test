<?php

defined('IN_plcms') or exit('No permission resources.');

//模型缓存路径

define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_func('util','content');

class yueduxing {

	private $db;

	function __construct() {

        $this->export_book_id = array(679,730,466,655,682,576,628,309,287,269,344,587,267,488,513,681,690,393,593,713,695,744,497,802

,900,526,570,791,935,909,978,869,1102,1190,735,429,1027,1079,1241,1248,995,609,1608,1480,1422

,1614,939,1710,1678,1615,1350,1446,1548,1538,1893,1776,1735,2085,1943,1357,1832,1660,1757,1940,2086,1869,1975);







		$this->db = pc_base::load_model('books_model');

                $this->content_db = pc_base::load_model('content_model');





                $this->chapter_db = pc_base::load_model('book_chapter_model');

                $this->volume_db = pc_base::load_model('book_volume_model');

                $this->_userid = param::get_cookie('_userid');

		$this->_username = param::get_cookie('_username');

		$this->_groupid = param::get_cookie('_groupid');

                $this->type_db = pc_base::load_model('type_model');

                $this->booktag_db = pc_base::load_model('booktag_model');

                $this->member_db = pc_base::load_model('member_model');

                $this->fav_db=  pc_base::load_model('favorite_model');

                $this->readlog_db=  pc_base::load_model('read_log_model');

                $this->hits_db = pc_base::load_model('hits_model');

                $this->favor_db = pc_base::load_model('favor_model');

                $this->recommend_db = pc_base::load_model('recommend_model');

	}





    //1. 全局资源分类接口 

    public function get_book_type_all(){

        $type=$this->type_db->select();

        $count = count($type);

        // echo $count;

        header("Content-type:text/xml;charset=utf-8");

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

        echo "<result language=\"zh_CN\" version=\"1.1\">";

        if($count > 0)

        {

            echo "<status>";

            echo "<code>";

            echo "<![CDATA[   0    ]]>";

            echo "</code>";



            echo "<msg>";

            echo "<![CDATA[   成功    ]]>";

            echo "</msg>";



            echo "<total>";

            echo "</total>";



            echo "<start>";

            echo "<![CDATA[   0    ]]>";

            echo "</start>";



            echo "<count>";

            echo "<![CDATA[   $count    ]]>";

            echo "</count>";



            echo "<datas>";

            foreach ($type as $key => $value) {

                echo "<category>";

                    echo "<cate_id>";



                    echo "<![CDATA[   {$value["typeid"]}    ]]>";

                    echo "</cate_id>";



                    echo "<cate_name>";

                    echo "<![CDATA[   {$value["name"]}    ]]>";

                    echo "</cate_name>";

               

                echo "</category>";

            }

            echo "</datas>";



            echo "</status>";

        }

        else

        {

            echo "<status>";

            echo "<code>";

            echo "<![CDATA[   1    ]]>";

            echo "</code>";



            echo "<msg>";

            echo "<![CDATA[   失败    ]]>";

            echo "</msg>";

            echo "</status>";

        }

        echo "</result>";

    }



    //2. 获取所有授权作品ID和NAME  

    public function get_book_id(){

        $export_book_id = $this->export_book_id;

        header("Content-type:text/xml;charset=utf-8");

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

        echo "<result language=\"zh_cn\" version=\"1.0\">";

        

        foreach ($export_book_id as $val){

                $book=$this->db->get_one(array('bookid'=>$val));

                $bookid = $book["bookid"];

                $name = $book["bookname"];

                if($bookid == "")

                {

                    continue;

                }

                echo "<item>";

                echo "<id>";

                echo "<![CDATA[  $bookid  ]]>";

                echo "</id>";

                echo "<bookname>";

                echo "<![CDATA[  $name   ]]>";

                echo "</bookname>";

                echo "</item>";

        }

        echo "</result>";

        // $export_book_id

    }





    //3. 获取书籍属性列表

    public function get_book_info(){

        $bookid = $_GET["bookid"];

        header("Content-type:text/xml;charset=utf-8");

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

        $book=$this->db->get_one(array('bookid'=>$bookid));

        $type = $this->type_db->get_one(array('typeid'=>$book["catid"]));



         

         

        echo "<result language=\"zh_cn\" version=\"1.0\">";

            


            echo "<bookname>";

            echo "<![CDATA[  {$book["bookname"]}  ]]>";

            echo "</bookname>";



            echo "<bookid>";

            echo "<![CDATA[  {$book["bookid"]}  ]]>";

            echo "</bookid>";


            echo "<category>";

            echo "<![CDATA[  {$type["name"]}  ]]>";

            echo "</category>";



            echo "<author>";

            echo "<![CDATA[  {$book["author"]}  ]]>";

            echo "</author>";




            echo "<smallcover>";

            echo "<![CDATA[  {$book["cover"]}  ]]>";

            echo "</smallcover>";


            echo "<bigcover>";

            echo "<![CDATA[  {$book["cover"]}  ]]>";

            echo "</bigcover>";



            echo "<webcover>";

            echo "<![CDATA[  {$book["cover"]}  ]]>";

            echo "</webcover>";






            echo "<desc>";

            echo "<![CDATA[  {$book["description"]}  ]]>";

            echo "</desc>";



            echo "<status>";

            echo "<![CDATA[  {$book["isend"]}  ]]>";

            echo "</status>";


            $sql = "select * from pl_book_chapter where bookid=".$bookid;
            $chapters = $this->chapter_db->query($sql);
            $chapters_arr = $this->chapter_db->fetch_array();
            $count = count($chapters_arr);

         
            echo "<chaptercount>";

            echo "<![CDATA[  $count  ]]>";

            echo "</chaptercount>";





            echo "<wordcount>";

            echo "<![CDATA[  {$book["words"]}  ]]>";

            echo "</wordcount>";



            $tagStr = $book["tag"];
            //var_dump($tagStr);
            $arr = explode(",",$tagStr);
            //var_dump($arr);
            $tagViewStr = "";
            foreach($arr as $u)
            {
                $tag = $this->booktag_db->get_one(array('id'=>$u));
                $tagViewStr = $tagViewStr. $tag["name"] ." ";
                
            }

            echo "<tags>";

            echo "<![CDATA[  $tagViewStr  ]]>";

            echo "</tags>";

           




            $tablepre =  $this->hits_db->db_tablepre;

            $views=$this->hits_db->get_one(array('hitsid'=>$bookid));

            echo "<clickcount>";

            echo "<![CDATA[  {$views["views"]}  ]]>";

            echo "</clickcount>";


            $createtime = date('Y-m-d',$book["createtime"]); 
            echo "<createdate>";

            echo "<![CDATA[  {$createtime}  ]]>";

            echo "</createdate>";


            $updatetime = date('Y-m-d',$book["updatetime"]); 
            echo "<updatedate>";

            echo "<![CDATA[  {$updatetime}  ]]>";

            echo "</updatedate>";



        echo "</result>";

            

    }





    //4. 获取书籍章节列表

    public function get_chapter_info(){

        $bookid = $_GET["bookid"];

        header("Content-type:text/xml;charset=utf-8");

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

        echo "<result language=\"zh_cn\" version=\"1.0\">";

        $book=$this->db->get_one(array('bookid'=>$bookid));

        $volumn=$this->volume_db->select(array('bookid'=>$bookid));

        // echo $volumn["name"]; echo $volumn["id"]

        // var_dump($volumn);

        // echo "222";

        foreach($volumn as $key=>$v)

        {

             // var_dump($v);

            // echo "<vol>";

            //     echo "<volumename>";

            //     echo "<![CDATA[  {$v["name"]}  ]]>";

                $chapter=$this->chapter_db->select(array('volumeId'=>$v["id"]));

                foreach($chapter as $key2=>$v2)

                {

                    echo "<item>";

                        // echo "<url>";

                        // $url = './books/'.$v2['bookid'].'/'.$v2['id'].'.txt';

                        // echo "<![CDATA[  $url  ]]>";

                        // echo "</url>";



                        echo "<chaptername>";

                        echo "<![CDATA[  {$v2['title']}  ]]>";

                        echo "</chaptername>";



                        echo "<chapterid>";

                        echo "<![CDATA[  {$v2['id']}  ]]>";

                        echo "</chapterid>";



                        echo "<vip>";

                        if($v2['readpoint'] == 0)
                        {
                            echo "<![CDATA[ 0 ]]>";
                        }
                        else
                        {
                            echo "<![CDATA[ 1 ]]>";
                        }

                        // echo "<![CDATA[  {$book['charge']}  ]]>";

                        echo "</vip>";



                        // if($v2['updatetime'])

                        // {

                        //     $time = date('Y-m-d H:i:s', $v2['updatetime']);

                        //     echo "<updatetime>";

                        //     echo "<![CDATA[  $time  ]]>";

                        //     echo "</updatetime>";

                        // }

                        // else

                        // {

                        //     $time = date('Y-m-d H:i:s', $v2['inputtime']);

                        //     echo "<updatetime>";

                        //     echo "<![CDATA[  $time  ]]>";

                        //     echo "</updatetime>";

                        // }



                    echo "</item>";

                }

            //     echo "</volumename>";

            // echo "</vol>";

        }

 

        echo "</result>";

    }





    //5.获取章节内容

    public function get_chapter_content(){

        //$bookid = $_GET["bookid"];

        $chapterid = $_GET["chapterid"];

        header("Content-type:text/xml;charset=utf-8");

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";

        echo "<result language=\"zh_cn\" version=\"1.0\">";

        echo "<content>";

       

       // $chapter=$this->chapter_db->get_one(array('id'=>$chapterid, "bookid"=>$bookid));
        $chapter=$this->chapter_db->get_one(array('id'=>$chapterid));

        $url = './books/'.$chapter['bookid'].'/'.$chapter['id'].'.txt';       

        $str = file_get_contents($url);

        echo "<![CDATA[  $str  ]]>";

        echo "</content>";

        echo "</result>";

    }

        

    

        

     

    

    
    

    function down(){

        $tablepre = $this->db->db_tablepre;

        $bookid=  intval($_GET['id']);

        $sql="select id from ".$tablepre."author_income where buy_type in(2,3) and bookid=".$bookid;

        $this->db->query($sql);

        $isbuy=$this->db->fetch_array();

		$book=$this->db->get_one(array('bookid'=>$bookid),'bookname');

        if($isbuy){

            if(!file_exists('./books/'.$bookid.'/book_'.$bookid.'.txt')){

                        $book=$this->db->get_one(array('bookid'=>$bookid),'bookname');

                        $book_name=$book['bookname']."\r\n\r\n\r\n";

                        

                        $chapterlist=$this->chapter_db->listinfo(array('bookid'=>$bookid),' chapter_number asc',1,10000,'','','','', 'id,title,chapter_number');

                        $book_content=$book_name;

                        foreach($chapterlist as $val){

                            if($val['chapter_number']>0){

                                $book_content.="\r\n第".$val['chapter_number'].'章：'.$val['title']."\r\n";

                            }  else {

                                $book_content.=$val['title']."\r\n";

                            }

                            

                            $book_content.="\r\n".file_get_contents('./books/'.$bookid.'/'.$val['id'].'.txt');

                        }

                        

                        fwrite(fopen('./books/'.$bookid.'/book_'.$bookid.'.txt', "w+"),new_html_special_chars($book_content));

                    }



        $str = file_get_contents('./books/'.$bookid.'/book_'.$bookid.'.txt');



$filename = $book['bookname'];

header("Content-type: text/plain");

        header("Accept-Ranges: bytes");

        header("Content-Disposition: attachment; filename=".$filename);

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0" );

        header("Pragma: no-cache" );

        header("Expires: 0" ); 

        exit($str);

//            include template('content','down');

        }else{

            

            echo  "<script language=\"javascript\">alert('您没权限操作！');window.close();</script>";

        }

    }



}

?>
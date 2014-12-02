<?php
defined('IN_plcms') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');
class changdu {
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

    //2. 获取所有授权作品ID  
    public function get_book_id(){
        $export_book_id = $this->export_book_id;
        header("Content-type:text/xml;charset=utf-8");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        echo "<datas>";
        
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
        echo "</datas>";
        // $export_book_id
    }


    //3. 获取书籍属性列表
    public function get_book_info(){
        $bookid = $_GET["bookid"];
        header("Content-type:text/xml;charset=utf-8");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        $book=$this->db->get_one(array('bookid'=>$bookid));
        $type = $this->type_db->get_one(array('typeid'=>$book["catid"]));

         
         
        echo "<data>";
            echo "<cname>";
            echo "<![CDATA[  {$type["name"]}  ]]>";
            echo "</cname>";

            echo "<bookname>";
            echo "<![CDATA[  {$book["bookname"]}  ]]>";
            echo "</bookname>";

            echo "<bookid>";
            echo "<![CDATA[  {$book["bookid"]}  ]]>";
            echo "</bookid>";

            echo "<bookpic>";
            echo "<![CDATA[  {$book["cover"]}  ]]>";
            echo "</bookpic>";

            echo "<zzjs>";
            echo "<![CDATA[  {$book["description"]}  ]]>";
            echo "</zzjs>";

            echo "<bksize>";
            echo "<![CDATA[  {$book["words"]}  ]]>";
            echo "</bksize>";

            $tablepre =  $this->hits_db->db_tablepre;
            $views=$this->hits_db->get_one(array('hitsid'=>$bookid));
           

            echo "<weekvisit>";
            echo "<![CDATA[  {$views["weekviews"]}  ]]>";
            echo "</weekvisit>";

            echo "<monthvisit>";
            echo "<![CDATA[  {$views["monthviews"]}  ]]>";
            echo "</monthvisit>";

            echo "<allvisit>";
            echo "<![CDATA[  {$views["views"]}  ]]>";
            echo "</allvisit>";

            echo "<writestatus>";
            echo "<![CDATA[  {$book["isend"]}  ]]>";
            echo "</writestatus>";

            echo "<license>";
            echo "<![CDATA[  {$book["charge"]}  ]]>";
            echo "</license>";

        echo "</data>";
            
    }


    //4. 获取书籍章节列表
    public function get_chapter_info(){
        $bookid = $_GET["bookid"];
        header("Content-type:text/xml;charset=utf-8");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        echo "<data>";
        $book=$this->db->get_one(array('bookid'=>$bookid));
        $volumn=$this->volume_db->select(array('bookid'=>$bookid));
        // echo $volumn["name"]; echo $volumn["id"]
        // var_dump($volumn);
        // echo "222";
        foreach($volumn as $key=>$v)
        {
             // var_dump($v);
            echo "<vol>";
                echo "<volumename>";
                echo "<![CDATA[  {$v["name"]}  ]]>";
                $chapter=$this->chapter_db->select(array('volumeId'=>$v["id"]));
                foreach($chapter as $key2=>$v2)
                {
                    echo "<chapteritem>";
                        echo "<url>";
                        $url = './books/'.$v2['bookid'].'/'.$v2['id'].'.txt';
                        echo "<![CDATA[  $url  ]]>";
                        echo "</url>";

                        echo "<chaptername>";
                        echo "<![CDATA[  {$v2['title']}  ]]>";
                        echo "</chaptername>";

                        echo "<chapterid>";
                        echo "<![CDATA[  {$v2['id']}  ]]>";
                        echo "</chapterid>";

                        echo "<license>";
                        echo "<![CDATA[  {$book['charge']}  ]]>";
                        echo "</license>";

                        if($v2['updatetime'])
                        {
                            $time = date('Y-m-d H:i:s', $v2['updatetime']);
                            echo "<updatetime>";
                            echo "<![CDATA[  $time  ]]>";
                            echo "</updatetime>";
                        }
                        else
                        {
                            $time = date('Y-m-d H:i:s', $v2['inputtime']);
                            echo "<updatetime>";
                            echo "<![CDATA[  $time  ]]>";
                            echo "</updatetime>";
                        }

                    echo "</chapteritem>";
                }
                echo "</volumename>";
            echo "</vol>";
        }
 
        echo "</data>";
    }


    //5.获取章节内容
    public function get_chapter_content(){
        $bookid = $_GET["bookid"];
        $chapterid = $_GET["chapterid"];
        header("Content-type:text/xml;charset=utf-8");
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
        echo "<content>";
       
        $chapter=$this->chapter_db->get_one(array('id'=>$chapterid, "bookid"=>$bookid));
        $url = './books/'.$chapter['bookid'].'/'.$chapter['id'].'.txt';       
        $str = file_get_contents($url);
        echo "<![CDATA[  $str  ]]>";
        echo "</content>";
    }
        
        public function index(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $bookid=  intval($_GET[id]);
            $tablepre = $this->db->db_tablepre;
            
            $book=$this->db->get_one(array('bookid'=>$bookid));
            if(!$book){
                showmessage('作品不存在',APP_PATH);
            }
            $upbook['hits']=$book['hits']+1;
            $this->hits($book['bookid']);
            $bookid = $this->db->update($upbook,array('bookid'=>$book['bookid']));
            $volumes=  $this->volume_db->listinfo(array('bookid'=>$book['bookid']),'id ASC');
            $all_volume=  count($volume);
            $page=$_GET['page'];
            $chapterlist=$this->db->mylistinfo2('*',$tablepre.'book_chapter where timing=\'\' and bookid='.$book['bookid'],' order by chapter_number asc',$page,100,'10');
            foreach ($chapterlist as $val){

                $chapterarr[$val[volumeId]][]=$val;
            }

            foreach($volumes as $key=>$v){
                    $volumes[$key]['chapter']=$chapterarr[$v['id']];
            }
            foreach($volumes as $key=>$v){
                if(is_array($v['chapter'])){
                    $volume[$key]=$v;
                }
            }

            $pages = $this->db->pagess;
            $new_chapter=$this->chapter_db->listinfo(array('bookid'=>$book['bookid'],'timing'=>''),'chapter_number desc', 1, 4);
            $fist_chapter=$this->chapter_db->get_one(array('bookid'=>$book['bookid'],'chapter_number'=>1),'id');;
            $authorid_name=$this->member_db->get_one(array('phpssouid'=>$book[authorid]),'nickname');
            $chapter=$this->chapter_db->get_one(array('bookid'=>$book[bookid]),'title,inputtime','id desc');
            $sql="select sum(words) as all_word from ".$tablepre."book_chapter where chaptertype=1 and bookid=$book[bookid]";
            $this->db->query($sql);
            $words=$this->db->fetch_array();
            $book['all_word']=$words[0]['all_word'];
            $book['authorid_name']=$authorid_name['nickname'];
            $book['last_update']=$chapter['inputtime'];
            $book_type=  $this->type_db->get_one(array('typeid'=>$book['catid']),'name');
            $book['book_type']=$book_type['name'];
            $fav= $this->fav_db->count('bookid='.$book['bookid']);
            if($_userid){
            $isfav=$this->fav_db->count('bookid='.$book['bookid'].' and userid='.$_userid);
            }
            //全部下载和全本购买价格
            if($book['isend']==1){
                $paytype = 0;
                $if_buy = self::_check_buy($book['bookid'],'完本购买消费');
                
                if(!$if_buy){
                    $price=  $this->check_price($book['all_word']);
                    $book_visitor = sys_auth($book['bookid'].'|'.$price['all'].'|'.$paytype.'|'.$book['authorid'].'|2');
                    $down_visitor = sys_auth($book['bookid'].'|'.$price['down'].'|'.$paytype.'|'.$book['authorid'].'|3');
                    $down_buy=self::_check_buy($book['bookid'],'完本下载消费');
                    $buy_submit='<a class="tips-b" href="javascript:void(0);" onclick="buy_all_book(\''.$book_visitor.'\',\'buy\',\''.$price['all'].'\')">';
                    if(!$down_buy){
                        $down_submit='<a class="tips-b" href="javascript:void(0);" onclick="buy_all_book(\''.$down_visitor.'\',\'down\',\''.$price['down'].'\')">';
                    }else{
                        $down_submit='<a class="tips-b" href="'.APP_PATH.'index.html?m=content&c=book&a=down&id='.$book['bookid'].'">';
                    }
                }else{
                    $buy_submit='<a title="已经购买过了" href="#">';
                    $down_submit='<a href="'.APP_PATH.'index.html?m=content&c=book&a=down&id='.$book['bookid'].'">';
                }
                
            }
            
//            print_r($price);exit;
            
            //同类热门start
            $channelId= channel_type($book['catid']);//当前频道id
            $kind_hot=$this->db->listinfo(array('catid'=>$book['catid']),'hits desc',1,4);
            //同类热门end
            //频道热门start
            $channel_type=  get_child($channelId);
            $channel_typeid=  implode(',', $_SESSION['child']);
            $sql = "select * from " . $tablepre . "book where catid in($channel_typeid) order by hits desc limit 9";
            $this->db->query($sql);
            $channel_hot = $this->db->fetch_array();
     
//            print_r($channel_hot);exit;
            //频道热门end
            //免费热榜start
            $fsql = "select * from " . $tablepre . "book where catid in($channel_typeid) and charge=0 order by hits desc limit 9";
            $this->db->query($fsql);
            $free_hot = $this->db->fetch_array();
            $commentid='book_'.$book['catid'].'-'.$book['bookid'].'-1';

            $sql="select count(id) as total from ".$tablepre."comment_data_1 where commentid='$commentid' and reply=0";

            $this->db->query($sql);
            $comment = $this->db->fetch_array();
//print_r($book);exit;
            //免费热榜end
            $paytype = 0;
            $allow_visitor = sys_auth($book['bookid'].'|'.$paytype.'|'.$book['authorid'].'|11');
            $support_type = sys_auth($book['bookid'].'|'.$paytype.'|'.$book['authorid']);
            $SEO = seo(1);
            $SEO['title']='简介-'.$book['bookname'].'_';
            
            
            if($channelId=='56'){
                $catid=1;
            }else{
               $catid=2; 
            }
            include template('content','book_index');
        }
        
        public function chapter(){
            $tablepre = $this->db->db_tablepre;
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $allow_visitor = 1;
            $phpssoid=$this->member_db->get_one(array('userid'=>$_userid),'phpssouid,vip');

            $chapterid=  intval($_GET[id]);
            $chapter=$this->chapter_db->get_one(array('id'=>$chapterid));
            $readpoint=$chapter['readpoint']*100;
            $content='<p>'.nl2br(file_get_contents('./books/'.$chapter['bookid'].'/'.$chapterid.'.txt')).'</p>';
			$content=str_replace('　', '', $content);
            $chapter['content']=  str_replace('<br />', '</p><p>', $content);
            $chapterlist=$this->chapter_db->listinfo(array('bookid'=>$chapter['bookid'],'timing'=>''),'listorder asc', 1, 10000);
            $book=$this->db->get_one(array('bookid'=>$chapter['bookid']),'bookname,authorid,hits,catid,free_etime');

            $upbook['hits']=$book['hits']+1;

            $this->hits($chapter['bookid']);
            $bookid = $this->db->update($upbook,array('bookid'=>$chapter['bookid']));
            
            $prelo=$chapter['listorder']-1;
            $nextlo=$chapter['listorder']+1;
            
            $prePage_id=$this->chapter_db->get_one(array('bookid'=>$chapter['bookid'],'listorder'=>$prelo),'id');

            $prePage=$prePage_id?APP_PATH.'index.php?m=content&c=book&a=chapter&id='.$prePage_id['id']:'';
            $nextPage_id=$this->chapter_db->get_one(array('bookid'=>$chapter['bookid'],'listorder'=>$nextlo),'id');

            $nextPage=$nextPage_id?APP_PATH.'index.php?m=content&c=book&a=chapter&id='.$nextPage_id['id']:'';
$nowtime=time();
            if(($readpoint>0)&&($book['authorid']!=$phpssoid['phpssouid'])&&($phpssoid['vip']!=1)&&($book['free_etime']<$nowtime)) {
                if(!$_userid){
                    $forward=APP_PATH.'index.php?m=content&c=book&a=chapter&id='.$chapterid;
                    showmessage('请先登录',APP_PATH.'index.php?m=member&c=index&a=login',3000);
                }
			$paytype = 0;
			//检查是否支付过
			$allow_visitor = self::_check_payment($chapter['bookid'].'_'.$chapterid,$paytype);
			$is_buy = self::_check_buy($chapter['bookid'],'完本购买消费');
			if(!$allow_visitor&&!$is_buy) {
				$http_referer = urlencode(get_url());
				$allow_visitor = sys_auth($chapter['bookid'].'_'.$chapterid.'|'.$readpoint.'|'.$paytype.'|'.$book['authorid'].'|1').'&http_referer='.$http_referer;
                                $buy_othor=$this->chapter_db->mylistinfo2("id,chapter_number,readpoint",$tablepre.'book_chapter WHERE bookid='.$chapter['bookid'].' and chapter_number>='.$chapter['chapter_number'].' and id  NOT  IN  ( SELECT chapterid FROM '.$tablepre.'author_income where userid='.$_userid.') and readpoint>0',' order by chapter_number asc',1,30,'10');
                                foreach($buy_othor as $key=>$v){
                                    $buy_othor[$key]['areadpoint']=$v['readpoint']*100;
                                }
//                                  print_r($buy_othor);exit;
                                include template('content','book_payment');exit;
			} else {
				$allow_visitor = 1;
			}
		}
            
            if($_userid){
            $read_log=  $this->readlog_db->get_one(array('bookid'=>$chapter['bookid'],'userid'=>$_userid),'id');
            if($read_log){
                $data['chapterid']=$chapterid;
                $data['chapter_number']=$chapter['chapter_number'];
                $data['readtime']=time();
                $readlog = $this->readlog_db->update($data,array('id'=>$read_log['id']));
            }else{
                $data['readtime']=time();
                $data['userid']=$_userid;
                $data['chapter_number']=$chapter['chapter_number'];
                $data['bookid']=$chapter['bookid'];
                $data['chapterid']=$chapterid;
                $readlog=$this->readlog_db->insert($data);
            }
            }
            
            $SEO = seo(1);
            
            include template('content','book_chapter');
        }
        
        
        
        /**
         * 作品库
        **/
        public function library(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $tablepre = $this->db->db_tablepre;
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $where='status>0';
            $cat1=  intval($_GET['cat1'])?intval($_GET['cat1']):79;
            $cat2_arr=$this->type_db->listinfo(array('parentid'=>$cat1),'listorder asc', 1, 10000);
            
            $child_id=  get_child($cat1);
            $child_id=  implode(',', $_SESSION['child']);
            
            unset($_SESSION['child']);
            if($child_id){
                $where2=' and catid in('.$child_id.')';
            }
            
            $cat2=  intval($_GET['cat2'])?intval($_GET['cat2']):'';
            $child_id2=  get_child($cat2);
            $child_id2=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            if($child_id2){
                $where2=' and catid in('.$child_id2.')';
            }
            if($cat2>0){
            $cat3_arr=$this->type_db->listinfo(array('parentid'=>$cat2),'listorder asc', 1, 10000);
            }
            $cat3=  intval($_GET['cat3'])?intval($_GET['cat3']):'';

            if($cat3){
                $where2=' and catid ='.$cat3;
            }
            $words=  intval($_GET['words'])?intval($_GET['words']):0;
			if($words==1){
				$where.=' and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) <= 300000';
			}elseif($words==2){
				$where.=' and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) > 300000 and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) <= 500000';
			}elseif($words==3){
				$where.=' and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) > 500000 and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) <= 1000000';
			}elseif($words==4){
				$where.=' and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) > 1000000 and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) <= 2000000';
			}elseif($words==5){
				$where.=' and (select sum(words) as all_word from '. $tablepre . 'book_chapter as c where chaptertype=1 and c.bookid=b.bookid) > 2000000';
			}
            $finish=  intval($_GET['finish'])?intval($_GET['finish']):0;
            if($finish==1){
                $where.=' and isend=1';
            }elseif($finish==2){
                $where.=' and isend=0';
            }
            $feetype=  intval($_GET['feetype'])?intval($_GET['feetype']):0;
            if($feetype==1){
				$where.=' and charge=0';
			}elseif($feetype==2){
				$where.=' and charge=1';
			}
			
            $sortkey=  intval($_GET['sortkey'])?intval($_GET['sortkey']):1;
			if($sortkey==4){
				$order_by='createtime';
			}elseif($sortkey==2){
				$order_by='hits';
			}elseif($sortkey==3){
				$order_by='recommend';
			}else{
				$order_by='updatetime';
			}
	
            $books=$this->db->mylistinfo2('b.*',$tablepre.'book as b where ' .$where.$where2,' order by '.$order_by.' desc',$page,8,'10');
            foreach ($books as $k => $v) {
                $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                $this->db->query($sql);
                $all_words = $this->db->fetch_array();

                $booklist[$k]=$v;
                $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                if($_userid){
                    $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                }
            }
            $pages = $this->db->pagess;

//            编辑推荐榜
            $bj_rec=$this->db->listinfo(array('status'=>4),'updatetime desc',1,10);
            
            
            //            编辑推荐榜
            $vsql = "select * from " . $tablepre . "book where status>0 and charge=1 order by updatetime desc limit 10";
            $this->db->query($vsql);
            $vip_hot = $this->db->fetch_array();
//            热门推荐榜
            $rsql = "select * from " . $tablepre . "book where status>0 order by recommend desc limit 10";
            $this->db->query($rsql);
            $rec_hot = $this->db->fetch_array();
//            print_r($rec_hot);exit;
//            print_r($bj_rec);exit;
            $catid=5;
            $SEO = seo(1);
            $SEO['title']='作品库-';
            include template('content','library');
        }
        
        //排行榜
        public function ranklist(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $tablepre = $this->db->db_tablepre;
            if($_POST){
                $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
                get_child(56);
                $gtypeid_arr = $_SESSION['child'];
                unset($_SESSION['child']);
                get_child(79);
                $btypeid_arr = $_SESSION['child'];
                unset($_SESSION['child']);
                $gtype_arr = $this->type_db->listinfo(array('parentid'=>56));
                $btype_arr = $this->type_db->listinfo(array('parentid'=>79));
                $alltype_arr = $this->type_db->listinfo(array('parentid'=>0));
                $catid=$_POST['catid'];
//                print_r($btypeid_arr);exit;
                if(in_array($catid, $gtypeid_arr)){
                    $cat_name='女频';
                    $all_id=56;
                    $typeid_arr=$gtype_arr;
                }elseif(($catid==79)||in_array($catid, $btypeid_arr)){
                    $cat_name='男频';
                    $all_id=79;
                    $typeid_arr=$btype_arr;
                }else{
                    $cat_name='女频';
                    $all_id=56;
                    $typeid_arr=$gtype_arr;
                    $catid=56;
                }
                
                $catids = get_child($catid);
                $typeid = implode(',', $_SESSION['child']);
                unset($_SESSION['child']);
            $page=$_POST['page'];
            $type=$_POST['type'];
                if($type==1){
                    if($_POST['period']=='d'){$date='dayviews';}elseif($_POST['period']=='w'){$date='weekviews';}elseif($_POST['period']=='m'){$date='monthviews';
                                }elseif($_POST['period']=='a'){ $date='views';}else{$date='dayviews';}
                    $name='免费点击榜';
                    $books=$this->db->mylistinfo("b.*,h.$date",$tablepre.'book as b inner join '.$tablepre.'hits as h on(b.bookid=h.hitsid) where b.catid in('.$typeid.') and status>0 and charge=0',' order by h.'.$date.' desc',$page,30,'10');
                     foreach ($books as $k => $v) {
                            $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                            $this->db->query($sql);
                            $all_words = $this->db->fetch_array();
                            $booklist[$k]=$v;
                            $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                            $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                            if($_userid){
                                $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                            }
                        }
                    $pages = $this->db->pages_ajx;
                }elseif($type==2){
                    if($_POST['period']=='d'){$date='dayviews';}elseif($_POST['period']=='w'){$date='weekviews';}elseif($_POST['period']=='m'){$date='monthviews';
                                }elseif($_POST['period']=='a'){ $date='views';}else{$date='dayviews';}
                    $name='VIP点击榜';
                    $books=$this->db->mylistinfo("b.*,h.$date",$tablepre.'book as b inner join '.$tablepre.'hits as h on(b.bookid=h.hitsid) where b.catid in('.$typeid.') and status>0 and charge=1',' order by h.'.$date.' desc',$page,30,'10');
                     foreach ($books as $k => $v) {
                            $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                            $this->db->query($sql);
                            $all_words = $this->db->fetch_array();
                            $booklist[$k]=$v;
                            $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                            $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                            if($_userid){
                                $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                            }
                        }
                    $pages = $this->db->pages_ajx;
                }elseif($type==3){
                    if($_POST['period']=='d'){$date='dayfavor';}elseif($_POST['period']=='w'){$date='weekfavor';}elseif($_POST['period']=='m'){$date='monthfavor';
                                }elseif($_POST['period']=='a'){ $date='all';}else{$date='dayfavor';}
                    $name='收藏榜';
                    $books=$this->db->mylistinfo("b.*,f.$date",$tablepre.'book as b inner join '.$tablepre.'favorites as f on(b.bookid=f.favorid) where b.catid in('.$typeid.') and status>0 ',' order by f.'.$date.' desc',$page,30,'10');
                     foreach ($books as $k => $v) {
                            $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                            $this->db->query($sql);
                            $all_words = $this->db->fetch_array();
                            $booklist[$k]=$v;
                            $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                            $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                            if($_userid){
                                $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                            }
                        }
                    $pages = $this->db->pages_ajx;
                }elseif($type==4){
                    if($_POST['period']=='d'){$date='dayrec';}elseif($_POST['period']=='w'){$date='weekrec';}elseif($_POST['period']=='m'){$date='monthrec';
                                }elseif($_POST['period']=='a'){ $date='all';}else{$date='dayfavor';}
                    $name='推荐榜';
                    $books=$this->db->mylistinfo("b.*,r.$date",$tablepre.'book as b inner join '.$tablepre.'recommend as r on(b.bookid=r.recid) where b.catid in('.$typeid.') and status>0 ',' order by r.'.$date.' desc',$page,30,'10');
                     foreach ($books as $k => $v) {
                            $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                            $this->db->query($sql);
                            $all_words = $this->db->fetch_array();
                            $booklist[$k]=$v;
                            $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                            $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                            if($_userid){
                                $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                            }
                        }
                    $pages = $this->db->pages_ajx;
                }elseif($type==5){
                    $date='mon_tick';
                    $books=$this->db->mylistinfo("*",$tablepre.'book where catid in('.$typeid.') and status>0 ',' order by mon_tick desc',$page,30,'10');
                     foreach ($books as $k => $v) {
                            $sql = "select sum(words) as all_word from " . $tablepre . "book_chapter where chaptertype=1 and bookid=$v[bookid]";
                            $this->db->query($sql);
                            $all_words = $this->db->fetch_array();
                            $booklist[$k]=$v;
                            $booklist[$k]['all_word'] = $all_words[0]['all_word'];
                            $booklist[$k]['fist_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'chapter_number'=>1),'id');
                            if($_userid){
                                $booklist[$k]['isfav']=$this->fav_db->count('bookid='.$v['bookid'].' and userid='.$_userid);
                            }
                        }
                    $pages = $this->db->pages_ajx;
                }
                
                print_r($this->show_ranklist($booklist,$pages,$cat_name,$_POST['catid'],$name,$_POST['period'],$type,$typeid_arr,$all_id,$date));exit;
            }
            
            
            
            $np_type=  get_child(56);
            $woman_typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            
            

            $gvip_all_hits=  $this->get_type_hits2(56,'views',10,1);//            女频VIP点击榜（总）
            
            $gvip_mon_hits=  $this->get_type_hits2(56,'monthviews',10,1);//            女频VIP点击榜（月）

            $gvip_week_hits=  $this->get_type_hits2(56,'weekviews',10,1);//            女频VIP点击榜（周）
            $bvip_all_hits=  $this->get_type_hits2(79,'views',10,1);//            男频VIP点击榜（总）
            $bvip_mon_hits=  $this->get_type_hits2(79,'monthviews',10,1);//            男频VIP点击榜（月）
            $bvip_week_hits=  $this->get_type_hits2(79,'weekviews',10,1);//            男频VIP点击榜（周）
            $gfree_all_hits=  $this->get_type_hits2(56,'views',10,0);//            女频免费点击榜（总）
            $gfree_mon_hits=  $this->get_type_hits2(56,'monthviews',10,0);//            女频免费点击榜（月）
            $gfree_week_hits=  $this->get_type_hits2(56,'weekviews',10,0);//            女频免费点击榜（周）
            $bfree_all_hits=  $this->get_type_hits2(79,'views',10,0);//            男频免费点击榜（总）
            $bfree_mon_hits=  $this->get_type_hits2(79,'monthviews',10,0);//            男频免费点击榜（月）
            $bfree_week_hits=  $this->get_type_hits2(79,'weekviews',10,0);//            男频免费点击榜（周）
            $girl_pk_all=  $this->get_recommend(56,'all',10);//女频PK榜（总）
            $girl_pk_mon=  $this->get_recommend(56,'monthrec',10);//女频PK榜（月）
            $girl_pk_week=  $this->get_recommend(56,'weekrec',10);//女频PK榜（周）
            $boy_pk_all=  $this->get_recommend(79,'all',10);//男频PK榜（总）
            $boy_pk_mon=  $this->get_recommend(79,'monthrec',10);//男频PK榜（月）
            $boy_pk_week=  $this->get_recommend(79,'weekrec',10);//男频PK榜（周）
            
            $girl_favor_all=  $this->get_favorites(56,'all',10);//女频PK榜（总）
            $girl_favor_mon=  $this->get_favorites(56,'monthfavor',10);//女频PK榜（月）
            $girl_favor_week=  $this->get_favorites(56,'weekfavor',10);//女频PK榜（周）
  
            $boy_favor_all=  $this->get_favorites(79,'all',10);//男频PK榜（总）
            $boy_favor_mon=  $this->get_favorites(79,'monthfavor',10);//男频PK榜（月）
            $boy_favor_week=  $this->get_favorites(79,'weekfavor',10);//男频PK榜（周）
            
            $sql = "select * from " . $tablepre . "book where status>0 order by mon_tick desc limit 10";
            $this->db->query($sql);
            $mon_tick = $this->db->fetch_array();
            
//            print_r($gvip_hits);exit;
            
            $catid=6;
            $SEO = seo(1);
            $SEO['title']='排行榜-';
            include template('content','ranklist');
        }
        
        public function girl(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $tablepre = $this->db->db_tablepre;
            $np_type=  get_child(56);
            $typearr=  $_SESSION['child'];
            unset($_SESSION['child']);
            $woman_typeid=  implode(',', $typearr);
            
            foreach($typearr as $k=>$v){
                $ischild=  $this->type_db->get_one(array('parentid'=>$v),'name');
                if(!$ischild){
                    $hot_type[]=$this->type_db->get_one(array('typeid'=>$v),'typeid,name,listorder');;
                }
            }
            $hot_type=  array_sort($hot_type,'listorder');
            
            
//            编辑推荐榜
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status=4 order by updatetime desc limit 12";
            $this->db->query($sql);
            $bj_rec = $this->db->fetch_array();
//          典藏全本作品
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status=11 and isend=1 order by updatetime desc limit 7";
            $this->db->query($sql);
            $end_fav = $this->db->fetch_array();
//          频道推荐
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status=6 order by updatetime desc limit 11";
            $this->db->query($sql);
            $channel_rec = $this->db->fetch_array();
            //          精品推荐
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status=7 order by updatetime desc limit 12";
            $this->db->query($sql);
            $good_rec = $this->db->fetch_array();
            
//          古代言情点击榜
            $gdyq=  get_child(57);
            $gdyq_typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,h.views from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($gdyq_typeid) and status>0 order by b.hits desc limit 9";
            $this->db->query($sql);
            $gdyq_hits = $this->db->fetch_array();
            //          现代都市点击榜
            $xdds=  get_child(58);
            $xdds_typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,h.views from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($xdds_typeid) and status>0 order by b.hits desc limit 9";
            $this->db->query($sql);
            $xdds_hits = $this->db->fetch_array();
//            最新更新作品
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status>0 order by updatetime desc limit 32";
            $this->db->query($sql);
            $news = $this->db->fetch_array();
            foreach($news as $k=>$v){
                $news[$k]['last_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'timing'=>''),'id,title','chapter_number desc');
            }
//            print_r($news);exit;
//            名家新作
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status=8 order by updatetime desc limit 11";
            $this->db->query($sql);
            $new_rec = $this->db->fetch_array();
//            点击榜（周）
            $sql = "select b.*,h.weekviews from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($woman_typeid) and status>0 order by weekviews desc limit 9";
            $this->db->query($sql);
            $week_hits = $this->db->fetch_array();
            //            点击榜（日）
            $sql = "select b.*,h.dayviews from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($woman_typeid) and status>0 order by dayviews desc limit 9";
            $this->db->query($sql);
            $day_hits = $this->db->fetch_array();
            //            点击榜（月）
            $sql = "select b.*,h.monthviews from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($woman_typeid) and status>0 order by monthviews desc limit 9";
            $this->db->query($sql);
            $mon_hits = $this->db->fetch_array();
            
//            最新上架
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status>0 and charge=1 order by createtime desc limit 9";
            $this->db->query($sql);
            $new_create = $this->db->fetch_array();
			//            最新签约 	signed
            $sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status>0 and signed=2 order by createtime desc limit 9";
            $this->db->query($sql);
            $new_signed = $this->db->fetch_array();
//            月票榜
			$sql = "select * from " . $tablepre . "book where catid in($woman_typeid) and status>0 order by mon_tick desc limit 9";
            $this->db->query($sql);
            $mon_tick = $this->db->fetch_array();
            
//            print_r($new_rec);exit;
            $catid=1;
            $SEO = seo(1);
            $SEO['title']='原创女频-';
            include template('content','woman');
        }
        public function boy(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $tablepre = $this->db->db_tablepre;
            $np_type=  get_child(79);
            $typearr=  $_SESSION['child'];
            unset($_SESSION['child']);
            $man_typeid=  implode(',', $typearr);
            //          频道推荐
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=6 order by updatetime desc limit 1,7";
            $this->db->query($sql);
            $channel_rec1 = $this->db->fetch_array();
             $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=6 order by updatetime desc limit 8,14";
            $this->db->query($sql);
            $channel_rec2 = $this->db->fetch_array();
             $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=6 order by updatetime desc limit 15,21";
            $this->db->query($sql);
            $channel_rec3 = $this->db->fetch_array();

            //            本周强推
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=10 order by hits desc limit 8";
            $this->db->query($sql);
            $bj_rec = $this->db->fetch_array();
             //          精品推荐
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=9 order by updatetime desc limit 16";
            $this->db->query($sql);
            $good_rec = $this->db->fetch_array();
            //          免费精品
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status=7 and charge=0 order by updatetime desc limit 12";
            $this->db->query($sql);
            $good_ree = $this->db->fetch_array();
            
            //          玄幻奇幻点击榜
            $xhqh_hits=  $this->get_type_hits(59,'views',6);
//            武侠仙侠
            $wxxx_hits=  $this->get_type_hits(60,'views',6);
//            现代都市
            $xdds_hits=  $this->get_type_hits(86,'views',6);
//            历史军事
            $lsjs_hits=  $this->get_type_hits(100,'views',6);
            //            游戏竞技
            $yxjj_hits=  $this->get_type_hits(105,'views',6);
            //            悬疑惊悚
            $xyjs_hits=  $this->get_type_hits(121,'views',6);
            
            //            最新更新作品
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status>0 order by updatetime desc limit 23";
            $this->db->query($sql);
            $news = $this->db->fetch_array();
            foreach($news as $k=>$v){
                $news[$k]['last_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'timing'=>''),'id,title','chapter_number desc');
            }
            
            //            免费人气榜
            $sql = "select b.*,h.views from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($man_typeid) and status>0 and charge=0 order by b.hits desc limit 8";
            $this->db->query($sql);
            $free_hits = $this->db->fetch_array();
            
            //            男生人气榜
            $sql = "select b.*,h.views from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($man_typeid) and status>0 and charge=1 order by b.hits desc limit 8";
            $this->db->query($sql);
            $charge_hits = $this->db->fetch_array();
            
            //            最新上架
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status>0 and charge=1 order by createtime desc limit 8";
            $this->db->query($sql);
            $new_create = $this->db->fetch_array();
            //            签约新人新作
            
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status>0 and charge=1 order by hits desc limit 8";
            $this->db->query($sql);
            $new_sing = $this->db->fetch_array();
            
            //          典藏全本作品
            $sql = "select * from " . $tablepre . "book where catid in($man_typeid) and status>0 and isend=1 order by hits desc limit 8";
            $this->db->query($sql);
            $end_fav = $this->db->fetch_array();
//            print_r($channel_rec1);exit;
            
            
            
            $SEO = seo(1);
            $SEO['title']='原创男频-';
            $catid=2;
            include template('content','man');
        }
        public function vip(){
            $_userid = $this->_userid;
            $_username = $this->_username;
            $_groupid = $this->_groupid;
            $tablepre = $this->db->db_tablepre;
            //新书推荐
            $sql = "select * from " . $tablepre . "book where charge=1 and status=8 order by updatetime desc limit 6";
            $this->db->query($sql);
            $new_rec = $this->db->fetch_array();
            
            //精品推荐
            $sql = "select * from " . $tablepre . "book where charge=1 and status=7 order by updatetime desc limit 10";
            $this->db->query($sql);
            $good_rec = $this->db->fetch_array();
            
            //            最新更新作品
            $sql = "select * from " . $tablepre . "book where charge=1 and status>0 order by updatetime desc limit 22";
            $this->db->query($sql);
            $news = $this->db->fetch_array();
            foreach($news as $k=>$v){
                $news[$k]['last_chapter']=$this->chapter_db->get_one(array('bookid'=>$v['bookid'],'timing'=>''),'id,title','chapter_number desc');
            }
            
            //点击榜
            $sql = "select * from " . $tablepre . "book where charge=1 order by hits desc limit 8";
            $this->db->query($sql);
            $hits = $this->db->fetch_array();
            //推荐榜
            $sql = "select * from " . $tablepre . "book where charge=1 order by recommend desc limit 8";
            $this->db->query($sql);
            $recommends = $this->db->fetch_array();
;

            //日销售榜
            $day_str=  strtotime(date('Y-m-d',time()));
            $week_str=  $day_str-604800;
            $mon_str=$day_str-2592000;
            
            //            $sql = "select b.*,count(c.id) as total from " . $tablepre . "book as b inner join " . $tablepre . "author_income as c on(b.bookid=c.bookid) where b.charge=1 order by total desc limit 10";

            $sql = "select id,bookid,count(id) as atotal,sum(all_val) as all_point from " . $tablepre . "author_income where bookid>0 and buy_type<10 GROUP BY bookid order by all_point desc limit 10";
            $this->db->query($sql);
            $all_sell = $this->db->fetch_array();

                foreach ($all_sell as $key=>$v){
                    $sql = "select bookname from " . $tablepre . "book where bookid=$v[bookid]";
                    $this->db->query($sql);
                    $bookinfo=$this->db->fetch_array();

                    $all_sell[$key]['bookname'] = $bookinfo[0]['bookname'];
                }
        
               $sql = "select id,bookid,count(id) as atotal,sum(all_val) as all_point from " . $tablepre . "author_income where bookid>0 and buy_type<10 and creat_at>$week_str GROUP BY bookid order by all_point desc limit 10";
            $this->db->query($sql);
            $week_sell = $this->db->fetch_array();

                foreach ($week_sell as $key=>$v){
                    $sql = "select bookname from " . $tablepre . "book where bookid=$v[bookid]";
                    $this->db->query($sql);
                    $bookinfo=$this->db->fetch_array();

                    $week_sell[$key]['bookname'] = $bookinfo[0]['bookname'];
                }
                $sql = "select id,bookid,count(id) as atotal,sum(all_val) as all_point from " . $tablepre . "author_income where bookid>0 and buy_type<10 and creat_at>$mon_str GROUP BY bookid order by all_point desc limit 10";
            $this->db->query($sql);
            $mon_sell = $this->db->fetch_array();

                foreach ($mon_sell as $key=>$v){
                    $sql = "select bookname from " . $tablepre . "book where bookid=$v[bookid]";
                    $this->db->query($sql);
                    $bookinfo=$this->db->fetch_array();

                    $mon_sell[$key]['bookname'] = $bookinfo[0]['bookname'];
                }
				if(empty($day_sell)){
					$day_sell=$week_sell;
				}
//            print_R($mon_sell);exit;
                $catid=7;
            $SEO = seo(1);
            $SEO['title']='VIP专区-';
            include template('content','vip');
        }
        
        
        
        function hits($hitsid) {
        $r = $this->hits_db->get_one(array('hitsid' => $hitsid));
        if (!$r){
            $data['hitsid']=$hitsid;
            $data['views']=1;
            $data['yesterdayviews']=1;
            $data['dayviews']=1;
            $data['weekviews']=1;
            $data['monthviews']=1;
            $data['updatetime']=SYS_TIME;
            return $this->hits_db->insert($data);
        }else{
        $views = $r['views'] + 1;
        $yesterdayviews = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayviews'] : $r['yesterdayviews'];
        $dayviews = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayviews'] + 1) : 1;
        $weekviews = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekviews'] + 1) : 1;
        $monthviews = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthviews'] + 1) : 1;
        $sql = array('views' => $views, 'yesterdayviews' => $yesterdayviews, 'dayviews' => $dayviews, 'weekviews' => $weekviews, 'monthviews' => $monthviews, 'updatetime' => SYS_TIME);
        return $this->hits_db->update($sql, array('hitsid' => $hitsid));
        }
    }
    
    public function add_favor(){
//        $this->favor_db

            $_userid = $this->_userid;
           if($_userid){
            $bookid=$_POST['bookid'];
            $isfav=$this->fav_db->get_one(array('userid' => $_userid,'bookid'=>$bookid));
            if($isfav){
                echo 9;exit;
            }else{
            $r = $this->favor_db->get_one(array('favorid' => $bookid));
            if (!$r) {
                $datas['favorid'] = $bookid;
                $datas['all'] = 1;
                $datas['yesterdayfavor'] = 1;
                $datas['dayfavor'] = 1;
                $datas['weekfavor'] = 1;
                $datas['monthfavor'] = 1;
                $datas['updatetime'] = SYS_TIME;
                $this->favor_db->insert($datas);
            } else {
                $all = $r['all'] + 1;
                $yesterdayfavor = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayfavor'] : $r['yesterdayfavor'];
                $dayfavor = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayfavor'] + 1) : 1;
                $weekfavor = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekfavor'] + 1) : 1;
                $monthfavor = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthfavor'] + 1) : 1;
                $sql = array('all' => $all, 'yesterdayfavor' => $yesterdayfavor, 'dayfavor' => $dayfavor, 'weekfavor' => $weekfavor, 'monthfavor' => $monthfavor, 'updatetime' => SYS_TIME);
                $this->favor_db->update($sql, array('favorid' => $bookid));
            }


            $data['userid']=$_userid;
            $data['bookid']=$bookid;
            $data['adddate']=time();
            $add=$this->fav_db->insert($data);
            if($add){
                echo 1;exit;
            }else{
                echo 2;exit;
            }
            }
           }else{
               echo 0;exit;
           }
           
        }
        
        public function recommend(){
            //                $this->recommend_db
            
            $_userid = $this->_userid;
            if($_userid){
            $bookid = $_POST['bookid'];
            $r = $this->recommend_db->get_one(array('recid' => $bookid));
            if (!$r) {
                $datas['recid'] = $bookid;
                $datas['all'] = 1;
                $datas['yesterdayrec'] = 1;
                $datas['dayrec'] = 1;
                $datas['weekrec'] = 1;
                $datas['monthrec'] = 1;
                $datas['updatetime'] = SYS_TIME;
                $this->recommend_db->insert($datas);
            } else {
                $all = $r['all'] + 1;
                $yesterdayrec = (date('Ymd', $r['updatetime']) == date('Ymd', strtotime('-1 day'))) ? $r['dayrec'] : $r['yesterdayrec'];
                $dayrec = (date('Ymd', $r['updatetime']) == date('Ymd', SYS_TIME)) ? ($r['dayrec'] + 1) : 1;
                $weekrec = (date('YW', $r['updatetime']) == date('YW', SYS_TIME)) ? ($r['weekrec'] + 1) : 1;
                $monthrec = (date('Ym', $r['updatetime']) == date('Ym', SYS_TIME)) ? ($r['monthrec'] + 1) : 1;
                $sql = array('all' => $all, 'yesterdayrec' => $yesterdayrec, 'dayrec' => $dayrec, 'weekrec' => $weekrec, 'monthrec' => $monthrec, 'updatetime' => SYS_TIME);
                $this->recommend_db->update($sql, array('recid' => $bookid));
            }
            $user_vote=  $this->member_db->get_one(array('userid'=>$_userid),'recommend_votes,vote_time');
            if(date('Y-m-d',$user_vote['vote_time'])<date('Y-m-d',time())){
                $data['recommend_votes']=3;
                $data['vote_time']=time();
                $this->member_db->update($data,array('userid'=>$_userid));
                unset($data);
            }
            $votes=$this->member_db->get_one(array('userid'=>$_userid),'recommend_votes');
            $book_rec=$this->db->get_one(array('bookid'=>$bookid),'recommend');
            if ($votes['recommend_votes'] > 0) {
                $data['recommend'] =$book_rec['recommend']+1;
                $data2['recommend_votes'] = $votes['recommend_votes']-1;
                $this->db->update($data, array('bookid' => $bookid));
                $this->member_db->update($data2, array('userid' => $_userid));
                echo $votes['recommend_votes']-1;
            } else{
                echo 9;
            }
            
           }else{
               echo 99;
           }exit;
        }
    
    function get_type_hits($catid,$data,$limit){
            $tablepre = $this->db->db_tablepre;
            $catids=  get_child($catid);
            $typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,h.$data from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($typeid) and status>0 order by h.$data desc limit $limit";
            $this->db->query($sql);
            return $this->db->fetch_array();
    }
    function get_type_hits2($catid,$data,$limit,$charge=0){
            $tablepre = $this->db->db_tablepre;
            $catids=  get_child($catid);
            $typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,h.$data from " . $tablepre . "book as b inner join " . $tablepre . "hits as h on(b.bookid=h.hitsid) where b.catid in($typeid) and status>0 and charge=$charge order by h.$data desc limit $limit";
            $this->db->query($sql);
            return $this->db->fetch_array();
    }
    function get_recommend($catid,$data,$limit){
            $tablepre = $this->db->db_tablepre;
            $catids=  get_child($catid);
            $typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,r.$data from " . $tablepre . "book as b inner join " . $tablepre . "recommend as r on(b.bookid=r.recid) where b.catid in($typeid) and status>0 order by r.$data desc limit $limit";
            $this->db->query($sql);
            return $this->db->fetch_array();
    }
    
    function get_favorites($catid,$data,$limit){
            $tablepre = $this->db->db_tablepre;
            $catids=  get_child($catid);
            $typeid=  implode(',', $_SESSION['child']);
            unset($_SESSION['child']);
            $sql = "select b.*,f.$data from " . $tablepre . "book as b inner join " . $tablepre . "favorites as f on(b.bookid=f.favorid) where b.catid in($typeid) and status>0 order by f.$data desc limit $limit";
            $this->db->query($sql);
            return $this->db->fetch_array();
    }
    
    function show_ranklist($books,$pages,$cat_name,$catid,$name,$vtime,$type,$typeid_arr,$all_id,$data){

        include template('content','show_ranklist');
    }
    
    
    /**
	 * 检查支付状态
	 */
	protected function _check_payment($flag,$paytype) {
		$_userid = $this->_userid;
		$_username = $this->_username;
		if(!$_userid) return false;
		pc_base::load_app_class('spend','pay',0);

		$r = spend::spend_time2($_userid,$flag);
		if($r['id']) {return true;
                }else{
		return false;
                }
	}
        
        protected function _check_buy($flag,$msg){
            $_userid = $this->_userid;
            $_username = $this->_username;
            if (!$_userid)
                return false;
            pc_base::load_app_class('spend', 'pay', 0);

            $r = spend::spend_buy($_userid, $flag,$msg);
            if ($r['id']) {
                return true;
            } else {
                return false;
            }
        }
                
        
        function inquiry(){
            $tablepre = $this->db->db_tablepre;
            $chapterid=rtrim($_POST['chapterid'], ',');
            if($chapterid){
            $sql="select sum(readpoint) as areadpoint from ".$tablepre."book_chapter where id in($chapterid)";
            $this->db->query($sql);
            $readpoint=$this->db->fetch_array();
            echo $readpoint[0]['areadpoint']*100;exit;
            }else{
                echo 0;exit;
            }
        }
        
        function array_sort($arr, $keys, $type = 'asc') {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }
    
    function check_price($words){
        if($words<399999){
            $book_price=600;
            $down_price=400;
        }elseif($words>400000&&$words<599999){
            $book_price=800;
            $down_price=500;
        }elseif($words>600000&&$words<799999){
            $book_price=1000;
            $down_price=600;
        }elseif($words>800000&&$words<999999){
            $book_price=1200;
            $down_price=700;
        }elseif($words>1000000&&$words<1199999){
            $book_price=1400;
            $down_price=800;
        }elseif($words>1200000){
            $book_price=1600;
            $down_price=1300;
        }
        $price['all']=$book_price;
        $price['down']=$down_price;
        return $price;
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
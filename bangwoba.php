<?php

defined('IN_plcms') or exit('No permission resources.');

//模型缓存路径

define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);

pc_base::load_app_func('util','content');

class bangwoba {

	private $db;

	function __construct() {

      
		$this->db = pc_base::load_model('books_model');

        $this->content_db = pc_base::load_model('content_model');

        $this->chapter_db = pc_base::load_model('book_chapter_model');

        $this->volume_db = pc_base::load_model('book_volume_model');

        $this->_userid = param::get_cookie('_userid');

		$this->_username = param::get_cookie('_username');

		$this->_groupid = param::get_cookie('_groupid');

        $this->type_db = pc_base::load_model('type_model');

        $this->member_db = pc_base::load_model('member_model');
        $this->bangwoba_db = pc_base::load_model('bangwoba_model');
        

        $this->fav_db=  pc_base::load_model('favorite_model');

        $this->readlog_db=  pc_base::load_model('read_log_model');

        $this->hits_db = pc_base::load_model('hits_model');

        $this->favor_db = pc_base::load_model('favor_model');

        $this->recommend_db = pc_base::load_model('recommend_model');

	}





    //1. 全局资源分类接口 
//localhost/php/yyw998_online_svn/index.html?m=export&c=bangwoba&a=add_point&partnerid=784&vmoney=5&tid=5
    public function add_point(){
       
        $partnerid =  $_GET["partnerid"];
        $vmoney =  $_GET["vmoney"];
        $tid =  $_GET["tid"];
        $nonceStr =  $_GET["nonceStr"];

        $retArr['partnerid'] =  $partnerid;
        $retArr['vmoney'] =  $vmoney;
        $retArr['tid'] =  $tid;
        // $safecode = $_SERVER['HTTP_HOST'];
$safecode = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
       
        $nonceStr_own = md5($safecode.$partnerid.$vmoney.$tid);
        if($nonceStr_own !=  $nonceStr)
        {
            $retArr['status'] =  "no";
            $retArr['errno'] =  "1002";
            // return {"partnerid":$partnerid,"vmoney":$vmoney,"tid":$tid,"status":"no","errno":"1002"};
            echo json_encode($retArr);
        }
        
        if($vmoney > 60000)
        {
            showmessage('充值的怡阅币太多',APP_PATH);
            $retArr['status'] =  "no";
            $retArr['errno'] =  "1004";
            // return {"partnerid":$partnerid,"vmoney":$vmoney,"tid":$tid,"status":"no","errno":"1004"};
            echo json_encode($retArr);
        }

        $member_info = $this->member_db->get_one(array('userid'=>$partnerid));
        if(!$member_info)
        {
            showmessage('错误的用户名',APP_PATH);
            // return {"partnerid":$partnerid,"vmoney":$vmoney,"tid":$tid,"status":"no","errno":"1005"};
            $retArr['status'] =  "no";
            $retArr['errno'] =  "1005";
            echo json_encode($retArr);
        }

        $point = $member_info['point'] + $vmoney;
        $upmember['point']=$point;
       
        $this->member_db->update($upmember, array('userid'=>$partnerid));
       
        $newmember['point'] = $vmoney;
        $newmember['userid'] = $partnerid;
        $newmember['tid'] = $tid;

        $showtime=date("YmdHis");

        $newmember['time'] = $showtime;
       
        $this->bangwoba_db->insert($newmember,true);
        

        $retArr['status'] =  "ok";
        $retArr['sn'] =  $showtime;
        echo json_encode($retArr);
        // return {"partnerid":$partnerid,"vmoney":$vmoney,"tid":$tid,"status":"ok","sn":$showtime};
        
    }


}

?>
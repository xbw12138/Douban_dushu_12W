<?php
/**
 * Created by PhpStorm.
 * User: xubowen
 * Date: 2017/5/7
 * Time: 下午1:37
 */
require 'api.php';
//提取url
/*$arrayTag=getTag();
for($i=0;$i<sizeof($arrayTag);$i++){
    $tag=$arrayTag[$i];
    for($page=0;$page<50;$page++){
        $content=getUrl($tag,$page);
        $arrayurl=analyseUrl($content);
        insertUrl($arrayurl,$tag);
    }
}*/
//$url="https://book.douban.com/subject/26896858/";
//$content=getWeb($url);
//var_dump(analyseWeb($content));

//$content=getUrl("小说",0);
//$arrayurl=analyseUrl($content);
//var_dump($arrayurl);
//插入图书数据
/*$arrayTag=getTag();
for($i=0;$i<sizeof($arrayTag);$i++){
    $tag=$arrayTag[$i];
    for($page=0;$page<50;$page++){
        $content=getUrl($tag,$page);
        $arrayurl=analyseUrl($content);
        insertUrl($arrayurl,$tag);
    }
}*/

//$content=getWeb("https://book.douban.com/subject/26957760/");
//var_dump(analyseWeb2($content));

//插入ISBN
selectUrl();
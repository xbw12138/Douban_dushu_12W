<?php
/**
 * Created by PhpStorm.
 * User: xubowen
 * Date: 2017/5/7
 * Time: 下午1:39
 */
header('Content-type: application/json; charset=UTF-8');
require 'config.php';
$conn=mysqli_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ;
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_database);
function getUrl($tag,$page){
    $index=$page*20;
    $url='https://book.douban.com/tag/'.$tag.'?start='.$index.'&type=T';
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    curl_close($curl);
    return $api;
}
function getWeb($url){
    $curl=curl_init($url);
    curl_setopt($curl,CURLOPT_HEADER, 0);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, false);
    $api=curl_exec($curl);
    curl_close($curl);
    return $api;
}
function getTag(){
    $myfile = fopen("tag.txt", "r") or die("Unable to open file!");
    $str=fread($myfile,filesize("tag.txt"));
    fclose($myfile);
    $isMatched = preg_match_all('/<td><a href="(?<grp0>[\s\S]*?)">(?<grp1>[\s\S]*?)<\/a><b>(?<grp2>[\s\S]*?)<\/b><\/td>/', $str, $matches);
    if($isMatched!=0) {
        for ($i = 0; $i < $isMatched; $i++) {
            $array[]=$matches[2][$i];
        }
    }
    return $array;
}
function analyseUrl($content){//<a class="nbg" href="https://book.douban.com/subject/4233385/"
    $regular='/<a class="nbg" href="(?<grp0>[\s\S]*?)"/';
    $regular2='/<div class="pub">\s*(?<grp0>[\s\S]*?)\s*<\/div>/';
    $regular3='/<img class="" src="(?<grp0>[\s\S]*?)"\s*width="90">/';
    $regular4='/<a href="(?<grp0>[\s\S]*?)" title="(?<grp1>[\s\S]*?)"/';
    $regular5='/<span class="pl">\s*(?<grp0>[\s\S]*?)\s*<\/span>\s*<\/div>\s*<p>(?<grp1>[\s\S]*?)<\/p>/';
    
    
    $isMatched = preg_match_all($regular, $content, $matches);
    $isMatched2 = preg_match_all($regular2, $content, $matches2);
    $isMatched3 = preg_match_all($regular3, $content, $matches3);
    $isMatched4 = preg_match_all($regular4, $content, $matches4);
    $isMatched5 = preg_match_all($regular5, $content, $matches5);
    if($isMatched!=0&&$isMatched2!=0&&$isMatched3!=0&&$isMatched4!=0&&$isMatched5!=0) {
        for ($i = 0; $i < $isMatched; $i++) {
            $array[$i][]=$matches4[2][$i];
            $array[$i][]=$matches[1][$i];
            $array[$i][]=$matches3[1][$i];
            $array[$i][]=$matches2[1][$i];
            $array[$i][]=$matches5[2][$i];

            //$content=getWeb($matches[1][$i]);
            //$array[$i][]=analyseWeb2($content);
        }
    }
    return $array;
}
function insertUrl($arrayurl,$tag){
    for($i=0;$i<sizeof($arrayurl);$i++){
        global $conn;
        $title=$arrayurl[$i][0];
        $url=$arrayurl[$i][1];
        $pic=$arrayurl[$i][2];
        $det=$arrayurl[$i][3];
        $info=$arrayurl[$i][4];
        //$isbn=$arrayurl[$i][5];
        //$sql = "INSERT INTO book(title,url,pic,det,info,tag,isbn) VALUES('$title','$url','$pic','$det','$info','$tag','$isbn')";
        $sql = "INSERT INTO book(title,url,pic,det,info,tag) VALUES('$title','$url','$pic','$det','$info','$tag')";
        mysqli_query($conn,$sql);
    }
}
function insertISBN($url,$isbn){
        global $conn;
        $sql = "UPDATE book set isbn='$isbn' where url='$url'";
        mysqli_query($conn,$sql);
}
function selectUrl(){
    global $conn;
    $sql="select url from book";
    if($result=mysqli_query($conn,$sql)) {
        while ($row = mysqli_fetch_array($result)) {
            $content=getWeb($row[0]);
            $isbn=analyseWeb2($content);
            insertISBN($row[0],$isbn);
        }
    }
}

function analyseWeb2($content){
    $regular='/<span class="pl">ISBN:<\/span>(?<grp0>[\s\S]*?)<br\/>/';
    $isMatched = preg_match_all($regular, $content, $matches);
    if($isMatched!=0) {
        for ($i = 0; $i < $isMatched; $i++) {
            $isbn=$matches[1][$i];
        }
    }else{
        $isbn="";
    }
    return $isbn;
}
function analyseWeb($content){
    $regular='/<a class="nbg"
      href="(?<grp0>[\s\S]*?)" title="(?<grp1>[\s\S]*?)">/';
    $regular2='/<span class="pl">(?<grp0>[\s\S]*?)<\/span> (?<grp1>[\s\S]*?)<br\/>/';
    $regular3='/<div class="intro">\s*<p>(?<grp0>[\s\S]*?)<\/p><\/div>\s*<\/div>/';
    $regular4='/<span class="pl">(?<grp0>[\s\S]*?)<\/span>&nbsp;
        <a href="(?<grp1>[\s\S]*?)">/';
    $isMatched = preg_match_all($regular, $content, $matches);
    $isMatched2 = preg_match_all($regular2, $content, $matches2);
    $isMatched3 = preg_match_all($regular3, $content, $matches3);
    $isMatched4 = preg_match_all($regular4, $content, $matches4);
    if($isMatched!=0) {
        for ($i = 0; $i < $isMatched; $i++) {
            $array[]=$matches[1][$i];
            $array[]=$matches[2][$i];
        }
    }
    if($isMatched2!=0) {
        for ($i = 0; $i < $isMatched2; $i++) {
            $array[]=$matches2[2][$i];
        }
    }
    if($isMatched3!=0) {
        for ($i = 0; $i < $isMatched3; $i++) {
            $array[]=$matches3[1][$i];
        }
    }
    if($isMatched4!=0) {
        for ($i = 0; $i < $isMatched4; $i++) {
            $array[]=$matches4[2][$i];
        }
    }
    $arrays[]=$array;
    return $arrays;
}
<?php 
	include_once("xmlrpc/xmlrpc.inc");
    include_once("xmlrpc/xmlrpcs.inc");
    include_once("txtdb/txt-db-api.php");
    include_once('util2.php');
    

    //param0:string appkey
    //param1:string username
    //param2:string password
    function getUsersBlogs($msg){
        //ignore input , output what you want directly
        //$appKey = $msg->getParam(0)->scalarval();
        $arr = array(
            //struct
            new xmlrpcval(
                array(
                'blogid' => new xmlrpcval('67322')
                , 'url' => new xmlrpcval(BLOGURL)
                , 'blogName' => new xmlrpcval('ghpage')
            ),'struct')
        );

    	return new xmlrpcresp(new xmlrpcval($arr,'array'));
    }

    //param0:string blogid
    //param1:string username
    //param2:string password 
    //param3:struct post
        //string title
        //string description
        //array categories
    //param4:bool publish

    //response:
    //string id
    function newPost($msg){

        $db = new Database('blog');
        $title = Escape($msg->getParam(3)->structMem('title')->scalarVal());
        $content = Escape($msg->getParam(3)->structMem('description')->scalarVal());
        //$content2 = Escape($msg->getParam(3)->structMem('description')->scalarVal());
        $createdate = date("Y-m-d H:i:s");
        $moddate = date("Y-m-d H:i:s");

        //filename format is yyyy-mm-dd-XXXXXX.html
        $filename = date('Y-m-d-') . uniqid() . '.html';
        //check git local path config and save the file into it
        $rs = $db->executeQuery("select count(*) as count from localgit");
        $rs->next();
        list($count)=$rs->getCurrentValues();
        if($count > 0){
            $rs = $db->executeQuery("select localpath,layout from localgit");
            $rs->next();
            list($localpath,$layout)=$rs->getCurrentValues();
            if(!is_dir($localpath)){
                $err = "$localpath is not a valid path.";
            }
            else{
                $sql = "insert into blogs (title,filename,content,layout,createdate,moddate) values('$title','$filename','$content','$layout','$createdate','$moddate')";
                $db->executeQuery($sql);
            }
        }
        
        
        $err = txtdbapi_get_last_error();
        if($err == null){
            $id = $db->getLastInsertId();
            return new xmlrpcresp(new xmlrpcval($id,'string'));
        }
        else{
            return new xmlrpcresp(0,-1,$err);
        }
            
    }


    //param0:string postid
    //param1:string username
    //param2:string password

    //response:
    //struct post
        //string title
        //string description
        //array categories

    function getPost($msg){
        $db = new Database('blog');
        //get file name from database
        $id = $msg->getParam(0)->scalarval();
        $rs = $db->executeQuery("select title,content from blogs where id=$id");
        $rs->next();
        list($title,$content)=$rs->getCurrentValues();
        if($title == null){
            $err = "postid : $id not found on server.";
        }
        else{
            return new xmlrpcresp(
                        new xmlrpcval(array(
                                'title' => new xmlrpcval($title,'string')
                                ,'description' => new xmlrpcval($content,'string')
                                ,'categories' => new xmlrpcval(array(),'array')
                            ),
                            'struct')
                    );
        }

        
        if($err != null){
            return new xmlrpcresp(0,-1,$err);
        }
    }

    //param0:string postid
    //param1:string username
    //param2:string password 
    //param3:struct post
        //string title
        //string description
        //array categories
    //param4:bool publish

    //response:
    //bool result
    function editPost($msg){

        $db = new Database('blog');
        $title = Escape($msg->getParam(3)->structMem('title')->scalarVal());
        $content = Escape($msg->getParam(3)->structMem('description')->scalarVal());
        $moddate = date("Y-m-d H:i:s");
        //get file name from database
        $id = $msg->getParam(0)->scalarval();
        $rs = $db->executeQuery("select count(id) as count from blogs where id=$id");
        $rs->next();
        list($count)=$rs->getCurrentValues();
        if($count < 1){
            $err = "postid : $id not found on server.";
        }
        else{
            $sql = "update blogs set title='$title',content='$content',moddate='$moddate' where id=$id";
            $db->executeQuery($sql);
            $err = txtdbapi_get_last_error();
        }
        
        if($err == null){
            return new xmlrpcresp(new xmlrpcval(true,'boolean'));
        }
        else{
            return new xmlrpcresp(0,-1,$err);
        }
    }

    //param0:string blogid
    //param1:string username
    //param2:string password 
    //param3:struct 
        //string name (WindowsLiveWriter/b19c1ccbecb2_EF6A/Capture_3.png)
        //string type image/png
        //base64 bits iVBORw0KGgoAAA===...
    function newMediaObject($msg){
        $type = $msg->getParam(3)->structMem('type')->scalarval();
        if(strpos($type,'image') != -1){
            $db = new Database('blog');
            $filename = date("Y-m-d-") . uniqid() . '.' . substr($type,strpos($type,'/')+1);
            $byte = $msg->getParam(3)->structMem('bits')->scalarval();
            $rs = $db->executeQuery("select count(*) as count from localgit");
            $rs->next();
            list($count)=$rs->getCurrentValues();
            if($count > 0){
                $rs = $db->executeQuery("select localpath from localgit");
                $rs->next();
                list($localpath)=$rs->getCurrentValues();
                if(!is_dir($localpath)){
                    $err = "$localpath is not a valid path.";
                }
                else{
                    $filepath = filePathCombine(filePathCombine($localpath,'assert\img'),$filename);
                    $file = fopen($filepath,"x+");
                    fwrite($file,$byte);
                    fclose($file);

                    return new xmlrpcresp(
                        new xmlrpcval(array(
                                'url' => new xmlrpcval('/assert/img/' . $filename,'string')
                            ),
                            'struct')
                    );
                }
            }
            
            if($err != null){
                return new xmlrpcresp(0,-1,$err);
            }
        }
    }

    //param0:string blogid
    //param1:string username
    //param2:string password
    //param3:int numberOfPosts

    //return
    //array 
    // struct post
        //string title
        //string description
        //array categories
    function getRecentPosts($msg){
        $top = $msg->getParam(3)->scalarval();
        $sql = "select id,title,content from blogs order by createdate desc limit $top";
        $db = new Database('blog');
        $rs = $db->executeQuery($sql);
        $arr = array();
        while($rs->next()){
            list($id,$title,$content) = $rs->getCurrentValues();
            $x = new xmlrpcval(
                array(
                    'title' => new xmlrpcval($title,'string'),
                    'description' => new xmlrpcval($content,'string'),
                    'categories' => new xmlrpcval(array(),'array'),
                    'postid' => new xmlrpcval($id,'string')
                    )
                ,'struct');
            array_push($arr,$x);
        }

        $err = txtdbapi_get_last_error();
        if($err != null){
            return new xmlrpcresp(0,-1,$err);
        }
        else{
            return new xmlrpcresp(new xmlrpcval($arr,'array'));
        }
    }

    $dispatch = array(
    	'blogger.getUsersBlogs' =>  array(
    		'function' => 'getUsersBlogs'
    		)
    	,'metaWeblog.newPost' => 
        array(
            'function' => 'newPost'
            )
        ,'metaWeblog.editPost' => 
        array(
            'function' => 'editPost'
            )
        ,'metaWeblog.getPost' => 
        array(
            'function' => 'getPost'
            )
        ,'metaWeblog.newMediaObject' => 
        array(
            'function' => 'newMediaObject'
            )
        ,'metaWeblog.getRecentPosts' =>
        array(
            'function' => 'getRecentPosts'
            )
    	);


    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $s = new xmlrpc_server($dispatch);
    }
    else{
        include('postlist.php');
    }


?>


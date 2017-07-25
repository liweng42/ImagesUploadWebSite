<?php

// If you want to ignore the uploaded files,
// set $demo_mode to true;

$demo_mode = false;
$upload_dir = 'uploads/';
$allowed_ext = array('jpg','jpeg','png','gif');


if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status('Error! Wrong HTTP method!');
}


if(array_key_exists('pic',$_FILES) && $_FILES['pic']['error'] == 0 ){

	$pic = $_FILES['pic'];

	if(!in_array(get_extension($pic['name']),$allowed_ext)){
		exit_status('Only '.implode(',',$allowed_ext).' files are allowed!');
	}

	if($demo_mode){

		// File uploads are ignored. We only log them.

		$line = implode('		', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
		file_put_contents('log.txt', $line.PHP_EOL, FILE_APPEND);

		exit_status('Uploads are ignored in demo mode.');
	}


	// Move the uploaded file from the temporary
	// directory to the uploads folder:
	$filename = get_uniqid().'.'.get_extension($pic['name']);
	$dir_ym = $upload_dir.date("Y")."/".date("m")."/"; //需要创建的文件夹目录
	$filename = $dir_ym.$filename;
	mk_folder($dir_ym);
	if(move_uploaded_file($pic['tmp_name'], $filename)){
		exit_status('File was uploaded successfuly!', $filename);
	}

}

exit_status('Something went wrong with your upload!');


// Helper functions

function exit_status($str, $filename=''){
	if (strlen($filename) > 0)
		echo json_encode(array('status'=>$str, 'filename'=>$filename));
	else
		echo json_encode(array('status'=>$str));
	exit;
}

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}

function get_uniqid(){
	 //16 位唯一字符串
	 return substr( md5(uniqid(microtime(true),true)), 8, 16 ) ;
}

/*递归建立多层目录函数*/
function mk_folder($path){
  if(!is_readable($path)){
    mk_folder( dirname($path) );
    if(!is_file($path)) mkdir($path,0777);
    }
}
?>

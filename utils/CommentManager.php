<?php

class CommentManager
{
	private static $instance = null;

	private function __construct()
	{
		require_once(ROOT . '/utils/DB.php');
		require_once(ROOT . '/class/Comment.php');
	}

	public static function getInstance()
	{
		if (null === self::$instance) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	public function listComments()
	{
		$db = DB::getInstance();
		$rows = $db->select('SELECT * FROM `comment`');

		$comments = [];
		foreach($rows as $row) {
			$n = new Comment();
			$comments[] = $n->setId($row['id'])
			  ->setBody($row['body'])
			  ->setCreatedAt($row['created_at'])
			  ->setNewsId($row['news_id']);
		}

		// We can Model get() method , we have many option by collection
		$comments = Comment::get()->toArray(); // which return data in an array format.
		return $comments;
	}


	// public function addCommentForNews($body, $newsId)
	// {
	// 	$db = DB::getInstance();
	// 	$sql = "INSERT INTO `comment` (`body`, `created_at`, `news_id`) VALUES('". $body . "','" . date('Y-m-d') . "','" . $newsId . "')";
	// 	$db->exec($sql);
	// 	return $db->lastInsertId($sql);
	// }

	// public function deleteComment($id)
	// {
	// 	$db = DB::getInstance();
	// 	$sql = "DELETE FROM `comment` WHERE `id`=" . $id;
	// 	return $db->exec($sql);
	// }
	public function addCommentForNews(Request,$requestData)
	{
		// Narendra
		// Use Request
		// Use DB
		// use any inbuilt encryption and decryption techinique
		// Use CSRF Token validation through middleware 
		// while any transactional features happening CSRF token is important
		if ($requestData->header('X-CSRF-TOKEN') == false) {
			return "/logout";
		}
		// validate session is exists or not (Use Session;)
		try {
	        DB::beginTransaction(); // this is inbuilt function of Laravel Database transcations
	        if(!empty($requestData)){
	            foreach ($requestData as $key => $value) {
	                $dataArr = [
	                    'id' => setId($value['id']),
	                    'title' => setTitle($value['title']),
	                    'body' => setBody($value['body']),
	                ];
	                $comment_id = Comment::insertGetId($dataArr);
	                // Comment will be a model and function will return an ID  after insertion
	                // Crud Functionality can be add here
	            }
	        }
	        DB::commit();
	        return 1;
	    } catch (\Exception $exception) {
	    	DB::rollback();
			// it can be common helper function
			// if you get any exception we can roll back the whole transaction. It's a default laravel database functionlaity
	    	// we handle any types of error and store in that particular error logs DB with session data
	    	// Errorlog::insert($exception)
	    }
	}
	public function deleteComment(Request,$requestData)
	{
		// Same process as above function
		// instead of hard delete we need to do Softdelete
		DB::beginTransaction();
		try {
			
			// Laravel model has default function which is Use SOFTDELETE=True
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			// it can be common helper function
			// if you get any exception we can roll back the whole transaction. It's a default laravel database functionlaity
	    	// we handle any types of error and store in that particular error logs DB with session data
	    	// Errorlog::insert($exception)
		}
	}
}
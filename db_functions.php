<?php
#Function to connect to the hungama database (make a connection) ends: local

$server_url = "localhost";

function db_connect()		
{			
		$DBHOST="localhost";
		$DBUSER="root";
		$DBPASS="";
		$DBNAME="oingo";

		  $link = mysqli_connect($DBHOST, $DBUSER, $DBPASS);
			
			if (!$link)
			  {
				die('Could not connect: ');
			  }
			   
			$db_link=mysqli_select_db($link,$DBNAME);
			if(!$db_link)
			{
				die('Could not select database: ');
			}
          return $link;
 }


function get_friends($uid){
	$link = db_connect();
		$user_id = mysqli_real_escape_string($link,$uid);
		
			$sql = "SELECT name from user where user_id IN (SELECT user_one_id FROM `friends`
		WHERE (`user_two_id` = $user_id)
		AND `status` = 1
UNION
SELECT user_two_id FROM `friends`
WHERE (user_one_id = $user_id)
AND `status` = 1) ";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['name'][$i] = $data['name'];
				

				$i++;
			}
		}
		return $dtls;
	}

function add_friends($user_one_id,$user_two_id){
	$link = db_connect();
		$user_one = mysqli_real_escape_string($link,$user_one_id);
		$user_two = mysqli_real_escape_string($link,$user_two_id);

		$sql ="INSERT INTO `friends`(`user_one_id`, `user_two_id`, `status`, `action_user_id`) VALUES ('$user_one','$user_two','0','$user_two')";	

		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
		return $ret;
}

function get_filter($user_id){

		$link = db_connect();
		$user_one = mysqli_real_escape_string($link,$user_id);
		date_default_timezone_set('EST');
		$month = date("d");
		
		$date = date("N"); 

		//SELECT @date := DAYOFWEEK($date);

		$sql = "SELECT * from notes
WHERE
notes.note_id IN
(
    SELECT a.note_id FROM
(SELECT DISTINCT note_id FROM `notes` JOIN filter ON filter.user_id = '$user_one'
WHERE (notes.visibility = 0 AND notes.user_id='$user_one')
OR (notes.visibility = 2 AND filter.visibility=2 )
Or (notes.visibility = 2 AND filter.visibility=1 AND notes.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = '$user_one')AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = '$user_one')AND `status` = 1))
OR 
(notes.visibility = 1 AND (filter.visibility=2 or filter.visibility=1 ) AND filter.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = notes.user_id)AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = notes.user_id)AND `status` = 1))) as a

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter JOIN user ON user.current_state_id = filter.state_id and filter.user_id = '$user_one')
)as b 
ON a.note_id = b.note_id

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter where filter.user_id = '$user_one'))as c
ON b.note_id = c.note_id

JOIN

(SELECT DISTINCT notes.note_id FROM notes JOIN filter JOIN location
WHERE
	filter.user_id='$user_one'
    AND
    location.user_id='$user_one'
     AND
	( haversine(notes.latitude,notes.longitude,location.latitude,location.longitude)<=notes.radius)
    AND
	( haversine(filter.latitude,filter.longitude,location.latitude,location.longitude)<=filter.radius)

)as d 
ON d.note_id = c.note_id

JOIN
(
SELECT distinct a.note_id FROM
(    
(SELECT type,note_id,start_date,end_date,start_time,end_time,day from notes join schedule on notes.schedule_id=schedule.schedule_id) as a 
join
(SELECT type,start_date,end_date,start_time,end_time,day from filter join schedule on filter.schedule_id=schedule.schedule_id AND filter.user_id = '$user_one') as b
JOIN
    location on user_id = '$user_one'
)
WHERE date(location.timestamp) BETWEEN a.start_date AND a.end_date
    AND
    	date(location.timestamp) BETWEEN b.start_date AND b.end_date
    AND 
    	time(location.timestamp) BETWEEN a.start_time AND a.end_time
    AND
    	time(location.timestamp) BETWEEN b.start_time AND b.end_time
    AND 
    	(  (a.type = 'next' OR a.type = 'every') AND (locate('$date',a.day)>0 )    ) OR (  (a.type = 'Monthly')  And (locate('$month',a.day)>0) )
    AND
    	(  (b.type = 'next' OR b.type = 'every') AND (locate('$date',b.day)>0 )    ) OR (  (b.type = 'Monthly')  And (locate('$month',b.day)>0) )
)as e 
ON d.note_id = e.note_id)
				";	


		
		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

		$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['note_id'][$i] = $data['note_id'];
				$dtls['description'][$i] = $data['description'];
				$dtls['comment_possible'][$i] = $data['comment_possible'];

				$i++;
			}
			
		}
		if ($i > 0) 
			return $dtls;
		else
			return array();

}

function make_friends($uid){
	$link = db_connect();
		$user_id = mysqli_real_escape_string($link,$uid);
		
			$sql ="SELECT * from user where user_id NOT IN (SELECT user_one_id FROM `friends`
		WHERE (`user_two_id` = $user_id)		
		UNION
		SELECT user_two_id FROM `friends`
		WHERE (user_one_id = $user_id))
		and user_id <> $user_id";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['name'][$i] = $data['name'];
				$dtls['user_id'][$i] = $data['user_id'];

				$i++;
			}
			
		}
		if ($i > 0) 
			return $dtls;
		else
			return array();
		
	}

function pending_friends($uid){
	$link = db_connect();
		$user_id = mysqli_real_escape_string($link,$uid);
		
			$sql ="SELECT * from user where user_id IN (SELECT user_one_id FROM `friends`
		WHERE `user_two_id` = $user_id
		AND `status` = 0 and action_user_id <> $user_id
		UNION 
		SELECT user_two_id FROM `friends`
		WHERE user_one_id =$user_id
		AND `status` = 0 and action_user_id <> $user_id)";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['name'][$i] = $data['name'];
				$dtls['user_id'][$i] = $data['user_id'];

				$i++;
			}
		}	
		if ($i > 0) 
			return $dtls;
		else
			return array();
	}


	function get_tags(){
		$link = db_connect();
		
			$sql = "SELECT * FROM tag ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['name'][$i] = $data['name'];
				$dtls['tag_id'][$i] = $data['tag_id'];
				$i++;
			}
		}
		return $dtls;
	}

	function get_states(){
		$link = db_connect();
		
			$sql = "SELECT * FROM state ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['name'][$i] = $data['name'];
				$dtls['state_id'][$i] = $data['state_id'];
				$i++;
			}
		}
		return $dtls;
	}

	function insert_notes($uid,$type,$repeat,$day,$start_time,$end_time,$start_date,$end_date,$latitude,$longitude,$Description,$visibility,$radius,$comment_possible,$tags){
		

			$link = db_connect();
			$user_id = mysqli_real_escape_string($link,$uid);

			$type = mysqli_real_escape_string($link,$type);
			$repeat = mysqli_real_escape_string($link,$repeat);
			$day = mysqli_real_escape_string($link,$day);
			$start_time = mysqli_real_escape_string($link,$start_time);
			$end_time = mysqli_real_escape_string($link,$end_time);
			$start_date = mysqli_real_escape_string($link,$start_date);
			$end_date = mysqli_real_escape_string($link,$end_date);
			$latitude = mysqli_real_escape_string($link,$latitude);
			$longitude = mysqli_real_escape_string($link,$longitude);
			$Description = mysqli_real_escape_string($link,$Description);
			$visibility = mysqli_real_escape_string($link,$visibility);
			$radius = mysqli_real_escape_string($link,$radius);
			$comment_possible = mysqli_real_escape_string($link,$comment_possible);
			$tags = mysqli_real_escape_string($link,$tags);


			$sql = "INSERT INTO schedule (type,repeat_mode,day,start_time,end_time,start_date,end_date) VALUES('$type','$repeat','$day','$start_time','$end_time','$start_date','$end_date')";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
	
			$last_schedule_id = mysqli_insert_id($link);
			echo ('last_schedule_id:'); 
			echo $last_schedule_id ;
			$sql = "INSERT INTO `notes`( `user_id`, `description`, `schedule_id`, `radius`, `latitude`, `longitude`, `comment_possible`, `visibility`) VALUES ('$user_id','$Description','$last_schedule_id','$radius','$latitude','$longitude','$comment_possible','$visibility')";	
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
	
			$last_note_id = mysqli_insert_id($link);
			echo ('last_note_id:'); 
			echo $last_note_id ;

			if(!empty($tags))
				$tags_array = explode(',', $tags);
			else
				$tags_array = array();
			print_r($tags_array);
			for ($i=0; $i < sizeof($tags_array); $i++) {
					$temp = $tags_array[$i];
     				$sql = "INSERT INTO `notetag`(`note_id`, `tag_id`) VALUES ('$last_note_id ','$temp')";	
					
					$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
		
  				}



			return $ret;
	}

	function get_notes(){
		
			$sql = "SELECT * FROM `notes`";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['note_id'][$i] = $data['note_id'];
				$dtls['latitude'][$i] = $data['latitude'];
				$dtls['longitude'][$i] = $data['longitude'];
				$dtls['description'][$i] = $data['description'];
				$i++;
			}
		}
		
		if ($i > 0) 
			return $dtls;
		else
			return array();

	}

	function get_notes_cust($user_id){
		
		$link = db_connect();
		$user_one = mysqli_real_escape_string($link,$user_id);
		date_default_timezone_set('EST');
		$month = date("d");
		
		$date = date("N"); 

		//SELECT @date := DAYOFWEEK($date);

		$sql = "SELECT * from notes
WHERE
notes.note_id IN
(
    SELECT a.note_id FROM
(SELECT DISTINCT note_id FROM `notes` JOIN filter ON filter.user_id = '$user_one'
WHERE (notes.visibility = 0 AND notes.user_id='$user_one')
OR (notes.visibility = 2 AND filter.visibility=2 )
Or (notes.visibility = 2 AND filter.visibility=1 AND notes.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = '$user_one')AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = '$user_one')AND `status` = 1))
OR 
(notes.visibility = 1 AND (filter.visibility=2 or filter.visibility=1 ) AND filter.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = notes.user_id)AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = notes.user_id)AND `status` = 1))) as a

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter JOIN user ON user.current_state_id = filter.state_id and filter.user_id = '$user_one')
)as b 
ON a.note_id = b.note_id

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter where filter.user_id = '$user_one'))as c
ON b.note_id = c.note_id

JOIN

(SELECT DISTINCT notes.note_id FROM notes JOIN filter JOIN location
WHERE
	filter.user_id='$user_one'
    AND
    location.user_id='$user_one'
     AND
	( haversine(notes.latitude,notes.longitude,location.latitude,location.longitude)<=notes.radius)
    AND
	( haversine(filter.latitude,filter.longitude,location.latitude,location.longitude)<=filter.radius)

)as d 
ON d.note_id = c.note_id

JOIN
(
SELECT distinct a.note_id FROM
(    
(SELECT type,note_id,start_date,end_date,start_time,end_time,day from notes join schedule on notes.schedule_id=schedule.schedule_id) as a 
join
(SELECT type,start_date,end_date,start_time,end_time,day from filter join schedule on filter.schedule_id=schedule.schedule_id AND filter.user_id = '$user_one') as b
JOIN
    location on user_id = '$user_one'
)
WHERE date(location.timestamp) BETWEEN a.start_date AND a.end_date
    AND
    	date(location.timestamp) BETWEEN b.start_date AND b.end_date
    AND 
    	time(location.timestamp) BETWEEN a.start_time AND a.end_time
    AND
    	time(location.timestamp) BETWEEN b.start_time AND b.end_time
    AND 
    	(  (a.type = 'next' OR a.type = 'every') AND (locate('$date',a.day)>0 )    ) OR (  (a.type = 'Monthly')  And (locate('$month',a.day)>0) )
    AND
    	(  (b.type = 'next' OR b.type = 'every') AND (locate('$date',b.day)>0 )    ) OR (  (b.type = 'Monthly')  And (locate('$month',b.day)>0) )
)as e 
ON d.note_id = e.note_id)
				";	


		
		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

		$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
					$dtls['note_id'][$i] = $data['note_id'];
				$dtls['latitude'][$i] = $data['latitude'];
				$dtls['longitude'][$i] = $data['longitude'];
				$dtls['description'][$i] = $data['description'];
				$i++;
			}
			
		}
		if ($i > 0) 
			return $dtls;
		else
			return array();

	}


	function insert_filter($uid,$type,$repeat,$day,$start_time,$end_time,$start_date,$end_date,$latitude,$longitude,$visibility,$radius,$tags,$state_id){
		

			$link = db_connect();
			$user_id = mysqli_real_escape_string($link,$uid);

			$type = mysqli_real_escape_string($link,$type);
			$repeat = mysqli_real_escape_string($link,$repeat);
			$day = mysqli_real_escape_string($link,$day);
			$start_time = mysqli_real_escape_string($link,$start_time);
			$end_time = mysqli_real_escape_string($link,$end_time);
			$start_date = mysqli_real_escape_string($link,$start_date);
			$end_date = mysqli_real_escape_string($link,$end_date);
			$latitude = mysqli_real_escape_string($link,$latitude);
			$longitude = mysqli_real_escape_string($link,$longitude);
			$visibility = mysqli_real_escape_string($link,$visibility);
			$radius = mysqli_real_escape_string($link,$radius);
			$tags = mysqli_real_escape_string($link,$tags);

			$state_id = mysqli_real_escape_string($link,$state_id);

			$sql = "INSERT INTO schedule (type,repeat_mode,day,start_time,end_time,start_date,end_date) VALUES('$type','$repeat','$day','$start_time','$end_time','$start_date','$end_date')";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
	
			$last_schedule_id = mysqli_insert_id($link);
			echo ('last_schedule_id:'); 
			echo $last_schedule_id ;
			$sql = "INSERT INTO `filter`( `user_id`, `schedule_id`, `radius`, `latitude`, `longitude`, `visibility`, `tag_id`, `state_id`) VALUES ('$user_id','$last_schedule_id','$radius','$latitude','$longitude','$visibility','$tags', '$state_id')";	
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
	
			echo "successful";
  				


			return $ret;
	}
	function insert_user($name,$address,$contact,$email,$password,$state_id,$latitude,$longitude){   
		

			$link = db_connect();
			

			$name = mysqli_real_escape_string($link,$name);
			$address = mysqli_real_escape_string($link,$address);
			$contact_no = mysqli_real_escape_string($link,$contact);
			$email = mysqli_real_escape_string($link,$email);
			$password = mysqli_real_escape_string($link,$password);
			$password=md5($password);
			$latitude = mysqli_real_escape_string($link,$latitude);
			$longitude = mysqli_real_escape_string($link,$longitude);
			

			$state_id = mysqli_real_escape_string($link,$state_id);

			
		
	
			$last_user_id = mysqli_insert_id($link);
			
			$sql = "INSERT INTO `user`( `user_id`, `name`, `address`, `contact_no`,`email`, `password` , `current_state_id`) VALUES ('last_user_id','$name','$address','$contact','$email','$password', '$state_id')";	
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
		
			$last_user_id = mysqli_insert_id($link);
			$sql = "INSERT INTO `location`(`user_id`, `latitude`, `longitude`) VALUES ($last_user_id,$latitude,$longitude)";
  				
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

			return $ret;
	}


	function update_friends_accept($accepted_friends_id,$uid){
		
			$link = db_connect();
			$user_id = mysqli_real_escape_string($link,$uid);

			$accepted_friends_id = mysqli_real_escape_string($link,$accepted_friends_id);
			$sql = "UPDATE friends SET status = '1', action_user_id = '$user_id' Where ((user_one_id = $user_id AND user_two_id = $accepted_friends_id) OR (user_two_id = $user_id AND user_one_id = $accepted_friends_id)) ";	
					

			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}
	function block_friends($add_friends_id,$original_user){
		
			$link = db_connect();
			$user_id = mysqli_real_escape_string($link,$original_user);

			$accepted_friends_id = mysqli_real_escape_string($link,$add_friends_id);
			$sql = "UPDATE friends SET status = '3', action_user_id = '$user_id' Where ((user_one_id = $user_id AND user_two_id = $accepted_friends_id) OR (user_two_id = $user_id AND user_one_id = $accepted_friends_id)) ";	
					

			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function get_comment($nid){
	$link = db_connect();
		$note_id = mysqli_real_escape_string($link,$nid);
	
			$sql ="SELECT * from comment join user where note_id = $note_id and user.user_id = comment.user_id ";	
					
			
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['description'][$i] = $data['description'];
				$dtls['user_id'][$i] = $data['user_id'];
				$dtls['name'][$i] = $data['name'];
				$i++;
			}
		}	
		if ($i > 0) 
			return $dtls;
		else
			return array();
	}

	function write_comment($comment,$original_user,$note_id){
		$link = db_connect();
		
		$comment = mysqli_real_escape_string($link,$comment);
		$user_id = mysqli_real_escape_string($link,$original_user);
		$note_id = mysqli_real_escape_string($link,$note_id);

		$sql = "INSERT INTO comment (description,user_id,note_id) VALUES('$comment','$user_id','$note_id')";	
		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));	
		return $ret;
	
	}

	function update_state($original_user,$state_id){
		$link = db_connect();
		
		$user_id = mysqli_real_escape_string($link,$original_user);
		$state_id = mysqli_real_escape_string($link,$state_id);

		$sql = "update user SET `current_state_id` = '$state_id' where user_id = $user_id ";	
		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));	
		return $ret;
	}
function get_filter_search($user_id,$search){

		$link = db_connect();
		$user_one = mysqli_real_escape_string($link,$user_id);
		$search = mysqli_real_escape_string($link,$search);
		date_default_timezone_set('EST');
		$month = date("d");
		
		$date = date("N"); 

		//SELECT @date := DAYOFWEEK($date);

		$sql = "SELECT * from notes
WHERE
notes.note_id IN
(
    SELECT a.note_id FROM
(SELECT DISTINCT note_id FROM `notes` JOIN filter ON filter.user_id = '$user_one'
WHERE (notes.visibility = 0 AND notes.user_id='$user_one')
OR (notes.visibility = 2 AND filter.visibility=2 )
Or (notes.visibility = 2 AND filter.visibility=1 AND notes.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = '$user_one')AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = '$user_one')AND `status` = 1))
OR 
(notes.visibility = 1 AND (filter.visibility=2 or filter.visibility=1 ) AND filter.user_id IN (SELECT user_one_id FROM `friends`WHERE (`user_two_id` = notes.user_id)AND `status` = 1 UNION SELECT user_two_id  FROM `friends`WHERE (user_one_id = notes.user_id)AND `status` = 1))) as a

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter JOIN user ON user.current_state_id = filter.state_id and filter.user_id = '$user_one')
)as b 
ON a.note_id = b.note_id

JOIN

(SELECT note_id FROM `notetag` 
WHERE notetag.tag_id IN 
( SELECT tag_id from filter where filter.user_id = '$user_one'))as c
ON b.note_id = c.note_id

JOIN

(SELECT DISTINCT notes.note_id FROM notes JOIN filter JOIN location
WHERE
	filter.user_id='$user_one'
    AND
    location.user_id='$user_one'
     AND
	( haversine(notes.latitude,notes.longitude,location.latitude,location.longitude)<=notes.radius)
    AND
	( haversine(filter.latitude,filter.longitude,location.latitude,location.longitude)<=filter.radius)

)as d 
ON d.note_id = c.note_id

JOIN
(
SELECT distinct a.note_id FROM
(    
(SELECT type,note_id,start_date,end_date,start_time,end_time,day from notes join schedule on notes.schedule_id=schedule.schedule_id) as a 
join
(SELECT type,start_date,end_date,start_time,end_time,day from filter join schedule on filter.schedule_id=schedule.schedule_id AND filter.user_id = '$user_one') as b
JOIN
    location on user_id = '$user_one'
)
WHERE date(location.timestamp) BETWEEN a.start_date AND a.end_date
    AND
    	date(location.timestamp) BETWEEN b.start_date AND b.end_date
    AND 
    	time(location.timestamp) BETWEEN a.start_time AND a.end_time
    AND
    	time(location.timestamp) BETWEEN b.start_time AND b.end_time
    AND 
    	(  (a.type = 'next' OR a.type = 'every') AND (locate('$date',a.day)>0 )    ) OR (  (a.type = 'Monthly')  And (locate('$month',a.day)>0) )
    AND
    	(  (b.type = 'next' OR b.type = 'every') AND (locate('$date',b.day)>0 )    ) OR (  (b.type = 'Monthly')  And (locate('$month',b.day)>0) )
)as e 
ON d.note_id = e.note_id)AND MATCH(notes.description) AGAINST('$search' IN BOOLEAN MODE);
				";	


		
		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

		$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				
				$dtls['note_id'][$i] = $data['note_id'];
				$dtls['description'][$i] = $data['description'];
				$dtls['comment_possible'][$i] = $data['comment_possible'];

				$i++;
			}
			
		}
		if ($i > 0) 
			return $dtls;
		else
			return array();

}
function update_loc($latitude,$longitude,$timee_stamp,$original_user){
		
			$link = db_connect();
			$user_id = mysqli_real_escape_string($link,$original_user);

			$latitude = mysqli_real_escape_string($link,$latitude);
			$longitude = mysqli_real_escape_string($link,$longitude);
			

			$sql = "UPDATE location SET latitude = '$latitude', longitude = '$longitude', timestamp ='$timee_stamp' Where user_id=$user_id ";	
					

			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

 /*
 function insert_questionary($name,$nationality,$mobile,$email,$birth_date,$hobby,$palce_visited,$favourite_place,$frequency,$purpose,$theme,$destinations,$travel_date,$days,$accommodation,$budget){
		
			$sql = "INSERT INTO questionary (name,nationality,mobile,email,birth_date,hobby,palce_visited,favourite_place,frequency,purpose,theme,destinations,travel_date,days,accommodation,budget) VALUES('$name','$nationality','$mobile','$email','$birth_date','$hobby','$palce_visited','$favourite_place','$frequency','$purpose','$theme','$destinations','$travel_date','$days','$accommodation','$budget')";	
			

			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function insert_hotel($id,$hotel_name,$hotel_city,$hotel_nights,$hotel_category){
		
			$sql = "INSERT INTO hotel (package_id,name,city,nights,category) VALUES('$id','$hotel_name','$hotel_city','$hotel_nights','$hotel_category')";	
			

			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

 	function insert_itenary($id,$ite_city,$ite_details,$ite_date,$ite_description){
			
			//echo $ite_description;

			$sql = "INSERT INTO itenary (package_id,ite_city,ite_details,ite_date,ite_description) VALUES('$id','$ite_city','$ite_details','$ite_date','$ite_description')";	
			

			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}



	function adminlogin($username,$password){
		
			$sql = "SELECT username,password FROM admin WHERE username='$username' and password='$password' ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

			$row  = mysqli_fetch_array($ret);

			if(is_array($row)) {
			session_start();
			$_SESSION['username']=$username;
			return true;
			
			} else {
				return false;
			
			}


			
	}
	
	function insert_theme($name,$image,$description){
		
			$sql = "INSERT INTO theme (name,image,description,creationdate) VALUES('".addslashes($name)."','".addslashes($image)."','".addslashes($description)."',NOW())";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function delete_theme($id){
		
			$sql = "DELETE FROM theme Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function delete_itenary($id){
		
			$sql = "DELETE FROM itenary Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function delete_hotel($id){
		
			$sql = "DELETE FROM hotel Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function update_theme($id,$name,$image,$description){
		
			$sql = "UPDATE theme SET name = '$name', image = '$image', description ='$description', updatedate = NOW() Where id=$id ";	
					

			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function edit_theme($id){
		
			$sql = "SELECT * FROM theme Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['name'][$i] = $data['name'];
				$dtls['image'][$i] = $data['image'];
				$dtls['description'][$i] = $data['description'];
				$i++;
			}
		}
		return $dtls;
	}

	function get_last_images(){
		
			$sql = "SELECT * FROM `theme` order by creationdate DESC LIMIT 8";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['creationdate'][$i] = $data['creationdate'];
				$dtls['updatedate'][$i] = $data['updatedate'];
				$dtls['name'][$i] = $data['name'];
				$dtls['image'][$i] = $data['image'];
				$dtls['description'][$i] = $data['description'];
				$dtls['package_id'][$i] = $data['package_id'];
				$i++;
			}
		}
		return $dtls;
	}

	
	function get_last_package_images(){
		
			$sql = "SELECT * FROM package where type='Private Group' order by id DESC LIMIT 4";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['package_home_image'][$i] = $data['package_home_image'];
				
				$i++;
			}
		}
		return $dtls;
	}

	function get_theme(){
		
			$sql = "SELECT * FROM theme";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['creationdate'][$i] = $data['creationdate'];
				$dtls['updatedate'][$i] = $data['updatedate'];
				$dtls['name'][$i] = $data['name'];
				$dtls['image'][$i] = $data['image'];
				$dtls['description'][$i] = $data['description'];
				$i++;
			}
		}
		return $dtls;
	}

	function retrieve_itenary($id){
		
		
			$sql = "SELECT * FROM itenary where package_id= $id";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['ite_city'][$i] = $data['ite_city'];
				$dtls['ite_date'][$i] = $data['ite_date'];
				$dtls['ite_details'][$i] = $data['ite_details'];
				$dtls['ite_description'][$i] = $data['ite_description'];
				
				$i++;
			}
		}
		return $dtls;
	}



	function retrieve_package($id){
		
		
			$sql = "SELECT * FROM package where id= $id";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['type'][$i] = $data['type'];
				$dtls['name'][$i] = $data['name'];
				$dtls['experience'][$i] = $data['experience'];
				$dtls['tags'][$i] = $data['tags'];
				$dtls['city'][$i] = $data['city'];
				$dtls['country'][$i] = $data['country'];
				$dtls['price'][$i] = $data['price'];
				$dtls['highlights'][$i] = $data['highlights'];
				$dtls['overview'][$i] = $data['overview'];
				$dtls['type_of_room'][$i] = $data['type_of_room'];
				$dtls['inclusion'][$i] = $data['inclusion'];
				$dtls['ite_city'][$i] = $data['ite_city'];
				$dtls['ite_date'][$i] = $data['ite_date'];
				$dtls['ite_details'][$i] = $data['ite_details'];
				$dtls['ite_inclusion'][$i] = $data['ite_inclusion'];
				$dtls['ite_exclusion'][$i] = $data['ite_exclusion'];

				$dtls['slider1'][$i] = $data['slider1'];
				$dtls['slider2'][$i] = $data['slider2'];
				$dtls['slider3'][$i] = $data['slider3'];
				$dtls['slider4'][$i] = $data['slider4'];
				$dtls['slider5'][$i] = $data['slider5'];

				$i++;
			}
		}
		return $dtls;
	}

	function retrieve_hotel($id){
		
		
			$sql = "SELECT * FROM hotel where 	package_id= $id";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;


		if($ret){
			while($data =mysqli_fetch_array($ret)){
					

				$dtls['id'][$i] = $data['id'];
				$dtls['name'][$i] = $data['name'];
				$dtls['city'][$i] = $data['city'];
				$dtls['nights'][$i] = $data['nights'];
				$dtls['category'][$i] = $data['category'];
				$dtls['package_id'][$i] = $data['package_id'];
				$dtls['itenary_id'][$i] = $data['itenary_id'];
				$i++;
			}
		}
		return $dtls;
	}


	function get_package(){
		
		
			$sql = "SELECT * FROM package";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
				$dtls['id'][$i] = $data['id'];
				$dtls['type'][$i] = $data['type'];
				$dtls['name'][$i] = $data['name'];
				$dtls['experience'][$i] = $data['experience'];
				$dtls['tags'][$i] = $data['tags'];
				$dtls['city'][$i] = $data['city'];
				$dtls['country'][$i] = $data['country'];
				$dtls['price'][$i] = $data['price'];
				$dtls['overview'][$i] = $data['overview'];
				$dtls['inclusion'][$i] = $data['inclusion'];
				$dtls['ite_city'][$i] = $data['ite_city'];
				$dtls['ite_date'][$i] = $data['ite_date'];
				$dtls['ite_details'][$i] = $data['ite_details'];
				$dtls['ite_inclusion'][$i] = $data['ite_inclusion'];
				$dtls['ite_exclusion'][$i] = $data['ite_exclusion'];

				$i++;
			}
		}
		return $dtls;
	}

	
	function insert_package($type,$name,$image,$slider1,$slider2,$slider3,$slider4,$slider5,$experience,$tags,$city,$country,$price,$highlights,$overview,$type_of_room,$inclusion,$ite_inclusion,$ite_exclusion){
		
			$sql = "INSERT INTO package (type,name,package_home_image,slider1,slider2,slider3,slider4,slider5,experience,tags,city,country,price,highlights,overview,type_of_room,inclusion,ite_inclusion,ite_exclusion) VALUES('$type','$name','$image','$slider1','$slider2','$slider3','$slider4','$slider5','$experience','$tags','$city','$country','$price','$highlights','$overview','$type_of_room','$inclusion','$ite_inclusion','$ite_exclusion')";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

			$last_id = mysqli_insert_id($link);
			return $last_id;
	}


	function delete_package($id){
		
			$sql = "DELETE FROM package Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			return $ret;
	}

	function update_package($id,$type,$name,$index_image,$image,$image_type,$slider1,$slider2,$slider3,$slider4,$slider5,$experience,$tags,$city,$country,$price,$highlights,$overview,$type_of_room,$inclusion,$ite_inclusion,$ite_exclusion){
		
			$sql = "UPDATE package SET type='$type',name='$name',index_image='$index_image',package_home_image='$image',image_type='$image_type',slider1='$slider1',slider2='$slider2',slider3='$slider3',slider4='$slider4',slider5='$slider5',experience='$experience',tags='$tags',city='$city',country='$country',price='$price',highlights='$highlights',overview='$overview',type_of_room='$type_of_room',inclusion='$inclusion',ite_inclusion='$ite_inclusion',ite_exclusion='$ite_exclusion' WHERE id='$id' ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));

			//$last_id = mysqli_insert_id($link);
			return $ret;
	}


	function edit_package($id){
		
			$sql = "SELECT * FROM package Where id=$id ";	
					
			$link = db_connect();
			$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
			$i = 0;

		if($ret){
			while($data =mysqli_fetch_array($ret)){
			$dtls['id'][$i] = $data['id'];
				$dtls['package_home_image'][$i] = $data['package_home_image'];
				$dtls['index_image'][$i] = $data['index_image'];
				$dtls['image_type'][$i] = $data['image_type'];
				
				$dtls['slider1'][$i] = $data['slider1'];
				$dtls['slider2'][$i] = $data['slider2'];
				$dtls['slider3'][$i] = $data['slider3'];
				$dtls['slider4'][$i] = $data['slider4'];
				$dtls['slider5'][$i] = $data['slider5'];

				$dtls['type'][$i] = $data['type'];
				$dtls['name'][$i] = $data['name'];
				$dtls['experience'][$i] = $data['experience'];
				$dtls['tags'][$i] = $data['tags'];
				$dtls['city'][$i] = $data['city'];
				$dtls['country'][$i] = $data['country'];
				$dtls['price'][$i] = $data['price'];
				$dtls['highlights'][$i] = $data['highlights'];
				$dtls['overview'][$i] = $data['overview'];
				$dtls['type_of_room'][$i] = $data['type_of_room'];
				$dtls['inclusion'][$i] = $data['inclusion'];
				$dtls['ite_city'][$i] = $data['ite_city'];
				$dtls['ite_date'][$i] = $data['ite_date'];
				$dtls['ite_details'][$i] = $data['ite_details'];
				$dtls['ite_inclusion'][$i] = $data['ite_inclusion'];
				$dtls['ite_exclusion'][$i] = $data['ite_exclusion'];
				$i++;
			}
		}
		return $dtls;
	}

	// function update_package($id,$type,$name,$experience,$tags,$city,$country,$price,$overview,$inclusion,$ite_city,$ite_date,$ite_details,$ite_inclusion,$ite_exclusion){
		
	// 		$sql = "UPDATE package SET type = '$type', name = '$name', experience = '$experience', tags ='$tags', city = '$city', country = '$country', price = '$price', overview ='$overview', inclusion = '$inclusion', ite_city = '$ite_city', ite_date = '$ite_date', ite_details ='$ite_details', ite_inclusion = '$ite_inclusion', ite_exclusion ='$ite_exclusion' Where id=$id ";	
					

	// 		$link = db_connect();
	// 		$ret = mysqli_query($link,$sql) or die(mysqli_error($link));
	// 		return $ret;
	// }
	
	?>
*/
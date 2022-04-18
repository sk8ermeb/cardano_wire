<?php
require __DIR__ . '/articleextract.php';
function logpr($data)
{
  $myfile = fopen("log.txt", "a");
  //fwrite($myfile, strval($form_data[getfield($form_data, "Address")])."\n");
  fwrite($myfile, strval($data));
  fwrite($myfile, "\n");
  fclose($myfile);

}

function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}



function article_preview(){

  global $wpdb;
	global $wp;
	$table_name_article = $wpdb->prefix . "cardanowire_articlecache";
	
	$discard = $_GET["discard"];
	$publish = $_GET["publish"];
	$scan = $_GET["scan"];
	if($publish > 0)
	{
		$updated = $wpdb->update( $table_name_article, array( 'status' => 1), array( 'id' => $publish ));
  	$sql = $wpdb->prepare( "SELECT * FROM $table_name_article WHERE id = %d", array($publish) );
  	$results = $wpdb->get_results($sql);
		if(count($results) > 0)
		{
			$newfolder = substr($results[0]->location, 0, strlen($results[0]->location) - 4);
			$htmlfile = "$newfolder/article.html";
    	$myfile = fopen($htmlfile, "r");
    	$contents = fread($myfile,filesize($htmlfile));
    	$contents = processdom($contents, $results[0]->ipfs);
			$contents = "Donation Address: ".$results[0]->addressowner."<br />".$contents;
			$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";	
  		$sql = $wpdb->prepare( "SELECT * FROM $table_name_articletags WHERE article = %d", array($results[0]->id) );
  		$tagresults = $wpdb->get_results($sql);
			$tags = [];
			if(count($tagresults)>0)
			{
				foreach($tagresults as $atag)
				{
					array_push($tags, $atag->tag);
				}
			}
			$postarr = ['post_title'=>$results[0]->name, 'post_content'=>$contents, 'post_name'=>$results[0]->name, 
				'tags_input'=>$tags, 'post_date_gmt'=>$results[0]->mintdate];
			//$postarr = ['post_title'=>"blabla"];
			$ret = wp_insert_post($postarr);
			//echo "return = $ret <br />";
			wp_publish_post($ret);
		}
		echo "publishing $publish";
	}
	if($discard > 0)
	{
		$updated = $wpdb->update( $table_name_article, array( 'status' => -1), array( 'id' => $discard ));
		//$upload_dir = wp_upload_dir();
		//$upurl = $upload_dir['baseurl'];
	  //$upurl = $upurl."/cardano_wire/".$ipfs;
  	$sql = $wpdb->prepare( "SELECT * FROM $table_name_article WHERE id = %d", array($discard) );
  	$results = $wpdb->get_results($sql);
		$loc = $results[0]->location;
		$res = unlink($results[0]->location);
		$newfolder = substr($results[0]->location, 0, strlen($results[0]->location) - 4);
		deleteDir($newfolder);		
		$name = $results[0]->name;
		echo "Discarded $name <br />";
	}
	//echo "--$discard--$publish--";
	if($scan > 0)
	{
		//trim($stdout, " \n\r\t\v\0");
		$maxsize = trim($_GET["maxsize"], " \n\r\t\v\0");
		$old = trim($_GET["old"], " \n\r\t\v\0");
		$tags = trim($_GET["tags"], " \n\r\t\v\0");
		$minstacked = trim($_GET["minstacked"], " \n\r\t\v\0");
		$adaadress = trim($_GET["adaadress"], " \n\r\t\v\0");
		//function blockarticleextract($oldest=null, $minada=4, $maxsize=10, $tags=[], $address=null)	
		$criteria = [];
		if(strlen($tags) > 0)
		{
			$tags = explode(',', $tags);
			$trimtags = [];
			//$data = explode(',', "abc , def, hij,klm, ");
			foreach($tags as $tag)
			{
				$tag = trim($tag, " \n\r\t\v\0");
				array_push($trimtags, $tag);
			}
			$criteria['tags'] = $trimtags;
		}
		if(strlen($adaadress) > 0)
		{
			$criteria['address'] = $adaadress;
		}
		//"adaadress"
		if(strlen($maxsize) > 0)
		{
			$maxsize = intval($maxsize);
			if($maxsize > 0){
				$criteria['maxsize'] = $maxsize;
			}
			else{
				echo "failed to parse Max size (using default of 10)<br />";
			}
		}
		if(strlen($minstacked) > 0)
		{
			$minstacked = intval($minstacked);
			if($minstacked > 0){
				$criteria['minada'] = $minstacked;
			}
			else{
				echo "failed to parse Min Stacked ADA (using default of 5)<br />";
			}
		}
		if(strlen($old) > 0)
		{
			try{
				$olddt = new DateTime($old);
				$criteria['oldest'] = $olddt;
			}catch (Exception $e) {
				echo "failed to parse date (using default of April 10th 2022<br />";
			}
		}
		//$olddt = new DateTime("2022-04-10 18:04:45");
		////$cnt = blockarticleextract($old, 1.9, 4, ['cool'], $addr);
		$extracted = blockarticleextract($criteria);
		echo "$extracted articles extracted. <br/>";
		//
	}

  $article = strval($_GET['id']);
	if($article ==false)
	{
  	$sql = $wpdb->prepare( "SELECT * FROM $table_name_article WHERE status = %d", array(0) );
  	$results = $wpdb->get_results($sql);
		if(count($results)>0){
			$out = "<table><tr><th>Name</th><th>Tags</th><th>Mint Date</th><th>Owner</th><th>Stacked Ada</th>
  </tr>
		";
			foreach($results as $article)
			{
				$table_name_articletags = $wpdb->prefix . "cardanowire_article_tags";	
  			$sql = $wpdb->prepare( "SELECT * FROM $table_name_articletags WHERE article = %d", array($article->id) );
  			$tagresults = $wpdb->get_results($sql);
				$tags = "";
				if(count($tagresults)>0)
				{
					foreach($tagresults as $atag)
					{
						$tags.="$atag->tag,";
					}
				}
				$lovelace = intval($article->stackedlovelace);
				$ada = $lovelace/1000000;
				$ada = strval($ada);
				$namelink =  home_url( $wp->request );
				//$article->name;
				$namelink = "<a href=$namelink/?id=".strval($article->id).">$article->name</a>";
				$ownerlink = "<a href=https://pool.pm/$article->addressowner>$article->addressowner</a>";
				$out.="<tr>";
				$out.="<td>$namelink</td>";
				$out.="<td>$tags</td>";
				$out.="<td>$article->mintdate</td>";
				$out.="<td>$ownerlink</td>";
				$out.="<td>$ada</td>";
				$out.="</tr>";
			}
			$out.="</table>";
			echo $out;
		}
		else{
			echo "No Pending Articles for publishing. Try rescanning the Cardano Wire. ";
		}
?>
<form>
<input type="hidden" id="scan" name="scan" value="1">
<table>
<tr><td>More Resent Then:</td><td><input type="text" id="old" name="old" value="2022-04-10 18:04:45"></td></tr>
<tr><td>Max Article Size</td><td><input type="text" id="maxsize" name="maxsize" value="10"></td></tr>
<tr><td>Minimum Stacked ADA</td><td><input type="text" id="minstacked" name="minstacked" value="5"></td></tr>
<tr><td>Tags (comma separated)</td><td><input type="text" id="tags" name="tags" value=""></td></tr>
<tr><td>Publishers Address</td><td><input type="text" id="adaadress" name="adaadress" value="addr1vyh44zmfkeph7hw2c0hnclpzsy9dlxsas4477982vwfxapck875t5"></td></tr>
</table>
<input type="submit" value="Scan For Articles">
</form>
<?php
		return;
	}

  $sql = $wpdb->prepare( "SELECT * FROM $table_name_article WHERE id = %d", array($article) );

  $results = $wpdb->get_results($sql);
  if(count($results)==0){

    echo "Somethign went wrong. Aticle not found";
  }
	else{
		//echo print_r($results, true);
		$newfolder = substr($results[0]->location, 0, strlen($results[0]->location) - 4);
		
		if (!file_exists($newfolder)) {
			umask(0000);
    	mkdir($newfolder, 0777, true);
			$zip = new ZipArchive;
			$res = $zip->open($results[0]->location);
			if ($res === TRUE) {
  			$zip->extractTo($newfolder);
  			$zip->close();
			}
		}
		
// Single Site
		//echo $upurl;
		echo "Donations Address: ".$results[0]->addressowner.". Publish Date:".$results[0]->mintdate."<br/>";
		$htmlfile = "$newfolder/article.html";
		$myfile = fopen($htmlfile, "r");
		$contents = fread($myfile,filesize($htmlfile));
		$contents = processdom($contents, $results[0]->ipfs);
		echo $contents;
		?>
		<form>
		 <input type="submit" value="Publish">
		<input type="hidden" id="publish" name="publish" value="<?php echo $article;?>">
		</form>
		<form>
		 <input type="submit" value="Discard">
		<input type="hidden" id="discard" name="discard" value="<?php echo $article;?>">
		</form>
		<?php
	}
//$old = new DateTime("2022-04-10 18:04:45");
//$cnt = blockarticleextract($old, 1.9, 4, ['cool'], $addr);
}
function processdom($contents, $ipfs)
{
	$upload_dir = wp_upload_dir();
	$upurl = $upload_dir['baseurl'];
	$upurl = $upurl."/cardano_wire/".$ipfs;
	$doc = new DOMDocument();
	$doc->loadHTML($contents);    
	$elements = $doc->getElementsByTagName('img');
	foreach($elements as $element) {
   	$src =  $element->getAttribute('src');
		$element->setAttribute('src', "$upurl/$src");
	}
	$elements = $doc->getElementsByTagName('video');
	foreach($elements as $element) {
		$children = $element->childNodes;
		foreach ($children as $child)
		{
			if($child->tagName == "source")
			{
				$src =  $child->getAttribute('src');
				$child->setAttribute('src', "$upurl/$src");
			}
		}
	}
	$elements = $doc->getElementsByTagName('audio');
	foreach($elements as $element) {
		$children = $element->childNodes;
		foreach ($children as $child)
		{
			if($child->tagName == "source")
			{
				$src =  $child->getAttribute('src');
				$child->setAttribute('src', "$upurl/$src");
			}
		}
	}
	$elements = $doc->getElementsByTagName('embed');
	foreach($elements as $element) {
   	$src =  $element->getAttribute('src');
		$element->setAttribute('src', "$upurl/$src");
	}
	$contents = $doc->saveHTML(); 
	return $contents;
}


<?php

function logpr($data)
{
  $myfile = fopen("log.txt", "a");
  //fwrite($myfile, strval($form_data[getfield($form_data, "Address")])."\n");
  fwrite($myfile, strval($data));
  fwrite($myfile, "\n");
  fclose($myfile);

}




function article_preview(){
  $article = strval($_GET['id']);
  global $wpdb;
	global $wp;
	$table_name_article = $wpdb->prefix . "cardanowire_articlecache";


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
		$upload_dir = wp_upload_dir();
 
		$upurl = $upload_dir['baseurl'];
		$upurl = $upurl."/cardano_wire/".$results[0]->ipfs;
// Single Site
		//echo $upurl;
		echo "Donations Address: ".$results[0]->addressowner.". Publish Date:".$results[0]->mintdate."<br/>";
		$htmlfile = "$newfolder/article.html";
		$myfile = fopen($htmlfile, "r");
		$contents = fread($myfile,filesize($htmlfile));
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
		echo $contents;
		//outputscript($upurl);	
	}
}

function outputscript($upurl)
{
		?>
		<script>
			function img_find() {
 			var imgs = document.getElementsByTagName("img");
    	var imgSrcs = [];

    	for (var i = 0; i < imgs.length; i++) {
				alert(imgs[i].class);
				if(imgs[i].class === "CPW1985")
				{
					var src = imgs[i].src;
					var lio = src.lastIndexOf('/');
					src = src.substring(lio, src.length - lio);
					alert(src);
				}
        //imgSrcs.push(imgs[i].src);
    	}

    	return imgSrcs;
			}
		//alert("running");	
		img_find();
		</script>
    <?php
}

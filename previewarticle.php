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
		echo print_r($results, true);

}
 /* else
  {
		//echo print_r($results, true);
    $meta = $results[0]->meta;
		$addr = $results[0]->addr;
    $data = json_decode($meta, True);
    $data = reset($data);
    $data = reset($data);
    $data = reset($data);
		
    //$policyid =
    //$work = print_r($data, True);
    //$work = array_values($work)[0];
    //$work = reset($work);
    //$work = reset($work);
    //echo $work;
    $loc = $data->location;
    $policy = substr($nft, 0, strpos($nft, '.'));
    $html = "<table>";
    $ipfs = "";
    $ext = "";
    expand_array($data, $html, $ipfs, $ext);
    $html .= "<tr><th>Minted Address</th><td>$addr</td></tr>";
		//$html .= 
    $html = $html."</table>";
		
		echo $html;
		//$fullfile = $ipfs;
		$hash = $ipfs;
		if(strpos($hash, "ipfs://")===0)
		{
			$hash = substr($hash, 7);
		}
		$fullfile = $hash;
		if(strlen( $ext) > 0)
		{
			$fullfile = $hash.".".$ext;
		}
		echo "Your article data can be pulled from any public ipfs gateway. For
example try pasting the following link into your
browser:<br/>
<a href=\"https://nftstorage.link/ipfs/$hash\">https://nftstorage.link/ipfs/$hash</a>
 <br /> 
You can see your asset on any major block explorer. Keep in mind that the standard tag for image NFTs is 721 and articles are 1985 so they usually won't show you all the meta data on the block chain:<br/>
<a href=\"https://pool.pm/$addr\">https://pool.pm/$addr</a>";


  }
*/
}

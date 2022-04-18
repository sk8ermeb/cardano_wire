# cardano_wire
## Installation
Wordpress plugin for pulling articles off the cardano blockchain and publishing to your site. This requires that you have a blockfost.io Cardano mainnet key and an ipfs node running locally on your server. The next release will have a variety of public ipfs gateway option so you don't have to install anything on your server.
* https://blockfrost.io/dashboard
* https://docs.ipfs.io/install/command-line/#official-distributions  
  
once ipfs is installed you need to run the following 2 commands  
`$ipfs init`  
`$ipfs daemon`  
You may want to run the second command in the a terminal mux such as screen so it will run in the background. If you aren't sure what that it close it down and in linux you can run the following command to run it in the background  
`$ipfs daemon > /path/to/some/ipfslog.txt 2>&1 &`  
Just be warned you will not easily be able to shut it down, so you will have to kill it with the terminal. After that make sure that port 4001 and 5001 are open  
`$sudo ss -lntp`  
This will allow the wordpress plugin to use your own machine as a gateway. This is important for the anti-sensorship properties of this app. Next you need to download the actual plugin source code. If you have shell access you can clone this directly into your wordpress plugin directory or you can copy the code in there manually or with whatever ftp client you use  
`$git clone https://github.com/sk8ermeb/cardano_wire`  

## Configuration
After the cardano_wire plugin is inyour wordpress plugin directory, make sure to activate it. You need to open the "Cardano Wire" settings in your dasboard and supply your blockfrost api key. Without this you can't pull data off the blockchain. Next you need to create a private page with a shortcode "[article_preview]". This page is how you enter your blockchain scanning parameters, preview articles, and decide which ones to publish or discard. It should be self explanatory. 

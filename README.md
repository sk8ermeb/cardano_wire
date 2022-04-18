# Cardano Wire Wordpress Plugin
## Descrition
Wordpress plugin for pulling articles off the cardano blockchain and publishing to your site. This is based on https://pressmint.io/ minting nft articles.
## Installation
Download the plugin (this repository) and put it in the plugins folder within wordpress and activate it. The plugin requires the ability to extract data off the blockchain and download file from an ipfs gateway.
### Cardano Blockchain
The following ways are supported within the plugin:
* https://blockfrost.io/dashboard  

You need to obtain a cardanno mainnet key. The free tier will be more then enough for this plugin. Once you get your key you will need to provide that to the plugin. In the wordpress dashboard you will see a "Cardano Wire" settings page. copy and paste your key into the "Blockfrost Cardano API Key" field and save it.
## IPFS
The plugin also needs a way to download files from the he InterPlanetary File System (IPFS) protocol. This is a decentralized way of file storage. The plugin currently supports 2 ways:
* nftstorage.link  
* Local Node  

### ftstorage.link
This is the easiest to configure by far since it doesn't require any server access to install anything. Sense this is a free public resource it is really slow and may be unreliable at times. Additionally this won't contribute to the censorship resistant nature of this project. But many people don't have the ability to install packages to their server. To use this options simply select it in "Cardano Wire" settings in the dashboard. 
### Local Node
The preffered way is to run an IPFS node/gateway on the the machine where wordpress is hosted. This will work very fast, is completely reliable and will help keep artiles resistant to sensorship. 
* https://docs.ipfs.io/install/command-line/#official-distributions  
  
once ipfs is installed you need to run the following 2 commands  
`$ipfs init`  
`$ipfs daemon`  
You may want to run the second command in the a terminal mux such as screen so it will run in the background. If you aren't sure what that it close it down and in linux you can run the following command to run it in the background  
`$ipfs daemon > /path/to/some/ipfslog.txt 2>&1 &`  
Just be warned you will not easily be able to shut it down, so you will have to kill it with the terminal. After that make sure that port 4001 and 5001 are open  
`$sudo ss -lntp`  
This will allow the wordpress plugin to use your own machine as a gateway. This is important for the anti-sensorship properties of this app. Next you need to download the actual plugin source code. If you have shell access you can clone this directly into your wordpress plugin directory or you can copy the code in there manually or with whatever ftp client you use  


## Configuration
Lastly you need to create a a private page with the a shortcode "[article_preview]". This page is how you enter your blockchain scanning parameters, preview articles, and decide which ones to publish or discard. It should be self explanatory. When you publish an article it creates a new post on your behalf. That is it!

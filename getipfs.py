#!/usr/bin/python3

import http.client, urllib.parse
import sys
import ipfshttpclient
import os
maxsize = 2097152
if (len(sys.argv) ==3):
	hsh = 'QmWf5q7JhcSbzM2kKLU9S4pPQFeHex3JBStqRsnRHuhe7x'
	#params = urllib.parse.urlencode({'arg': hsh, 'offset': '0', 'length': '2097152'})
	params = urllib.parse.urlencode({'arg': sys.argv[1], 'offset': '0', 'length': str(maxsize+10)})
	
	headers = {"Content-type": "application/x-www-form-urlencoded", "Accept": "text/plain"}
	conn = http.client.HTTPConnection("127.0.0.1", port=5001)
	conn.request("POST", "/api/v0/cat?"+params)
	response = conn.getresponse()
	print(response.status, response.reason)
	data = response.read()
	tmppath = '/var/www/html/nftfiles/.'+sys.argv[2]+'.tmp'
	path = '/var/www/html/nftfiles/'+sys.argv[2]
	with open(tmppath, 'wb') as nftfile:
		nftfile.write(data)
	conn.close()
	b = os.path.getsize(tmppath)
	if(b > maxsize):
		os.remove(tmppath)
		with open(path, 'wb') as nftfile:
			pass
	else:
		os.rename(tmppath, path)
else:
	print("Wrong nunmber of arguments")


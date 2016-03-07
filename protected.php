<?php
/*
	AUTHOR: MicroVB Inc ( https://www.microvb.com )
	===================================================================

	The MIT License (MIT)
	Copyright (c) 2016 MicroVB Inc.

	Permission is hereby granted, free of charge, to any person
	obtaining a copy of this software and associated documentation
	files (the "Software"), to deal in the Software without restriction,
	including without limitation the rights to use, copy, modify, merge,
	publish, distribute, sublicense, and/or sell copies of the Software,
	and to permit persons to whom the Software is furnished to do so,
	subject to the following conditions:

	The above copyright notice and this permission notice shall be
	included in all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
	EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
	MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
	IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
	CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
	TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.	

	===================================================================

	SERVER REQUIREMENTS / INSTRUCTIONS
		Requires:   mod_rewrite, xsendfile

	   To setup XSendFile, you need to add the following lines to your MAIN apache conf
        if not using any VirtualHosts,  or your main VirtualHost conf inside the VirtualHost tags

        	XSendFile on
        	XSendFilePath /path/to/this/script/without/filename/

	   It is important to ensure that XSendFilePath is the full path from / or equivalent, and ends
        in a trailing /


	INSTRUCTIONS TO GENERATE PRIVATE/PUBLIC KEY PAIR
	
	  COMMAND LINE
		openssl genrsa -des3 -out PRIVATE_KEY.pem 2048
		openssl rsa -in PRIVATE_KEY.pem -out PRIVATE_KEY.pem
		openssl rsa -in PRIVATE_KEY.pem -outform PEM -pubout -out PUBLIC_KEY.pem

	  ALTERNATIVE (THIS WORKS IN SOME SYSTEMS)
		
		$keys = URLSigner::generate();
		echo '<pre>' . $keys->private_key . '</pre><br>';
		echo '<pre>' . $keys->public_key . '</pre><br>';

		
	  TODO
		Copy the contents of 'PRIVATE_KEY.pem' to the define('PRIVATE_KEY') value
		Copy the contents of 'PUBLIC_KEY.pem' to the define('PUBLIC_KEY') value

	INSTRUCTIONS TO USE THIS SCRIPT
	
		In the file you want to generate URL's from :
		
			require('/path/to/protected.php');
		
		To generate a signed url :
		
			define('URLSIGNINCLUDE', true);
			$link = URLSigner::sign( [your url to sign], [expires in minutes] );
			
		Verification is done automatically, but if you wish to manually
		verify a signed URL, you must pass the entire URL including query
		string as follows :
		
			define('URLSIGNINCLUDE', true);
			$bool = URLSigner::sign( [your url to verify] );
		
*/
if(!defined('PRIVATE_KEY')) {
	define('PRIVATE_KEY', '-----BEGIN RSA PRIVATE KEY-----
MIIEpQIBAAKCAQEAx6ux0PNiW6QcKqtXxjQJQrv0D4hLkoHdLzNuwvxSQpwF7YkZ
1E7DfGsDUV0hZkc2vuIKIq1wBL/q5BL4lqH2fxotBI9VJf7ldYVqywk/5lEDymxo
g7DmQhUid688xbUCtUUBbZ88jY1x+/rhgf7wwHuV95X5Z5dGwXdO8z64DjWqgb8w
PIiMHuCxm9/KMm3O9fzrzC80oHzXMmJRZ/tPp2odV6xQh5Y3TkzFn6quod5loTiS
sN1Ue9n9QqPVlQJD9yKiAfeg+YdRMfuYI1Vw4cJ+r2iKAuNs+GtQOW3b1VV8hPQe
MSwWShMq8YTm7IAaUaLGEwfMOuBW06OeV+i91wIDAQABAoIBAQCR6wG56APbYOVM
sYcly+VwpZbIuxwvZ0RTOE0bpfYfw5H5c5Yyt5TZGgOEtICyFB0IBnzNtt4EOpTY
NJ0CyD4xyNlZWb4qVEswRV40HwBZup8AkZUXmHHNnVBhEulgutXNzy4qBJLmB5Zj
RYcDz2H16NtB4pIviDgnLp+91/n+NyLFvyPLvZFltewKQ/+7ZsYDcxwTlOtOb1Sz
2ZTL250oL18Gweo82Uuxl6ipHmUfVU2J66ZsXm6A0uA9o3Qrgc4BiI+5mstIJuAO
lawGL2XlbZ2y+kvyVdpmNtgdvLgR2eUCPxk/5uL5aDjRWUBAMp8ERBOKNGgbeOun
9nkuCFwBAoGBAPGJOv40qD3bXdtpv2dMWyEO2CvGELRA+kkBDxFrsQWJw/9pr0PD
5gvtKxPAcZdXDkvR21YayjIxyt1mbuDzdpoIh0IZkiJ3TZ3wHUnjKytVjQqec5IC
cJpQhPz21hd2NbjngwIk7ztaTLf+0wkDmrv81oae93FLxI6+KWDVGu8BAoGBANOg
qgff0rkVtPb4n5GLKQD0Vwzk2zvG7J5GdVZ4W/y4JFScFdMY0/fhLuVc5Gl7A1hn
3YSjecx2CBAsv7+aJeUd8MBKWUJ/xsdWJjdEbwJ7sedN4XxzbGpcdmVzrZj/gq03
10S9hkV2sNUDIBitWLMJRqGT8UxdI1Vk12ZgjgTXAoGBAOHQUeYNpulF6ObUY80Y
lu4+KZ4rK7zKLvUH12WLEFJELYjh7qjlQnMOBcMOnWRHUKdUCMLkgvsQkEATn0AS
fmSd6o7Cx1wPu/IX5doJV3fJIPa3kwcD3vB2rQ6vWxNOQgWf9FyR2VPdJXKz++sm
goiUZqAviNlUY+ysHpVYRzkBAoGBAIlZ4mEf9J0pqH0OWkpVHnS/IOx+cIe4kQQc
yLUpgtJgFTxQ3Z1XpONh5FT62EhZjY9IQi5/B2MbTBprYLwTaPruVr4Gwy30zme7
0yvVn5LmA04TbwCdzUSu5CzuSkJdu0t/TZkQxN+6rARkdeVuRH5Wy9+8rESawn7+
5wpMKoCbAoGAWjwthstDp0oD21TofoUHPtbxovKg+0ygiPON9paUufI7/I7duKX7
lGl6Q8FW++2fsVVxfhQSNtvnqgJrJxggWwAJ9jZGhwb+3sjKI6gyFBHkL4rKEhyf
YxGF1ZZ5ccmwDULVfefXq44oc4u20+TRig2XzGo4mp72+tc/zqCL4Q0=
-----END RSA PRIVATE KEY-----');
}

if(!defined('PUBLIC_KEY')) {
	define('PUBLIC_KEY', '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAx6ux0PNiW6QcKqtXxjQJ
Qrv0D4hLkoHdLzNuwvxSQpwF7YkZ1E7DfGsDUV0hZkc2vuIKIq1wBL/q5BL4lqH2
fxotBI9VJf7ldYVqywk/5lEDymxog7DmQhUid688xbUCtUUBbZ88jY1x+/rhgf7w
wHuV95X5Z5dGwXdO8z64DjWqgb8wPIiMHuCxm9/KMm3O9fzrzC80oHzXMmJRZ/tP
p2odV6xQh5Y3TkzFn6quod5loTiSsN1Ue9n9QqPVlQJD9yKiAfeg+YdRMfuYI1Vw
4cJ+r2iKAuNs+GtQOW3b1VV8hPQeMSwWShMq8YTm7IAaUaLGEwfMOuBW06OeV+i9
1wIDAQAB
-----END PUBLIC KEY-----');
}

if(!defined('URLSIGNINCLUDE')) {
	if(URLSigner::verify(URLSigner::current()) === true) {
		$file = (object)parse_url(URLSigner::current());
		$file = __DIR__ . DIRECTORY_SEPARATOR . basename($file->path);
		URLSigner::push($file);
	} else {
		echo 'Invalid signature';

	}
}


class URLSigner {
	
	static function generate($bits = 4096) {
		$keys = openssl_pkey_new(
			Array(
				'digest_alg' => 'sha256',
				'private_key_bits' => $bits,
				'private_key_type' => OPENSSL_KEYTYPE_RSA
			)
		);

		if($keys === false) {
			return openssl_error_string();
		}
		
		openssl_pkey_export($keys, $private_key);
		
		$public_key = openssl_pkey_get_details($keys);
		$public_key = $public_key['key'];
		
		return (object)Array(
			'private_key'=>$private_key,
			'public_key'=>$public_key
		);
	}
	
	static function sign($url, $expire = 5) {
		$signature = '';
		$expire = time() + ($expire * 60);
		openssl_sign(
			json_encode(
				Array(
					'expires'=>(int)$expire,
					'url'=>(string)$url
				)
			),
			$signature,
			PRIVATE_KEY,
			OPENSSL_ALGO_SHA256
		);
		
		return sprintf('%s?Expires=%s&Hash=%s',
						$url,
						$expire, 
						str_replace(
							array('+', '=', '/'),
							array('-', '_', '~'),
							base64_encode($signature)
						)
					);
	}
	
	static function current() {
		$port = '';
		if (($_SERVER['REQUEST_SCHEME'] == 'https' && (int)$_SERVER['SERVER_PORT'] != 443) || ($_SERVER['REQUEST_SCHEME'] == 'http' && (int)$_SERVER['SERVER_PORT'] != 80) ) { $port = ':' . $_SERVER['SERVER_PORT']; }
		
		return rtrim(sprintf(
			'%s://%s%s%s?%s',
			$_SERVER['REQUEST_SCHEME'],
			$_SERVER['HTTP_HOST'],
			$port,
			$_SERVER['REQUEST_URI'],
			$_SERVER['REDIRECT_QUERY_STRING']
		),'?');
		
	}
	
	static function verify($url) {
		try{
			/* Parse url for values */
			$r = (object)parse_url($url);
			$u = $r->scheme . '://' . $r->host . $r->path;

			parse_str($r->query, $q);

			/* Verify time hasn't expired */
			$i = (int)$q['Expires'];
			if((time() - $i) >= 0) { return false; }
			
			/* Validate signature */
			$result = openssl_verify(
								json_encode(
									Array(
										'expires'=>(int)$i,
										'url'=>(string)$u
									)
								),
								base64_decode(
									str_replace(
										array('-', '_', '~'),
										array('+', '=', '/'),
										$q['Hash']
									)
								),
								PUBLIC_KEY,
								OPENSSL_ALGO_SHA256
						);
			if($result === 1) { return true; }
						
		} catch(Exception $e) {
			var_export($e);
			return false;
		}
		return false;
	}
	
	static function push($file) {
		if(file_exists($file)) {
			header("X-Sendfile: $file");
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
			header("Content-Length: " . filesize($file));
			exit;
		} else {
			header('HTTP/1.0 404 Not Found', true, 404);
			echo 'File has been removed';
		}
	}
	
}

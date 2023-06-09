<?php
/*
 * test_http.php
 *
 * @(#) $Header: /opt2/ena/metal/http/test_http.php,v 1.19 2011/01/27 02:18:18 mlemos Exp $
 *
 */

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<TITLE>Test for Manuel Lemos' PHP HTTP class</TITLE>
</HEAD>
<BODY>
<H1 align="center">Test for Manuel Lemos' PHP HTTP class</H1>
<HR>
<UL>
<?php
	require("http.php");

	/* Uncomment the line below when accessing Web servers or proxies that
	 * require authentication.
	 */
	/*
	require("sasl.php");
	*/

	set_time_limit(0);
	$http=new http_class;

	/* Connection timeout */
	$http->timeout=0;

	/* Data transfer timeout */
	$http->data_timeout=0;

	/* Output debugging information about the progress of the connection */
	$http->debug=1;

	/* Format dubug output to display with HTML pages */
	$http->html_debug=1;


	/*
	 *  Need to emulate a certain browser user agent?
	 *  Set the user agent this way:
	 */
	$http->user_agent="Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)";

	/*
	 *  If you want to the class to follow the URL of redirect responses
	 *  set this variable to 1.
	 */
	$http->follow_redirect=1;

	/*
	 *  How many consecutive redirected requests the class should follow.
	 */
	$http->redirection_limit=5;

	/*
	 *  If your DNS always resolves non-existing domains to a default IP
	 *  address to force the redirection to a given page, specify the
	 *  default IP address in this variable to make the class handle it
	 *  as when domain resolution fails.
	 */
	$http->exclude_address="";

	/*
	 *  If you want to establish SSL connections and you do not want the
	 *  class to use the CURL library, set this variable to 0 .
	 */
	$http->prefer_curl=0;

	/*
	 *  If basic authentication is required, specify the user name and
	 *  password in these variables.
	 */

	$user="";
	$password="";
	$realm="";       /* Authentication realm or domain      */
	$workstation=""; /* Workstation for NTLM authentication */
	$authentication=(strlen($user) ? UrlEncode($user).":".UrlEncode($password)."@" : "");

/*
	Do you want to access a page via SSL?
	Just specify the https:// URL.
	$url="https://www.openssl.org/";
*/

	$url="https://".$authentication."www.php.net/";

	/*
	 *  Generate a list of arguments for opening a connection and make an
	 *  HTTP request from a given URL.
	 */
	$error=$http->GetRequestArguments($url,$arguments);

	if(strlen($realm))
		$arguments["AuthRealm"]=$realm;

	if(strlen($workstation))
		$arguments["AuthWorkstation"]=$workstation;

	$http->authentication_mechanism=""; // force a given authentication mechanism;

	/*
	 *  If you need to access a site using a proxy server, use these
	 *  arguments to set the proxy host and authentication credentials if
	 *  necessary.
	 */
	/*
	$arguments["ProxyHostName"]="127.0.0.1";
	$arguments["ProxyHostPort"]=3128;
	$arguments["ProxyUser"]="proxyuser";
	$arguments["ProxyPassword"]="proxypassword";
	$arguments["ProxyRealm"]="proxyrealm";  // Proxy authentication realm or domain
	$arguments["ProxyWorkstation"]="proxyrealm"; // Workstation for NTLM proxy authentication
	$http->proxy_authentication_mechanism=""; // force a given proxy authentication mechanism;
	*/

	/*
	 *  If you need to access a site using a SOCKS server, use these
	 *  arguments to set the SOCKS host and port.
	 */
	/*
	$arguments["SOCKSHostName"]='127.0.0.1';
	$arguments["SOCKSHostPort"]=1080;
	$arguments["SOCKSVersion"]='5';
	*/

	/* Set additional request headers */
	$arguments["Headers"]["Pragma"]="nocache";
/*
	Is it necessary to specify a certificate to access a page via SSL?
	Specify the certificate file this way.
	$arguments["SSLCertificateFile"]="my_certificate_file.pem";
	$arguments["SSLCertificatePassword"]="some certificate password";
*/

/*
	Is it necessary to preset some cookies?
	Just use the SetCookie function to set each cookie this way:

	$cookie_name="LAST_LANG";
	$cookie_value="de";
	$cookie_expires="2010-01-01 00:00:00"; // "" for session cookies
	$cookie_uri_path="/";
	$cookie_domain=".php.net";
	$cookie_secure=0; // 1 for SSL only cookies
	$http->SetCookie($cookie_name, $cookie_value, $cookie_expiry, $cookie_uri_path, $cookie_domain, $cookie_secure);
*/


	echo "<LI><H2>Opening connection to:</H2>\n<P><TT>",HtmlSpecialChars($arguments["HostName"]),"</TT></P>\n";
	flush();
	$error=$http->Open($arguments);
	echo "</LI>\n";

	if($error=="")
	{
		echo "<LI><H2>Sending request for page:</H2>\n<P><TT>";
		echo HtmlSpecialChars($arguments["RequestURI"]),"\n";
		if(strlen($user))
			echo "\nLogin:    ",$user,"\nPassword: ",str_repeat("*",strlen($password));
		echo "</TT></P>\n";
		flush();
		$error=$http->SendRequest($arguments);
		echo "</LI>\n";

		if($error=="")
		{
			echo "<LI><H2>Request:</H2>\n<PRE>\n".HtmlSpecialChars($http->request)."</PRE></LI>\n";
			echo "<LI><H2>Request headers:</H2>\n<PRE>\n";
			for(Reset($http->request_headers),$header=0;$header<count($http->request_headers);Next($http->request_headers),$header++)
			{
				$header_name=Key($http->request_headers);
				if(GetType($http->request_headers[$header_name])=="array")
				{
					for($header_value=0;$header_value<count($http->request_headers[$header_name]);$header_value++)
						echo $header_name.": ".$http->request_headers[$header_name][$header_value],"\r\n";
				}
				else
					echo $header_name.": ".$http->request_headers[$header_name],"\r\n";
			}
			echo "</PRE>\n";
			flush();

			$headers=array();
			$error=$http->ReadReplyHeaders($headers);
			echo "</LI>\n";
			if($error=="")
			{
				echo "<LI><H2>Response status code:</H2>\n<P>".$http->response_status;
				switch($http->response_status)
				{
					case "301":
					case "302":
					case "303":
					case "307":
						echo " (redirect to <TT>".$headers["location"]."</TT>)<BR>\nSet the <TT>follow_redirect</TT> variable to handle redirect responses automatically.";
						break;
				}
				echo "</P></LI>\n";
				echo "<LI><H2>Response headers:</H2>\n<PRE>\n";
				for(Reset($headers),$header=0;$header<count($headers);Next($headers),$header++)
				{
					$header_name=Key($headers);
					if(GetType($headers[$header_name])=="array")
					{
						for($header_value=0;$header_value<count($headers[$header_name]);$header_value++)
							echo $header_name.": ".$headers[$header_name][$header_value],"\r\n";
					}
					else
						echo $header_name.": ".$headers[$header_name],"\r\n";
				}
				echo "</PRE></LI>\n";
				flush();

				echo "<LI><H2>Response body:</H2>\n<PRE>\n";

				/*
					You can read the whole reply body at once or
					block by block to not exceed PHP memory limits.
				*/

				/*
				$error = $http->ReadWholeReplyBody($body);
				if(strlen($error) == 0)
					echo HtmlSpecialChars($body);
				*/

				for(;;)
				{
					$error=$http->ReadReplyBody($body,1000);
					if($error!=""
					|| strlen($body)==0)
						break;
					echo HtmlSpecialChars($body);
				}

				echo "</PRE></LI>\n";
				flush();
			}
		}
		$http->Close();
	}
	if(strlen($error))
		echo "<H2 align=\"center\">Error: ",$error,"</H2>\n";
?>
</UL>
<HR>
</BODY>
</HTML>

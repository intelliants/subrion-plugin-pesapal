<?php

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaPesapal = $iaCore->factoryModule('pesapal', 'pesapal', 'common');

    $iaPesapal->load();

    $credentials = $iaPesapal->getCredentials();
//pesapal params
    $token = $params = NULL;

    /*
    PesaPal Sandbox is at http://demo.pesapal.com. Use this to test your developement and
    when you are ready to go live change to https://www.pesapal.com.
    */
    $consumer_key = $credentials['key'];//Register a merchant account on
//demo.pesapal.com and use the merchant key for testing.
//When you are ready to go live make sure you change the key to the live account
//registered on www.pesapal.com!
    $consumer_secret = $credentials['secret'];// Use the secret from your test
//account on demo.pesapal.com. When you are ready to go live make sure you
//change the secret to the live account registered on www.pesapal.com!
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();
    $iframelink = 'https://demo.pesapal.com/api/PostPesapalDirectOrderV4';//change to
//https://www.pesapal.com/API/PostPesapalDirectOrderV4 when you are ready to go live!

//get form details
    $amount = $_POST['amount'];
    $amount = number_format($amount, 2);//format amount to 2 decimal places

    var_dump($amount);

    $desc = $_POST['description'];
    $type = $_POST['type']; //default value = MERCHANT
    $reference = $_POST['reference'];//unique order id of the transaction, generated by merchant
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phonenumber = '';//ONE of email or phonenumber is required


    $callback_url = IA_URL . 'completed/';

    /*$post_xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><PesapalDirectOrderInfo xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" Amount=\"".$amount."\" Description=\"".$desc."\" Type=\"".$type."\" Reference=\"".$reference."\" FirstName=\"".$first_name."\" LastName=\"".$last_name."\" Email=\"".$email."\" PhoneNumber=\"".$phonenumber."\" xmlns=\"http://www.pesapal.com\" />";*/

    $post_xml = '<?xml version="1.0" encoding="utf-8"?>';
    $post_xml .= '<PesapalDirectOrderInfo ';
    $post_xml .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
    $post_xml .= 'xmlns:xsd="http://www.w3.org/2001/XMLSchema" ';
    $post_xml .= 'Amount="' . $amount . '" ';
    $post_xml .= 'Description="' . $desc . '" ';
    $post_xml .= 'Type="' . $type . '" ';
    $post_xml .= 'Reference="' . $reference . '" ';
    $post_xml .= 'FirstName="' . $first_name . '" ';
    $post_xml .= 'LastName="' . $last_name . '" ';
    $post_xml .= 'Email="' . $email . '" ';
    $post_xml .= 'PhoneNumber="' . $phonenumber . '" ';
    $post_xml .= 'xmlns="http://www.pesapal.com" />';
    $post_xml = htmlentities($post_xml);

    // $post_xml = htmlentities($post_xml);

    $consumer = new OAuthConsumer($consumer_key, $consumer_secret);

//post transaction to pesapal
    $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
    $iframe_src->set_parameter("oauth_callback", $callback_url);
    $iframe_src->set_parameter("pesapal_request_data", $post_xml);
    $iframe_src->sign_request($signature_method, $consumer, $token);

//display pesapal - iframe and pass iframe_src

/*    $iframe = '<iframe src="<?php echo $iframe_src;?>" width="100%" height="700px"  scrolling="no" frameBorder="0">*/
//    <p>Browser unable to load iFrame</p>
//</iframe>';
    $iaView->iaSmarty->assign('frame', $iframe_src);

    // var_dump($iframe_src);
}
?>


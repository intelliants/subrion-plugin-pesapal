<?php

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaPesapal = $iaCore->factoryModule('pesapal', 'pesapal', 'common');

    $iaPesapal->load();
    $credentials = $iaPesapal->getCredentials();
    $iframelink = $iaPesapal->getIframeLink();
    $post_xml = $iaPesapal->createPostXml($_POST);

    $callback_url = IA_URL . 'pay/' . $_POST['reference'] . '/completed/';

    $token = $params = null;
    $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

    $consumer = new OAuthConsumer($credentials['key'], $credentials['secret']);

    $iframe_src = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $iframelink, $params);
    $iframe_src->set_parameter("oauth_callback", $callback_url);
    $iframe_src->set_parameter("pesapal_request_data", $post_xml);
    $iframe_src->sign_request($signature_method, $consumer, $token);

    $iaView->iaSmarty->assign('frame', $iframe_src);
}

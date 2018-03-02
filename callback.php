<?php

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaPesapal = $iaCore->factoryModule('pesapal', 'pesapal', 'common');
    $iaTransaction = $iaCore->factory('transaction');

    $iaPesapal->load();
    $credentials = $iaPesapal->getCredentials();
    $statusrequestAPI = $iaPesapal->getApiLink();

    $pesapalNotification = $_GET['pesapal_notification_type'];
    $pesapalTrackingId = $_GET['pesapal_transaction_tracking_id'];
    $pesapal_merchant_reference = $_GET['pesapal_merchant_reference'];

    if ($pesapalNotification == "CHANGE" && $pesapalTrackingId != '') {
        $token = $params = null;
        $consumer = new OAuthConsumer($credentials['key'], $credentials['secret']);
        $signature_method = new OAuthSignatureMethod_HMAC_SHA1();

        $request_status = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $statusrequestAPI, $params);
        $request_status->set_parameter("pesapal_merchant_reference", $pesapal_merchant_reference);
        $request_status->set_parameter("pesapal_transaction_tracking_id", $pesapalTrackingId);
        $request_status->sign_request($signature_method, $consumer, $token);

        $status = $iaPesapal->checkIpnMessage($request_status);

        if (!$status) {
            $iaTransaction->addIpnLogEntry($iaPesapal->getModuleName(), $_GET, 'Invalid');
            return iaView::errorPage(iaView::ERROR_NOT_FOUND);
        }

        if ($iaPesapal->updateTransaction($pesapalTrackingId, $status)) {
            $resp = "pesapal_notification_type=$pesapalNotification&pesapal_transaction_tracking_id=$pesapalTrackingId&pesapal_merchant_reference=$pesapal_merchant_reference";
            ob_start();
            echo $resp;
            ob_flush();
            exit;
        }
    }
}

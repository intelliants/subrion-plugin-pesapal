<?php
/******************************************************************************
 *
 * Subrion - open source content management system
 * Copyright (C) 2018 Intelliants, LLC <https://intelliants.com>
 *
 * This file is part of Subrion.
 *
 * Subrion is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Subrion is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Subrion. If not, see <http://www.gnu.org/licenses/>.
 *
 *
 * @link https://subrion.org/
 *
 ******************************************************************************/

if (iaView::REQUEST_HTML == $iaView->getRequestType()) {
    $iaPesapal = $iaCore->factoryModule('pesapal', IA_CURRENT_MODULE, 'common');
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

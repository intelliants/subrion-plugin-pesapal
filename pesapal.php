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

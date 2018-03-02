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

class iaPesapal extends abstractCore
{
    protected $_isDemoMode;
    protected static $_table = 'payment_transactions';
    protected $_moduleName = 'pesapal';

    public function init()
    {
        parent::init();

        $this->_isDemoMode = (bool)$this->iaCore->get('pesapal_demo_mode');
    }

    public function getModuleName()
    {
        return $this->_moduleName;
    }

    public function getCredentials()
    {
        $type = $this->_isDemoMode ? 'demo' : 'live';

        $key = sprintf('pesapal_key_%s', $type);
        $secret = sprintf('pesapal_secret_%s', $type);

        $credentials['key'] = $this->iaCore->get($key);
        $credentials['secret'] = $this->iaCore->get($secret);

        return $credentials;
    }

    public function getIframeLink()
    {
        return $this->_isDemoMode ? 'https://demo.pesapal.com/api/PostPesapalDirectOrderV4' : 'https://www.pesapal.com/API/PostPesapalDirectOrderV4';
    }

    public function getApiLink()
    {
        return $this->_isDemoMode ? 'https://demo.pesapal.com/api/querypaymentstatus' : 'https://pesapal.com/api/querypaymentstatus';
    }

    public function load()
    {
        $basePath = IA_MODULES . 'pesapal/includes/lib/';

        require $basePath . 'OAuth.php';
    }

    public function updateTransaction($reference_id, $status)
    {
        $iaTransaction = $this->iaCore->factory('transaction');

        switch ($status) {
            case 'COMPLETED':
                $transaction['status'] = iaTransaction::PASSED;
                break;
            case 'PENDING':
                $transaction['status'] = iaTransaction::PENDING;
                break;
            case 'FAILED':
            case 'INVALID':
                $transaction['status'] = iaTransaction::FAILED;
                break;
        }

        $txn = $iaTransaction->getBy('reference_id', $reference_id);

        return $iaTransaction->update($transaction, $txn['id']);
    }

    public function checkIpnMessage($request_status)
    {
        if (!($ch = curl_init($request_status))) {
            return false;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if (defined('CURL_PROXY_REQUIRED')) if (CURL_PROXY_REQUIRED == 'True') {
            $proxy_tunnel_flag = (defined('CURL_PROXY_TUNNEL_FLAG') && strtoupper(CURL_PROXY_TUNNEL_FLAG) == 'FALSE') ? false : true;
            curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, $proxy_tunnel_flag);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($ch, CURLOPT_PROXY, CURL_PROXY_SERVER_DETAILS);
        }

        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $raw_header = substr($response, 0, $header_size - 4);
        $headerArray = explode("\r\n\r\n", $raw_header);
        $header = $headerArray[count($headerArray) - 1];

        $elements = preg_split("/=/", substr($response, $header_size));
        $status = $elements[1];

        curl_close($ch);

        return $status;
    }

    public function createPostXml($post)
    {
        $amount = $post['amount'];
        $amount = number_format($amount, 2);
        $desc = $post['description'];
        $type = $post['type'];
        $reference = $post['reference'];
        $first_name = $post['first_name'];
        $last_name = $post['last_name'];
        $email = $post['email'];
        $phonenumber = isset($post['phonenumber']) ? $post['phonenumber'] : '';

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

        return htmlentities($post_xml);
    }
}

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

$iaPesapal = $iaCore->factoryModule('pesapal', 'pesapal', 'common');

$reference = null;
$pesapal_tracking_id = null;

if (isset($_GET['pesapal_merchant_reference']) && isset($_GET['pesapal_transaction_tracking_id'])) {
    $reference = $_GET['pesapal_merchant_reference'];
    $pesapal_tracking_id = $_GET['pesapal_transaction_tracking_id'];

    $transaction = $temp_transaction;

    $transaction['reference_id'] = $pesapal_tracking_id;

    $member = $iaUsers->getInfo($transaction['member_id']);

    $order['payment_gross'] = $transaction['amount'];
    $order['mc_currency'] = $transaction['currency'];
    $order['payment_date'] = $transaction['date_created'];
    $order['payment_status'] = iaLanguage::get($transaction['status']);
    $order['first_name'] = ($member['fullname'] ? $member['fullname'] : $member['username']);
    $order['last_name'] = '';
    $order['payer_email'] = $member['email'];
    $order['txn_id'] = $pesapal_tracking_id;

    $iaView->setMessages(iaLanguage::get('pesapal_payment_completed'), iaView::SUCCESS);
}

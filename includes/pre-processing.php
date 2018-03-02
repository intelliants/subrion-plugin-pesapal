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
$iaUser = $iaCore->factory('users');

$current_user = $iaUser::getIdentity();

$iaView->iaSmarty->assign('transaction', $transaction);
$iaView->iaSmarty->assign('user_email', $current_user->email);
$iaView->iaSmarty->assign('user_phone', $current_user->phone);

$content = $iaView->iaSmarty->fetch('module:pesapal/details-form.tpl');

$iaView->title(iaLanguage::get('pesapal_title'));

$iaView->assign('protect', false);
$iaView->assign('content', $content);

$tplFile = 'page';

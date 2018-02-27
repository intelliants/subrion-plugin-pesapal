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


    public function init()
    {
        parent::init();

        $this->_isDemoMode = (bool)$this->iaCore->get('pesapal_demo_mode');
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

    public function load()
    {
        $basePath = IA_MODULES . 'pesapal/includes/lib/';

        require $basePath . 'OAuth.php';
    }
}
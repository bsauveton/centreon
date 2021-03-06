<?php
/*
 * Copyright 2005-2014 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 *
 */

namespace CentreonSecurity\Internal;

use CentreonAdministration\Repository\UserRepository;
use Centreon\Internal\Exception;

/**
 * Class for authentication
 *
 * @authors Maximilien Bersoult
 * @package Centreon
 * @subpackage Core
 */
class Auth
{
    /*
     * Declare Values
     */
    protected $login;
    protected $password;
    protected $autologin;
    public $userInfos;
    protected $debug;
    /*
     * Error Message
     */
    protected $error;

    /**
     * Constructor
     * 
     * @param string $username The username for authentication
     * @param string $password The password
     * @param boolean $autologin If the authentication is by autologin
     * @param string $token The token string
     */
    public function __construct($username, $password, $autologin, $token = "")
    {
        $this->login = $username;
        $this->password = $password;
        $this->autologin = $autologin;
        $this->debug = false;
        /*if (1 === Di::getDefault()->get('config')->get('default', 'debug_auth', 0)) {
            $this->debug = true;
        }*/
        $this->checkUser($username, $password, $token);
    }
    
    /**
     * Check user password
     *
     * @param $username string The username
     * @param $password string The password
     * @param $token string The token
     */
    protected function checkUser($username, $password, $token)
    {
        //$logger = \Monolog\Registry::getInstance('MAIN');
        
        try {
            $login = htmlentities($username, ENT_QUOTES, "UTF-8");
            if ($this->autologin == 0 || ($this->autologin && $token != "")) {
                $this->userInfos = UserRepository::checkUser($login, $password);
            } else {
                $this->userInfos = UserRepository::checkUser($login, $password, $token);
            }
            //$logger->debug("Contact '" . $login . "' logged in - IP : " . filter_input(INPUT_SERVER, "REMOTE_ADDR"));
        } catch (Exception $e) {
            if ($this->debug) {
                //$logger->debug($e->getMessage());
            }
            throw new \Centreon\Internal\Exception($e->getMessage(), $e->getCode());
        }
    }
}

<?php
/*
CREATE TABLE IF NOT EXISTS `payments` (
`inv_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Invoice ID',
`sum` int(11) NOT NULL DEFAULT '0' COMMENT 'money',
`email` varchar(255) DEFAULT NULL COMMENT 'email for recurrent payments',
`recurrent` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'is this recurrent?',
`success` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'successful or not payment',
`canceled` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'canceled or not for recurrent payments',
`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'date when first payment was success',
`desc` varchar(255) NOT NULL COMMENT 'payment description',
`params` varchar(512) DEFAULT NULL COMMENT 'shp_params',
`redirect` varchar(512) DEFAULT NULL COMMENT 'first redirect url for once type of payments',
`last_recurrent` timestamp NULL DEFAULT NULL COMMENT 'date of last recurrent payment',
PRIMARY KEY (`inv_id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=utf8 COMMENT='Recurrent payments';

CREATE TABLE IF NOT EXISTS `recurrents` (
`id` int(11) NOT NULL AUTO_INCREMENT,
`PreviousInvoiceID` int(11) NOT NULL DEFAULT '0',
`inv_id` int(11) NOT NULL DEFAULT '0',
`sum` int(11) NOT NULL DEFAULT '0',
`timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
 */
class RobokassaRecurrent extends Robokassa
{
    const DB_HOST = 'localhost';
    const DB_NAME = 'your_db_name';
    const DB_USER = 'your_db_user';
    const DB_PWD  = 'your_db_password';
    /**
     * @return mixed
     */
    private function getConnection()
    {
        try {
            $username   = self::DB_USER;
            $password   = self::DB_PWD;
            $host       = self::DB_HOST;
            $db         = self::DB_NAME;
            $connection = new PDO("mysql:dbname=$db;host=$host", $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            if ($this->Debug) {
                $this->debug('Подключение не удалось: ' . $e->getMessage());
            }

            echo 'Подключение не удалось: ' . $e->getMessage();
        }
    }
    /**
     * @param $sum
     * @param $desc
     * @param $invid
     * @param array           $params
     * @param $IncCurrLabel
     * @param $recurrent
     */
    public function doRecurrentRedirect($sum = 100, $desc = '', $invid = '0', $params = array(), $IncCurrLabel = 'ru', $recurrent = 1)
    {
        $recurrent = (int) $recurrent;
        if ($recurrent > 0) {
            $IncCurrLabel = 'BANKOCEAN3R';
        }

        $invid    = $this->insertPayment($sum, $params['email'], $recurrent, '', $desc, $params);
        $redirect = $this->getPayment($sum, $desc, $invid, $params, $IncCurrLabel);
        if ($recurrent > 0) {
            $redirect .= '&Recurring=1';
        }

        $this->updateRedirectField($invid, $redirect);
        if ($this->Debug) {
            $this->debug($invid, 'PAYMENT_RELEASE_INVID');
        }

        header("X-Redirect: Powered by neatek");
        header("Location: " . $redirect);
    }
    /**
     * @param  $sum
     * @param  $email
     * @param  $recurrent
     * @param  false        $redirect_url
     * @param  $desc
     * @param  array        $params
     * @param  $date
     * @return mixed
     */
    public function insertPayment($sum = 0.0, $email = '', $recurrent = false, $redirect_url = '', $desc = '', $params = array(), $date = '')
    {
        if (empty($date)) {
            $date = date("Y-m-d H:i:s");
        }

        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO `payments` (`sum`,`email`,`recurrent`,`redirect`,`desc`,`params`,`timestamp`) VALUES (:sum, :email, :recurrent, :redirect, :description, :params, :cdate)');
        if (is_bool($recurrent)) {
            if ($recurrent == true) {
                $recurrent = 1;
            } else {
                $recurrent = 0;
            }

        }
        $stmt->execute(array(
            'sum'         => strip_tags($sum),
            'email'       => strip_tags($email),
            'recurrent'   => $recurrent,
            'redirect'    => $redirect_url,
            'description' => $desc,
            'params'      => json_encode($params),
            'cdate'       => $date,
        ));
        return $conn->lastInsertId();
    }
    /**
     * @param  $sum
     * @param  $prev_invid
     * @param  $invid
     * @return mixed
     */
    public function insertRecurrent($sum = 0.0, $prev_invid = 0, $invid = 0)
    {
        if (empty($date)) {
            $date = date("Y-m-d H:i:s");
        }

        $conn = $this->getConnection();
        $stmt = $conn->prepare('INSERT INTO `recurrents` (`PreviousInvoiceID`, `inv_id`, `sum`, `timestamp`) VALUES (:PreviousInvoiceID, :inv_id, :sum, :ctimestamp);');
        $stmt->execute(array(
            'PreviousInvoiceID' => $prev_invid,
            'inv_id'            => $invid,
            'sum'               => $sum,
            'ctimestamp'        => $date,
        ));
        return $conn->lastInsertId();
    }
    /**
     * @param $invid
     */
    public function setPaymentSuccess($invid = 0)
    {
        if ($this->Debug) {
            $this->debug($invid, 'SET_PAYMENT_SUCCESS');
        }

        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE `payments`SET success=1 WHERE inv_id=:invid');
        $stmt->execute(array(
            'invid' => $invid,
        ));
    }
    /**
     * @param $invid
     * @param $redirect_url
     */
    public function updateRedirectField($invid = 0, $redirect_url = '')
    {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE `payments`SET redirect=:redirect WHERE inv_id=:invid');
        $stmt->execute(array(
            'invid'    => $invid,
            'redirect' => $redirect_url,
        ));
    }
    /**
     * @return mixed
     */
    public function getLatestForRecurrent()
    {
        $conn       = $this->getConnection();
        $prev_month = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . '-1 months')); // Only for 1 month / Recurrent Period
        $sql        = "SELECT * FROM `payments` WHERE (`timestamp` < '" . $prev_month . "' AND `last_recurrent` IS NULL) OR `last_recurrent` < '" . $prev_month . "' AND success=1 AND recurrent=1 AND canceled=0 LIMIT 20";
        if ($this->Debug) {
            $this->debug($sql, 'SQL_GET_RECURRENTS');
        }

        $stmt     = $conn->query($sql);
        $payments = array();
        while ($row = $stmt->fetch()) {
            if ($this->Debug) {
                $this->debug(print_r($row, true), 'DO_RECURRENT');
            }

            $payments[] = $row;
        }
        return $payments;
    }
    /**
     * @param $invid
     */
    public function updateLastRecurrent($invid = 0)
    {
        $conn = $this->getConnection();
        $stmt = $conn->prepare('UPDATE `payments`SET last_recurrent=:date_recurrent WHERE inv_id=:invid');
        $stmt->execute(array(
            'date_recurrent' => date('Y-m-d H:i:s'),
            'invid'          => $invid,
        ));
        if ($this->Debug) {
            $this->debug(print_r($invid, true) . "\r\n" . date('Y-m-d H:i:s'), 'UPDATE_LAST_RECURRENT');
        }

    }
    /**
     * @param  $sum
     * @param  $desc
     * @param  $invid
     * @param  $prev_invid
     * @param  array           $params
     * @param  $IncCurrLabel
     * @return mixed
     */
    public function getRecurrentPayment($sum = 100, $desc = '', $invid = '0', $prev_invid = '0', $params = array(), $IncCurrLabel = 'ru')
    {
        $email = "";if (isset($params['email'])) {
            $email = $params['email'];
        }

        $signature    = $this->genSig($sum, $invid, $params);
        $redirect_url = "https://auth.robokassa.ru/Merchant/Recurring?MrchLogin=" . $this->MerchLogin . "&OutSum=" . $sum . "&InvId=" . $invid . "&PreviousInvoiceID=" . $prev_invid . "&InvDesc=" . urlencode($desc) . "&Email=" . $email . "&SignatureValue=" . $signature;
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $redirect_url .= "&shp_" . $key . "=" . urlencode($value);
            }
        }

        if ($this->Debug) {
            $this->debug('PAYMENT_URL: ' . $redirect_url . "\r\n");
        }

        return $redirect_url;
    }
    /**
     * @param array   $data
     * @param $name
     */
    public function log_errors($data = array(), $name = '')
    {
        file_put_contents('errors' . date('dmY') . '.log', date('[H:i:s] ') . $name . "\r\nRESULT:::\r\n" . print_r($data, true) . "\r\n=====\r\n", FILE_APPEND);
    }
    /**
     * @param  $invid
     * @return mixed
     */
    public function getRecurrent($invid = 0)
    {
        $conn  = $this->getConnection();
        $invid = (int) $invid;
        $sql   = "SELECT * FROM `payments` WHERE inv_id=" . $invid . " AND success=1 AND recurrent=1 AND canceled=0 LIMIT 1";
        $stmt  = $conn->query($sql);
        while ($row = $stmt->fetch()) {
            if (!empty($row)) {
                return $row;
            }

        }
        return null;
    }
    /**
     * @param $invid
     */
    public function cancelRecurrent($invid = 0)
    {
        $invid = (int) $invid;
        $conn  = $this->getConnection();
        $stmt  = $conn->prepare('UPDATE `payments`SET canceled=:can WHERE inv_id=:invid');
        $stmt->execute(array(
            'can'   => 1,
            'invid' => $invid,
        ));
    }
    /**
     * @param  $invid
     * @return mixed
     */
    public function getRecurrentPrevInvId($invid = 0)
    {
        // get prev invid by autoInvid
        $conn  = $this->getConnection();
        $invid = (int) $invid;
        $sql   = "SELECT * FROM `recurrents` WHERE inv_id=" . $invid . " LIMIT 1";
        $stmt  = $conn->query($sql);
        while ($row = $stmt->fetch()) {
            if (!empty($row)) {
                return $row;
            }

        }
        return null;
    }
    public function doRecurrents()
    {
        // function for cron-job every 1min for 20 people
        $recurrents = $this->getLatestForRecurrent();
        foreach ($recurrents as $key => $value) {
            $params = (array) json_decode($value['params']);
            //$params['prev_inv_id'] = $value['inv_id'];
            $invid = $this->insertPayment($value['sum'], $value['email'], 0, 'Automatic Recurrent Request', $value['desc'], $params);
            $url   = $this->getRecurrentPayment($value['sum'], $value['desc'], $invid, $value['inv_id'], $params);
            if ($this->Debug) {
                $this->debug(print_r($url, true), 'RECURRENT_URL');
            }

            $result = file_get_contents($url);
            //$result = 'OK';
            if ($this->Debug) {
                $this->debug(print_r($result, true), 'RECURRENT_ANSWER');
            }

            if (strpos($result, 'OK') !== false) {
                /**set_success_recurrent**/
                $this->setPaymentSuccess($invid);
                /**update_last_recurrent_date**/
                $this->updateLastRecurrent($invid);
                $this->updateLastRecurrent($value['inv_id']);
                /**store_recurrent_success_payment**/
                $this->insertRecurrent($value['sum'], $value['inv_id'], $invid);
            } else {
                $this->log_errors(print_r($result, true), 'ERROR_RECURRENT');
            }
        }
    }
}

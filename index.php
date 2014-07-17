<?php
require_once 'KinveyREST.php';
/**
 * Created by PhpStorm.
 * User: felipeneuhauss
 * Date: 17/07/14
 * Time: 16:14
 */

/**
 * Retrieve
 */

use KinveyREST\KinveyREST;

$kinveyRest = New

$users = $kinveyRest->users(null, null, array('fields' => array('name', 'email', '_id')));
/**
 * Obtem as vendas dos usuarios
 */
foreach ($users as &$user) {
    $user->sales = $kinveyRest->retrieve('Sales',
        '',
        array('_acl.creator' => $user->_id),
        array('sort' => array('_kmd.lmt' => -1),
            'limit' => 1),
        array(''));

    $responses = array();
    if (count($user->sales)) {
        foreach ($user->sales as $sale) {
            # verifica se fez venda a mais de 15 dias
            $lastSaleDate = new \DateTime($sale->_kmd->lmt);
            $today        = new \DateTime('now');
            $diff         = $today->diff($lastSaleDate);
            if ($diff->days >= 15) {
                # Manda e-mail
                $renderer = $this->getServiceLocator()->get('ViewRenderer');
                $content = $renderer->partial('application/notification/notsale-email',  array('user' => $user));
                $responses[] = $mandrill->send($user->email , $user->name,  'Izie - Há 15 dias que você não vende', $content);
            }
        }
    }
}
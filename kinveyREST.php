<?php
/**
 * Created by JetBrains PhpStorm.
 * User: felipeneuhauss
 * Date: 12/11/13
 * Time: 11:58
 * To change this template use File | Settings | File Templates.
 */

namespace KinveyRest;

class KinveyREST
{
    protected $_appKey          = 'kid_eTiEcN6MFO';
    protected $_masterSecret    = '5ea7d50fe928441284ec112880493a4c';
    protected $_baseUrl         = 'https://baas.kinvey.com';
    protected $_consolePrefix   = '/appdata/';
    public $showUrl             = false;

    public function __construct()
    {
    }

    public function setUserConsolePrefix()
    {
        $this->_consolePrefix = '/user/';
    }

    public function setAppDataConsolePrefix()
    {
        $this->_consolePrefix = '/appdata/';
    }

    public function getConsolePrefix()
    {
        return $this->_consolePrefix;
    }

    /**
     * @param string $masterSecret
     */
    public function setMasterSecret($masterSecret)
    {
        $this->_masterSecret = $masterSecret;
    }

    /**
     * @return string
     */
    public function getMasterSecret()
    {
        return $this->_masterSecret;
    }

    /**
     * @param string $appKey
     */
    public function setAppKey($appKey)
    {
        $this->_appKey = $appKey;
    }

    /**
     * @return string
     */
    public function getAppKey()
    {
        return $this->_appKey;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->_baseUrl = $baseUrl;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function getHTTPHead()
    {
        return array(
            'Authorization: Basic '.base64_encode($this->getAppKey().':'.$this->getMasterSecret()). '',
            'X-Kinvey-API-Version: 3',
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Kinvey-ResponseWrapper true',
            'Content-Type: text/xml; charset=utf-8'
        );
    }

    public function retrieve($collectionName = '', $id = '', $query = array(), $modifiers = array(), $resolve = array(), $convertJson = true)
    {
        if (($collectionName == null || $collectionName == '') && $this->getConsolePrefix() != '/user/') {
            throw new Exception('Collection not informed');
        }

        $url = $this->getBaseUrl().$this->getConsolePrefix(). $this->getAppKey(). '/'. $collectionName;

        if ($id != '') {
            $url .= '/'.$id;
        }

        $url = $this->_buildQuery($url, $query, $modifiers, $resolve);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            return $convertJson ? json_decode($output) : $output;
        }
    }

    public function update($collectionName = '', $id = '', $data = array())
    {
        if ($collectionName == '') {
            throw new Exception('Collection not informed to update');
        }

        if ($id == '') {
            throw new Exception('$id data is required to update');
        }

        $url = $this->getBaseUrl().$this->getConsolePrefix(). $this->getAppKey(). '/'. $collectionName . '/'. $id;

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        $data_string = json_encode($data);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            // something like "{"appPlan":{"_type":"KinveyRef","_collection":"AppPlans","_id":"52824b3d6ee8db1935002f2e"},"_id":"528e0b5f5681204e4e000005","_acl":{"creator":"kid_VVOYDIVQJ9"},"_kmd":{"lmt":"2013-11-22T19:40:23.389Z","ect":"2013-11-22T19:11:33.221Z"}}"
            return json_decode($output);
        }
    }

    public function create($collectionName = '', $data = array())
    {
        if ($collectionName == '') {
            throw new Exception('Collection not informed to create');
        }

        $requestType = 'POST';

        $url = $this->getBaseUrl() .$this->getConsolePrefix(). $this->getAppKey(). '/'. $collectionName . '/';

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        $data_string = json_encode($data);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            // something like "{"appPlan":{"_type":"KinveyRef","_collection":"AppPlans","_id":"52824b3d6ee8db1935002f2e"},"_id":"528e0b5f5681204e4e000005","_acl":{"creator":"kid_VVOYDIVQJ9"},"_kmd":{"lmt":"2013-11-22T19:40:23.389Z","ect":"2013-11-22T19:11:33.221Z"}}"
            return json_decode($output);
        }
    }

    public function delete($collectionName = '', $id = '', $query = array())
    {

        if ($collectionName == '') {
            throw new Exception('Collection not informed to delete');
        }

        if ($id == '') {
            throw new Exception('$id data is required to delete');
        }

        $url = $this->getBaseUrl().$this->getConsolePrefix(). $this->getAppKey(). '/'. $collectionName . '/'. $id;

        $url = $this->_buildQuery($url, $query);

        $ch = curl_init($url);

        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt( $ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            return json_decode($output);
        }
    }

    public function users($id = '', $query = array(), $modifiers = array())
    {

        $url = $this->getBaseUrl().'/user/'. $this->getAppKey(). '/';

        if ($id != '') {
            $url .= $id;
        }

        $url = $this->_buildQuery($url, $query, $modifiers);

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            return json_decode($output);
        }
    }

    protected function _buildQuery($url = '', $query = array(), $modifiers = array(), $resolve = array())
    {
        if (is_null($query)) {
            $query = array();
        }

        $url .= "?query=".json_encode($query);

        if (!empty($modifiers)) {
            if (isset($modifiers['limit'])) {
                // ?query={}&limit=10
                $url .= '&limit='.$modifiers['limit'];
            }

            if (isset($modifiers['sort'])) {
                if (is_array($modifiers['sort'])) {
                    // &sort={"age": -1}
                    $url .= '&sort='.json_encode($modifiers['sort']);
                }
                if (is_string($modifiers['sort'])) {
                    $url .= '&sort='.$modifiers['sort'];
                }
            }

            if (isset($modifiers['fields'])) {
                // query={}&fields=age,lastName
                $url .= '&fields='.implode(',', $modifiers['fields']);
            }

            if (isset($modifiers['skip'])) {
                // ?query={}&skip=20
                $url .= '&skip='.$modifiers['sort'];
            }
        }

        if (!empty($resolve)) {
            $url .= '&resolve='.implode(',', $resolve);
        }

        if ($this->showUrl) {
            debug($url,1);
        }

        return $url;
    }

    public function execute($customEndpoint = '', $data = array(), $requestType = 'POST')
    {
        if ($customEndpoint == '') {
            throw new Exception('Custom endpoint not informed');
        }

        $url = $this->getBaseUrl().'/rpc/'. $this->getAppKey(). '/custom/'. $customEndpoint . '/';

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        $data_string = json_encode($data);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $requestType);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            // something like "{"appPlan":{"_type":"KinveyRef","_collection":"AppPlans","_id":"52824b3d6ee8db1935002f2e"},"_id":"528e0b5f5681204e4e000005","_acl":{"creator":"kid_VVOYDIVQJ9"},"_kmd":{"lmt":"2013-11-22T19:40:23.389Z","ect":"2013-11-22T19:11:33.221Z"}}"
            return json_decode($output);
        }
    }

    public function login($username, $password)
    {
        if ($username == '') {
            throw new Exception('Informe seu usuário');
        }

        if ($password == '') {
            throw new Exception('Informe sua senha');
        }

        $url = $this->getBaseUrl().'/user/'. $this->getAppKey(). '/login';

        $ch = curl_init($url);
        curl_setopt($ch,CURLOPT_HTTPHEADER, $this->getHTTPHead());

        $data_string = json_encode(array('username' => $username, 'password' => $password));
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // Acessar a URL e retornar a saída
        $output = curl_exec( $ch );

        if (curl_errno($ch)) {
            return 'Curl error: ' . curl_error($ch);
        } else {
            curl_close($ch);
            // something like "{"appPlan":{"_type":"KinveyRef","_collection":"AppPlans","_id":"52824b3d6ee8db1935002f2e"},"_id":"528e0b5f5681204e4e000005","_acl":{"creator":"kid_VVOYDIVQJ9"},"_kmd":{"lmt":"2013-11-22T19:40:23.389Z","ect":"2013-11-22T19:11:33.221Z"}}"
            return json_decode($output);
        }
    }
}
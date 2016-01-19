<?php
/**
 * @class: Tiny
 */

require_once "LogMessage.php";
require_once "functions.php";
class Tiny {

    function __construct()
    {
        $this->dbHost = getenv('DB_HOST');

        $this->dbUser = getenv('DB_USER');

        $this->dbPass = getenv('DB_PASSWORD');

        $this->dbName = getenv('DB_NAME');

        try
        {
            $this->connection = new PDO("mysql:host=".$this->dbHost.";dbname=".$this->dbName, $this->dbUser, $this->dbPass);
        }
        catch(Exception $e)
        {
            LogMessage::exception($e->getMessage(), __FUNCTION__, __CLASS__);
            throw new Exception ("Error Connecting to Database");
        }

    }

    /**
     * createUrl creates the tinyUrl for requested Url
     * @params $url string, requested URL to be shorted
     * @return string
     **/
    public function createUrl($url)
    {
        try{
            $urlObj = $this->getUrl($url);
        }
        catch (Exception $e)
        {
            $response = array("status" => 'error', "message" => "An error occured, Please try again later","error_code" => $e->getCode());
            return json_encode($response);
        }
        if($urlObj){
            $response = array("status" => 'success', "url" => $urlObj["url"],"tiny_url" => getUrl($urlObj["tiny"]));
            return json_encode($response);
        }
        do{
            try{
                $newTinyUrl = $this->createTinyUrl();
                $urlCheck = $this->getTinyUrl($newTinyUrl, true);
            }
            catch (Exception $e){
                $response = array("status" => 'error', "message" => "An error occured, Please try again later","error_code" => $e->getCode());
                return json_encode($response);
            }
        }while($urlCheck == true);

        try{
            $this->registerUrl($url, $newTinyUrl);
            $response = array("status" => 'success', "url" => $url, "tiny_url" => getUrl($newTinyUrl));
            return json_encode($response);
        }catch (Exception $e){
            $response = array("status" => 'error', "message" => $e->getMessage(), "error_code"=> $e->getCode());
            return json_encode($response);
        }

    }

    /**
     * getUrl checks if a url exist on the database
     * @param $url
     * @param bool $returnBool
     * @return bool | array
     * @throws Exception
     */
    protected function getUrl($url, $returnBool = false)
    {
        try{
            $sql = $this->connection->prepare("SELECT * FROM url WHERE url = :requestUrl");
            $sql->bindParam(":requestUrl", $url, PDO::PARAM_STR);

            $sql->fetch(PDO::FETCH_OBJ);
            $sql->execute();
            if($sql->rowCount() == 1){
                foreach($sql as $test)
                {
                    return ($returnBool == true) ? true : $test;
                }
            }
            else return false;

        }catch (Exception $e){
            LogMessage::exception($e->getMessage(), __FUNCTION__, __CLASS__);
            throw new Exception ("Error ckecking existence of URL", 1);
        }
    }


    /**
     * function return tinyUrl if exist and false if it does not
     * @param $url
     * @param bool $returnBool
     * @return bool | array
     * @throws Exception
     */
    public function getTinyUrl($url, $returnBool = false)
    {
        try{
            $sql = $this->connection->prepare("SELECT * FROM url WHERE tiny = :requestUrl");
            $sql->bindParam(":requestUrl", $url, PDO::PARAM_STR);

            $sql->fetch(PDO::FETCH_OBJ);
            $sql->execute();
            if($sql->rowCount() == 1){
                foreach($sql as $test)
                {
                    return ($returnBool == true) ? true : $test;
                }
            }
            else return false;

        }catch (Exception $e){
            LogMessage::exception($e->getMessage(), __FUNCTION__, __CLASS__);
            throw new Exception ("Error ckecking existence of tinyURL", 2);
        }

    }

    /**
     * create random string for URL
     * @return string
     */
    protected function createTinyUrl()
    {
        $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz_-";
        srand((double)microtime()*1000000);
        $i = 0;
        $tinyUrl = '' ;
        $no = rand(3,8);

        while ($i <= $no) {
            $num = rand() % strlen($chars);
            $tmp = substr($chars, $num, 1);
            $tinyUrl = $tinyUrl . $tmp;
            $i++;
        }

        return $tinyUrl;
    }

    /**
     * register all url and respective tinyurl generated to the database
     * @param $url
     * @param $tinyUrl
     * @throws Exception
     */
    protected function registerUrl($url, $tinyUrl)
    {
        try{
            $query = "INSERT INTO url (url, tiny, created_at, user_agent, server_ip, remote_ip)
                      VALUES (:url, :tinyUrl , NOW(), :user_agent, :server_ip, :remote_ip)";
            $sql = $this->connection->prepare($query);
            $sql->bindParam(":url", $url, PDO::PARAM_STR);
            $sql->bindParam(":tinyUrl", $tinyUrl, PDO::PARAM_STR);
            $sql->bindParam(":user_agent", $_SERVER["HTTP_USER_AGENT"], PDO::PARAM_STR);
            $sql->bindParam(":server_ip", $_SERVER["SERVER_ADDR"], PDO::PARAM_STR);
            $sql->bindParam(":remote_ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
            $sql->execute();

        }catch (Exception $e) {
            LogMessage::exception($e->getMessage(), __FUNCTION__, __CLASS__);
            throw new Exception ("Error registering new URL", 3);
        }
    }

    /**
     * gets the requested url
     * @param $url
     * @return string
     */
    public function getRequestUrl($url)
    {
        try{
            $tinyUrlArray = $this->getTinyUrl($url);
            if($tinyUrlArray) {
                if(filter_var($tinyUrlArray["url"], FILTER_VALIDATE_URL)){
                    $response = array("status" => true, "url" => $tinyUrlArray["url"], "tiny_url" => $_SERVER["SERVER_NAME"]. "/" .$tinyUrlArray["tiny"]);
                }
                else {
                    $response = array("status" => false, "message" => "URL returned is not supported format.", "error_code" => 11);
                }
            }

            else{
                $response = array("status" => false, "message" => "URL requested does not exist", "error_code" => 12);
            }

            return json_encode($response);
        }
        catch (Exception $e) {
            $response = array("status" => false, "message" => "Error getting the requested URL", "error_code"=> $e->getCode());
            return json_encode($response);
        }
    }


    /**
     * registers all the URL requests gotten on the site
     * @param $url
     * @throws Exception
     */
    public function registerRequest($url)
    {
        try{
            $query = "INSERT INTO requests (tiny_url, created_at, user_agent, server_ip, remote_ip)
                      VALUES ( :tinyUrl , NOW(), :user_agent, :server_ip, :remote_ip)";
            $sql = $this->connection->prepare($query);
            $sql->bindParam(":tinyUrl", $url, PDO::PARAM_STR);
            $sql->bindParam(":user_agent", $_SERVER["HTTP_USER_AGENT"], PDO::PARAM_STR);
            $sql->bindParam(":server_ip", $_SERVER["SERVER_ADDR"], PDO::PARAM_STR);
            $sql->bindParam(":remote_ip", $_SERVER["REMOTE_ADDR"], PDO::PARAM_STR);
            $sql->execute();

        }catch (Exception $e) {
            LogMessage::exception($e->getMessage(), __FUNCTION__, __CLASS__);
            throw new Exception ("Error registering new URL", 3);
        }
    }

} 
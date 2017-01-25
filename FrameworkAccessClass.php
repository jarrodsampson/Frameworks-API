<?php

/**
 * A Framework API
 *
 * Supporting GET, POST, PUT, and DELETE
 *
 * @author Jarrod Sampson
 * @copyright 2015 Planlodge
 *
 */

class FrameworkAccessClass extends AccessObject
{
    
    public function __construct()
    {
    	// CORS headers to allow certain methods
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Content-type:application/json;charset=utf-8');
        header('X-Content-Type-Options: nosniff');
        parent::__construct();
    }
    
    private function databaseConnection()
    {
        // check database connections
        $conn       = mysqli_connect($this->host, $this->username, $this->password, $this->database);
        $connection = true;
        
        if ($conn) {
            $connection = true;
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        return $conn;
    }
    
    /*
	 * Loop through the data
	 * for GET functions
	 */
    private function dataQuery($conn, $sqlQueryObject, $format, $version)
    {
        if ($version == "v1")
        {
            $result      = mysqli_query($conn, $sqlQueryObject);
            $numberCount = mysqli_num_rows($result);
            
            if ($numberCount > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    $framework   = $row['framework'];
                    $language    = $row['language'];
                    $link        = $row['link'];
                    $description = $row['description'];
                    $id          = $row['id'];
                    $popularity  = $row['popularity'];
                    $dateAdded   = $row['dateAdded'];
                    $image       = $row['images'];

                    
                    $frameworks[] = array(
                        'id' => $id,
                        'framework' => $framework,
                        'language' => $language,
                        'link' => $link,
                        'popularity' => $popularity,
                        'dateAdded'  => $dateAdded,
                        'image'      => $image,
                        'description' => $description
                    );
                }
                // 
                //  This will create the json associated with the query
                //
                if ($format == "json")
                {
                    $output = json_encode(array(
                        'items' => $numberCount,
                        'data' => $frameworks
                    ), JSON_PRETTY_PRINT);
                    
                    return $output;
                }
                // 
                //  This will create the xml associated with the query
                //
                else if ($format == "xml")
                {

                    $xml = new SimpleXMLElement('<xml/>');

                    foreach ($frameworks as $framework) {
                        $track = $xml->addChild('framework');
                        $track->addChild('id', $framework['id']);
                        $track->addChild('name', $framework['framework']);
                        $track->addChild('language', $framework['language']);
                        $track->addChild('link', $framework['link']);
                        $track->addChild('popularity', $framework['popularity']);
                        $track->addChild('dateAdded', $framework['dateAdded']);
                        $track->addChild('image', $framework['image']);
                        $track->addChild('description', $framework['description']);
                    }

                    Header('Content-type: text/xml');
                    return ($xml->asXML());
                }
                
                
            } else {
                $output = json_encode(array(
                    'data' => 'No Framework Found'
                ), JSON_PRETTY_PRINT);
                
                return $output;
            }
        }
        else
        {

            //
            //   ncorrect Version found
            //
            $output = json_encode(array(
                'Version' => 'Please Select a released version of this API.',
                'Issue' => $version . ' is not defined.'
            ), JSON_PRETTY_PRINT);

            return $output;

        }

    }
    
    public function getRequestParam()
    {
        
        if (isset($_GET['query']) && (isset($_GET['format']) && (isset($_GET['version'])))){

            $query = $_GET['query'];
            $format = $_GET['format'];
            $version = $_GET['version'];
            
            // check database connections
            $conn = $this->databaseConnection();
            
            if ($conn) {
                
                $queries  = new Queries;
                $sqlQuery = $queries->fetchFrameworksSearch($query);
                
                echo $this->dataQuery($conn, $sqlQuery, $format, $version);
                
            } else {
                $connection = false;
                echo "There was an error, please contact web administrator.";
                return false;
            }
            
            
            
        } 
        else if (isset($_GET['format']) && (isset($_GET['version'])))
        {
        	$format = $_GET['format'];
            $version = $_GET['version'];
            
            // check database connections
            $conn = $this->databaseConnection();
            
            if ($conn) {
                
                $queries  = new Queries;
                $sqlQuery = $queries->fetchAllFrameworks();
                
                
                echo $this->dataQuery($conn, $sqlQuery, $format, $version);
                
                
            } else {
                $connection = false;
                echo "There was an error, please contact web administrator.";
                return false;
            }
        }
        
    }
    
    public function postFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        if ($conn) {
            
            
            $framework   = trim($_POST['framework']);
            $language    = trim($_POST['language']);
            $link        = trim($_POST['link']);
            $description = trim($_POST['description']);
            
            $queries = new Queries;
            
            $sqlCheck    = $queries->checkFrameworkIfExists($framework);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) > 0) {
                echo json_encode(array(
                    'error' => $framework . ' already Exists.'
                ));
            } else {
                $sqlQuery = $queries->newFrameworkAddition($framework, $language, $link, $description);
                
                $result = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Created Framework Entry.'
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Add Request.'
                    ));
                }
            }
            
            
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
    }
    
    public function deleteFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        parse_str(file_get_contents("php://input"), $post_vars);
        $framework = $post_vars['framework'];
        
        if ($conn) {
            
            $queries     = new Queries;
            $sqlCheck    = $queries->checkFrameworkIfExists($framework);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) <= 0) {
                
                echo json_encode(array(
                    'error' => $framework . ' does not exist.'
                ));
            } else {
                $sqlQuery = $queries->deleteFrameworkQuery($framework);
                $result   = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Deleted ' . $framework
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Delete ' . $framework
                    ));
                }
            }
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        
    }
    
    public function updateFramework()
    {
        // check database connections
        $conn = $this->databaseConnection();
        
        parse_str(file_get_contents("php://input"), $post_vars);
        $id          = $post_vars['id'];
        $framework   = trim($post_vars['framework']);
        $language    = trim($post_vars['language']);
        $link        = trim($post_vars['link']);
        $description = trim($post_vars['description']);
        
        if ($conn) {
            
            $queries     = new Queries;
            $sqlCheck    = $queries->checkFrameworkIfExistsById($id);
            $checkResult = mysqli_query($conn, $sqlCheck);
            if (mysqli_num_rows($checkResult) <= 0) {
                echo json_encode(array(
                    'error' => $framework . ' does not exist.'
                ));
            } else {
                $sqlQuery = $queries->updateFrameworkQuery($id, $framework, $language, $link, $description);
                
                $result = mysqli_query($conn, $sqlQuery);
                if ($result) {
                    echo json_encode(array(
                        'success' => 'Updated ' . $framework
                    ));
                } else {
                    echo json_encode(array(
                        'error' => 'Unable to Update ' . $framework
                    ));
                }
            }
            
            
        } else {
            $connection = false;
            echo "There was an error, please contact web administrator.";
            return false;
        }
        
        
    }
}


?>
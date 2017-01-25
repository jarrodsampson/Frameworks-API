<?php

class Queries
{
    public function fetchAllFrameworks()
    {
        $query = "SELECT * FROM Frameworks";
        
        return $query;
    }
    
    public function fetchFrameworksSearch($querySearch)
    {
        $query = "SELECT * FROM Frameworks WHERE framework LIKE '%$querySearch%' OR description LIKE '%$querySearch%' OR language LIKE '%$querySearch%' LIMIT 10";
        
        return $query;
    }
    
    public function newFrameworkAddition($framework, $language, $link, $description)
    {
        $query = "INSERT INTO Frameworks VALUES('','$framework','$language','$link','$description')";
        
        return $query;
    }
    
    public function deleteFrameworkQuery($framework)
    {
        $query = "DELETE FROM Frameworks WHERE framework = '$framework'";
        
        return $query;
    }
    
    public function updateFrameworkQuery($id, $framework, $language, $link, $description)
    {
        $query = "UPDATE Frameworks SET framework = '$framework', language = '$language', link = '$link', description = '$description' WHERE id = '$id'";
        
        return $query;
    }
    
    public function checkFrameworkIfExists($framework)
    {
        $query = "SELECT * FROM Frameworks WHERE framework = '$framework'";
        
        return $query;
    }
    
    public function checkFrameworkIfExistsById($id)
    {
        $query = "SELECT * FROM Frameworks WHERE id = '$id'";
        
        return $query;
    }
}


?>
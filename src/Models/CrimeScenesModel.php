<?php

namespace Vanier\Api\Models;
use Vanier\Api\Models\BaseModel;

class CrimeScenesModel extends BaseModel
{
    private $table_name = "crime_scenes";
    
    public function __construct()
    {
        parent::__construct();
    }

    public function handleGetAllCrimeScenes(array $filters = [])
    {
        $query_values = [];
        $sql = "SELECT * FROM $this->table_name WHERE 1 ";
        
        //filters
        if(isset($filters["street"])){
            $sql .= " AND street LIKE CONCAT(:street,'%') ";
            $query_values[":street"] = $filters["street"]."%";
        }

        if(isset($filters["city"])){
            $sql .= " AND city LIKE CONCAT(:city,'%') ";
            $query_values[":city"] = $filters["city"]."%";
        }

        if(isset($filters["crime_sceneID"])){
            $sql .= " AND crime_sceneID LIKE CONCAT(:crime_sceneID,'%') ";
            $query_values[":crime_sceneID"] = $filters["crime_sceneID"]."%";
        }

        // sorting
        if(isset($filters["sort_by"])){
            $sort_by = $filters["sort_by"];
            if($sort_by == "street"){
                $sql .= " ORDER BY street";
            } elseif($sort_by == "city"){
                $sql .= " ORDER BY city";
            } elseif($sort_by == "crime_sceneID"){
                $sql .= " ORDER BY crime_sceneID";
            } 
        }

        return $this->paginate($sql, $query_values);
    }

    public function handleGetCrimeSceneById($crime_scene_id) {
        $sql = "SELECT * FROM $this->table_name WHERE crime_sceneID = :crime_sceneID";
        $query_values = [":crime_sceneID" => $crime_scene_id];

        return $this->run($sql, $query_values)->fetchAll();
    }
}
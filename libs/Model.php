<?php

class Model{
    function __construct() {
        //echo "Welcome  to the Model Base Class";
        $this->bdd = new DataBase();
    }

    public function useDataBase(){
        $useDb = "USE ".Session::get("selected_db");
        $R_Use = $this->bdd->query($useDb);
        if($R_Use){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    public function getTableInfos($table){
        //echo "DESC ".$table;
        $this->useDataBase();
        $describe = $this->bdd->query("DESC ".$table);
        //echo $describe->rowCount();
        if($describe){
            while ($data = $describe->fetch()){
                $donnee[] = array(
                    "Name" => $data["Field"],
                    "Type" => $data["Type"],
                    "Key" => $data["Key"],
                );
            }
        }
        //$this->buildClass($table);
        return $donnee;
    }


    public function getForeingKeyTable($fk,$table){
        $foreigneData = $this->getForeingTables($table);
        for($i=0;$i<count($foreigneData);$i++) {
            if($foreigneData[$i]['Column_Name'] == $fk){
                $ftable = $foreigneData[$i]['Foreign_table'];
            }
        }
        return $ftable;
    }

    public function getForeingTables($table){
        /*USE information_schema;
            select table_name,column_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
            from KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = 'demodb'
            AND TABLE_NAME = 't3'
            and REFERENCED_COLUMN_NAME is not null */

        $useInfoSchema = $this->bdd->query("USE information_schema");
        $r = "select table_name,column_NAME,CONSTRAINT_NAME,REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME
            from information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = '".Session::get("selected_db")."'
            AND TABLE_NAME = '".$table."'
            and REFERENCED_COLUMN_NAME is not null";
        //echo $r;
        $reqData = $this->bdd->query($r);

        $donnee = array();
        while($data = $reqData->fetch()){
            $donnee[] = array(
                "Foreign_table" => $data["REFERENCED_TABLE_NAME"],
                "Foreign_key" => $data["REFERENCED_COLUMN_NAME"],
                "Column_Name" => $data['column_NAME']
            );
        }
        return $donnee;
    }
}
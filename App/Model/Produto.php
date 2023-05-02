<?php

    namespace App\Model;

    class Produto{
        private $id;
        private $name;
        private $description;

        function getId(){
            return  $this->id;
        }
        function getname(){
            return  $this->name;
        }
        function getDescription(){
            return  $this->description;
        }

        function setId($id){
            $this->id = $id;
        }
        function setname($name){
            $this->name = $name;
        }
        function setDescription($description){
            $this->description = $description;
        }
    }

?>
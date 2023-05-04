<?php

    namespace App\Model;

    class ProdutoDao {

        public function create(Produto $produto){

            $sql = "INSERT INTO produto(name,description) VALUES (?,?)";

            $insert = Conexao::getConn()->prepare($sql);
            $insert->bindValue(1, $produto->getName());
            $insert->bindValue(2, $produto->getDescription());
            $insert->execute();
        }

        public function read(){
            $sql = "SELECT * FROM produto";

            $select = Conexao::getConn()->prepare($sql);
            $select->execute();

            if($select->rowCount() > 0){
                $resultado = $select->fetchAll(\PDO::FETCH_ASSOC);
                return $resultado;
            } else {
                return [];
            }
        }
        public function update(Produto $produto){
            $sql = "UPDATE produto SET name = ?, description = ? WHERE id = ?";

            $update = Conexao::getConn()->prepare($sql);
            $update->bindValue(1, $produto->getName());
            $update->bindValue(2, $produto->getDescription());
            $update->bindValue(3, $produto->getId());
            $update->execute();
        }
        
        public function delete($id){
            $sql = "DELETE FROM produto WHERE id = $id";

            $delete = Conexao::getConn()->prepare($sql);
            $delete->execute();
        }
    }
<?php
    require_once "vendor/autoload.php";

    $produto = new \App\Model\Produto();
    $produtoDao = new \App\Model\ProdutoDao();


    $produto->setId(1);
    $produto->setName('DESKTOP GAME DELL');
    $produto->setDescription('Memoria 64GB, Intel core Dual, SSD 164GB');

    // $produtoDao->update($produto);
    $produtoDao->delete(1);


    foreach($produtoDao->read() as $produtos){
        var_dump($produtos);
    }
?>
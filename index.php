<?php
    use App\Model\Conexao;
    require_once "vendor/autoload.php";
    session_start();
    ob_start();
    $produtoDao = new \App\Model\ProdutoDao();
    $home = new Home();
    

    class Home {
        public $head;
        public $footer; 

        function setHead($head){
            $this->head = $head;
        }

        function setFooter($footer){
            $this->footer = $footer;
        }

        public function renderHome(){
            require_once $this->head;
            require_once $this->footer;
        }

        public function renderProductList($products){
            ?>
            <table class="table">
                    <tr>
                        <th>Nome do produto</th>
                        <th>Descrição</th>
                        <th>Comando</th>
                    </tr>
                <?php
                foreach($products as $produtos){
                    if($produtos != ''){
                        ?> 
                        <tr>
                            <td><?php echo $produtos['name']?></td>
                            <td><?php echo $produtos['description']?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <a class='btn btn-danger' href='index.php?type=delete&id=<?php echo $produtos['id']?>' data-toggle="tooltip" title="Deletar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/>
                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/>
                                        </svg>
                                    </a>
                                    <a class="btn btn-primary" href="index.php?type=update&id=<?php echo $produtos['id']?>"  data-toggle="tooltip" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>    
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php       
                    }
                }
                ?>
            </table>
            <?php
        }

        public function messagensBar(){
            if(isset($_SESSION['messagerBar'])){
                ?>
                <div class="alert alert-<?php echo $_SESSION['messagerBar']['alert']?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['messagerBar']['messeger']?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php
                session_destroy();
            }
        }
    }
    
    $home->setHead("App/View/include/html/header.html");
    $home->setFooter("App/View/include/html/header.html");
    $home->messagensBar();
    $home->renderProductList($produtoDao->read());
    $home->renderHome();
    
    try {
        if(isset($_GET['type'])){
            if($_GET['type'] == 'create'){

                renderforms();
                if(isset($_POST['btn-success'])){
                    if($_POST['name'] != ''){
                        if($_POST['description'] != ''){
                            actionProdutoCreate($_POST['name'], $_POST['description']);
                        } else {
                            throw new Exception('O campo <b>descrição</b> precisa ser preenchido!', 41);
                        }
                    } else {
                        throw new Exception('O campo <b>nome</b> precisa ser preenchido!', 41);
                    }
                } else if(isset($_POST['btn-cancel'])){
                    throw new Exception('Ação cancelada!', 42);
                }
                
            } else if($_GET['type'] == 'update'){
                $id = $_GET['id'];
                $sql = "SELECT * FROM produto WHERE id = $id";

                $select = Conexao::getConn()->prepare($sql);
                $select->execute();

                if($select->rowCount() > 0){
                    $resultado = $select->fetchAll(\PDO::FETCH_ASSOC);
                    
                    renderformsUpdate($resultado[0]);
                    if(isset($_POST['btn-success'])){
  
                        if($_POST['description'] != '' OR $_POST['name'] != ''){
                            if($_POST['description'] != '' AND $_POST['name'] == ''){
                                actionProdutoUpdate($resultado[0]['id'], $resultado[0]['name'], $_POST['description']);
                            } else if($_POST['description'] == '' AND $_POST['name'] != ''){
                                actionProdutoUpdate($resultado[0]['id'], $_POST['name'], $resultado[0]['description']);
                            } else {
                                actionProdutoUpdate($resultado[0]['id'], $_POST['name'], $_POST['description']);
                            }
                        } else {
                            throw new Exception('O produto não alterado!', 42);
                        }
                    } else if(isset($_POST['btn-cancel'])){
                        throw new Exception('Ação cancelada!', 42);
                    }
                } else {
                    throw new Exception('O produto não foi encontrado!', 42);
                }

            } else if($_GET['type'] == 'delete'){
                $id = $_GET['id'];

                actionProdutoDelete();

                if(isset($_POST['btn-danger'])){
                    $produtoDao->delete($id);
                    header('Location: http://matheus.com/projetos/');
                } else if(isset($_POST['btn-cancel'])){
                    throw new Exception('Ação cancelada!', 42);
                }
            }
        }
        echo "<a class='btn btn-primary mt-4' href='index.php?type=create'>adicionar</a><br>";
    } catch(Exception $erro){
        $_SESSION['messagerBar'] = ['alert' => 'danger', 'messeger' => $erro->getMessage()];
        header('Location: http://matheus.com/projetos/index.php');
    }
    
    function renderformsUpdate($place){
        ?>
        <form id="background-mondal" method="post">
            <div style="display: block"class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title">Editar o produto</h5>
                        </div>
                        <div class="modal-body">
                            <label class="mb-2" for="floatingInput">Nome do Produto</label>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" autocomplete="off" name="name" id="name" placeholder="<?php echo $place['name']?>">
                                <label for="floatingInput"><?php echo $place['name']?></label>
                            </div>
                            <label class="mb-2" for="floatingInput">Descrição do Produto</label>
                            <div class="form-floating">
                                <input type="text" class="form-control" autocomplete="off" name="description" id="description" placeholder="<?php echo $place['description']?>">
                                <label for="floatingPassword"><?php echo $place['description']?></label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="btn-cancel" id="btn-cancel" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="btn-success" id="btn-success" class="btn btn-primary">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }
    function renderforms(){
        ?>
        <form id="background-mondal" method="post">
            <div style="display: block"class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-light">
                            <h5 class="modal-title">Adicionar um produto</h5>
                        </div>
                        <div class="modal-body">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" autocomplete="off" name="name" id="name" placeholder="Nome do produto">
                                <label for="floatingInput">Nome do produto</label>
                            </div>
                            <div class="form-floating">
                                <input type="text" class="form-control" autocomplete="off" name="description" id="description" placeholder="Descrição do Produto">
                                <label for="floatingPassword">Descrição do Produto</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="btn-cancel" id="btn-cancel" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="btn-success" id="btn-success" class="btn btn-primary">Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    } 

    function actionProdutoCreate($name, $description){
        $produto = new \App\Model\Produto('', $name, $description);
        $produtoDao = new \App\Model\ProdutoDao();
        $produtoDao->create($produto);
        $_SESSION['messagerBar'] = ['alert' => 'success', 'messeger' => "Produto criado com sucesso!"];
        header('Location: http://matheus.com/projetos/index.php');
    }

    function actionProdutoUpdate($id, $name, $description){
        var_dump($id, $name, $description);
        $produtoDao = new \App\Model\ProdutoDao();
        $produto = new \App\Model\Produto($id, $name, $description);
        $produtoDao->update($produto);
        $_SESSION['messagerBar'] = ['alert' => 'success', 'messeger' => "Produto atualizado com sucesso!"];
        header('Location: http://matheus.com/projetos/index.php');
    }

    function actionProdutoDelete(){
        ?>
        <form id="background-mondal" method="post">
            <div style="display: block"class="modal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-light">
                            <h5 class="modal-title">Apagando o produto</h5>
                        </div>
                        <div class="modal-body">
                            <p>Você realmente deseja apagar esse produto?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="btn-cancel" id="btn-cancel" class="btn btn-outline-secondary" data-bs-dismiss="modal">fechar</button>
                            <button type="submit" name="btn-danger" id="btn-danger" class="btn btn-danger">Apagar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <?php
    }
    require_once "App/View/include/html/footer.html";
?>
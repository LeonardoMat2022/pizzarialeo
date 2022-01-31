<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

    if($method === "GET") {

        $pedidosQuery = $conn->query("SELECT * FROM pedidos");
        $pedidos = $pedidosQuery->fetchAll();
        $pizzas = [];

        foreach($pedidos as $pedido) {

            $pizza = [];

            // definir um array para a pizza

            $pizza["id"] = $pedido["pizza_id"];

            // resgatando a pizza

            $pizzaQuery = $conn->prepare("SELECT * FROM pizzas WHERE id = :pizza_id");

            $pizzaQuery->bindParam(":pizza_id", $pizza["id"]);

            $pizzaQuery->execute();

            $pizzaData = $pizzaQuery->fetch(PDO::FETCH_ASSOC);

            // RESTAGANDO A BORDA DA PIZZA

            $bordaQuery = $conn->prepare("SELECT * FROM bordas WHERE id = :borda_id");

            $bordaQuery->bindParam(":borda_id", $pizzaData["borda_id"]);

            $bordaQuery->execute();

            $borda = $bordaQuery->fetch(PDO::FETCH_ASSOC);


            $pizza["borda"] = $borda["tipo"];


             // RESTAGANDO A massa DA PIZZA

             $massaQuery = $conn->prepare("SELECT * FROM massas WHERE id = :massa_id");

             $massaQuery->bindParam(":massa_id", $pizzaData["massa_id"]);
 
             $massaQuery->execute();
 
             $massa = $massaQuery->fetch(PDO::FETCH_ASSOC);
 
 
             $pizza["massa"] = $massa["tipo"];


             // RESTAGANDO A SABORES DA PIZZA

             $saboresQuery = $conn->prepare("SELECT * FROM  pizza_sabor WHERE pizza_id = :pizza_id");

             $saboresQuery->bindParam(":pizza_id", $pizza["id"]);
 
             $saboresQuery->execute();
 
             $sabores = $saboresQuery->fetchAll(PDO::FETCH_ASSOC);

            // REGASTANDO O NOME DOS SABORES

            $saboresDaPizza = [];

            $saborQuery = $conn->prepare("SELECT * FROM sabores WHERE id = :sabor_id");

            foreach($sabores as $sabor) {

                $saborQuery->bindParam(":sabor_id", $sabor["sabor_id"]);

                $saborQuery->execute();

                $saborPizza = $saborQuery->fetch(PDO::FETCH_ASSOC);

                array_push($saboresDaPizza, $saborPizza["nome"]);

            }

            $pizza["sabores"] = $saboresDaPizza;


            //ADICIONAR O STATUS DO PEDIDO

            $pizza["status"] = $pedido["status_id"];
 

            //ADICIONAR O ARRAY DE PIZZA, AO ARRAY DAS PIZZAS
            array_push($pizzas, $pizza);

        }

        //RESGATANDO OS STATUS
        $statusQuery = $conn->query("SELECT * FROM status;");

        $status = $statusQuery->fetchAll();




    } else if($method === "POST") {
        // VERIFICANDO TIPO DE POST
        $type = $_POST["type"];

        // DELETAR PEDIDO
        if($type === "delete") {
            $pizzaid = $_POST["id"];

            $deleteQuery = $conn->prepare("DELETE FROM pedidos WHERE pizza_id = :pizza_id;");

            $deleteQuery->bindParam(":pizza_id", $pizzaid, PDO::PARAM_INT);
            $deleteQuery->execute();

            $_SESSION["msg"] = "PEDIDO REMOVIDO COM SUCESSO!";
            $_SESSION["status"]= "success";


    //ATUALIZAR STATUS DO PEDIDO

        } else if($type === "update") {
            $pizzaid = $_POST["id"];
            $statusid = $_POST["status"];

            $updateQuery = $conn ->prepare("UPDATE pedidos SET status_id = :status_id WHERE pizza_id = :pizza_id");

            $updateQuery->bindParam("pizza_id", $pizzaid, PDO::PARAM_INT);
            $updateQuery->bindParam("status_id", $statusid, PDO::PARAM_INT);

            $updateQuery->execute();

            $_SESSION["msg"] = "PEDIDO ATUALIZADO COM SUCESSO!";
            $_SESSION["status"]= "success";
        }

    //RETORNA USUARIO PARA DASHBORD

    header("Location: ../dashboard.php");
    }

?>
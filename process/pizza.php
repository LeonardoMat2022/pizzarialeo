<?php

    include_once("conn.php");

    $method = $_SERVER["REQUEST_METHOD"];

// RESGATE DOS DADOS, MONTAGEM DO PEDIDO

    if($method === "GET")  {
        $bordasQuery = $conn->query("SELECT * FROM bordas;");

        $bordas = $bordasQuery->fetchAll();

        $massasQuery = $conn->query("SELECT * FROM massas;");

        $massas = $massasQuery->fetchAll();

        $saboresQuery = $conn->query("SELECT * FROM sabores;");

        $sabores = $saboresQuery->fetchAll();

   
//CRIAÇÃO DO PEDIDO
    } else if ($method == "POST") {
        $data = $_POST;

        $borda = $data["borda"];
        $massa = $data["massa"];
        $sabores = $data["sabores"];

        //VALIDAÇÃO DE SABORES MAXIMOS
        if(count($sabores) > 3) {
            $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
            $_SESSION["status"] = "warning";

        } else {
            //SALVANDO BORDA E MASSA NA PIZZA
            $stmt = $conn->prepare("INSERT INTO pizzas (borda_id, massa_id) VALUES (:borda, :massa)");

            //FILTRANDO INPUTS
            $stmt->bindParam(":borda", $borda, PDO::PARAM_INT);
            $stmt->bindParam(":massa", $borda, PDO::PARAM_INT);

            $stmt->execute();

        // RESGATANDO O ULTIMO ID DA UMTIMA PIZZA
            $pizzaid = $conn->lastInsertid();

            $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

            // REPETIÇÃO ATE TERMINAR DE SALVR TODOS OS SABORES
            foreach($sabores as $sabor) {
                // Filtrando os inputs
                $stmt->bindParam(":pizza", $pizzaid, PDO::PARAM_INT);
                $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);


                $stmt->execute();


            }

            //CRIAR O PEDIDO DA PIZZA

            $stmt = $conn->prepare("INSERT INTO pedidos (pizza_id, status_id) VALUES (:pizza, :status)");

            //STATUS SEMPRE INICIA COM 1, QUE É EM PRODUÇÃO

            $statusid = 1;

            // FILTRAR INPUTS
            $stmt->bindParam(":pizza", $pizzaid);
            $stmt->bindParam(":status", $statusid);


            $stmt->execute();

            $_SESSION["msg"] = "PEDIDO REALIZADO COM SUCESSO";
            $_SESSION["status"] = "success";

        }
    // RETORNA PARA PAGINA INICIAL
    header("Location: ..");


    }

?>
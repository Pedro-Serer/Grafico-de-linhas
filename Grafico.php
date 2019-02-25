<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <style>
      svg{
        border: 2px solid black;
        box-shadow: 2px 2px 2px black;
      }

      .verde{
        background-color: #2E2E2E;
      }

      .white{
        background-color: #BDBDBD;
      }
    </style>
  </head>
  <body>

    <form method="post" action="">
      Digite o valor da linha: <input id="muda" name="campo" type="text" placeholder="Tamanho da linha"></br>
      <p>Vai subir:</p>
      <select name="somar">
        <option value="1">Sim</option>
        <option value="0">Não</option>
      </select>
      <input name="submit" type="submit" value="OK">
    </form>

    <svg id="treino" height="375" width="850"></svg>

    <?php
    require_once('C:\funcoes.php');
    date_default_timezone_set("Brazil/East");
    /*Salvar o rating no banco e subtrair ou somar o último valor pelo novo valor, então com isso alterar o L1. O M1 pode ser ajustados pelos ID. As datas ficarão como hit nos círculos*/
    /*Dentro de um intervalo de hora o L1 vai ser 50, entre uma hora e oura vai ser 90 e entre um dia e outra vai ser 120*/

    $hora    = date("H");
    $minuto  = date("i");
    $minHora = $hora.":".$minuto;

    $conectar                  = mysqli_connect("127.0.0.1","root","","test");
    $consulta_do_ultimo_rating = mysqli_query($conectar, "SELECT * FROM path ORDER BY ID DESC");
    $ultimo_rating             = mysqli_fetch_array($consulta_do_ultimo_rating, MYSQLI_ASSOC);
    $pega                      = isset($_POST['campo'])?$_POST['campo']:0;
    $pega1                     = $ultimo_rating["Rating"];
    $somar                     = $_POST['somar'];

    /*Verifica se o as linhas irão subir ou descer*/
    if($somar == 1){
      $resultado    = $pega1 + $pega;
      $movimento_L2 = $pega * (-0.35);
    }
    else{
      $resultado    = $pega1 - $pega;
      $movimento_L2 = $pega * 0.35;
    }

    if ($resultado > 2100 or $resultado < 1200) {
      die("<script>alert('Valor $resultado fora de escala'); history.go(-1);</script>");
    }

    $valor_m      = transforma_m($resultado);
    $retorna_L2   = transforma_l($hora, $minuto);

    /*Consulta o último valor de dx*/
    $consulta_dx   = mysqli_query($conectar, "SELECT * FROM path ORDER BY ID DESC");
    $row_dx        = mysqli_fetch_array($consulta_dx, MYSQLI_ASSOC);
    $pega_dx       = $row_dx['Dx'];
    $pega_id       = $row_dx['ID'];

    /*Se não tiver último valor do banco insere com NULL no banco*/
    if($pega_id > 0){
      $insert = mysqli_query($conectar, "INSERT INTO Path VALUES (DEFAULT, $resultado, $pega_dx + $retorna_L2, $valor_m, 'NULL', 'NULL', $movimento_L2, $retorna_L2)");
    }
    else{
      $insert = mysqli_query($conectar, "INSERT INTO Path VALUES (DEFAULT, $resultado, $pega_dx + $retorna_L2, $valor_m, 'NULL', 'NULL', $movimento_L2, 'NULL')");
    }

    /*Consulta para o update*/
    $consulta_update = mysqli_query($conectar, "SELECT * FROM path ORDER BY ID DESC");
    $row_update      = mysqli_fetch_array($consulta_update, MYSQLI_ASSOC);
    $pega_UltLTam    = $row_update['UltLTam'];
    $pega_UltAng     = $row_update['UltAng'];

    /*Salva o L2 e o ID em variáveis e recupera esses valorres para dar um update no antigo*/
    if($ultimo_rating['ID'] or $ultimo_rating['Rating'] == NULL){
      $update_LTamanho = mysqli_query($conectar, "UPDATE path SET LTamanho = $pega_UltLTam WHERE ID = $pega_id LIMIT 1");
      $update_Langulo  = mysqli_query($conectar, "UPDATE path SET Langulo = $pega_UltAng WHERE ID > $pega_id-1 LIMIT 1");
    }

    $consulta = mysqli_query($conectar, "SELECT * FROM path ORDER BY ID ASC");

    /*Cria os elementos do gráfico*/
    echo $script = "<script> var namespaceSVG = 'http://www.w3.org/2000/svg';";

    while($row = mysqli_fetch_array($consulta, MYSQLI_ASSOC)){
      $id       = $row["ID"];
      $Dx       = $row["Dx"];
      $Dy       = $row["Dy"];
      $Langulo  = $row["Langulo"];
      $LTamanho = $row["LTamanho"];

      echo "\n\nvar path$id = document.createElementNS(namespaceSVG, 'path');
      path$id.setAttributeNS(null, 'd', 'M $Dx $Dy l $Langulo $LTamanho');
      path$id.setAttributeNS(null, 'stroke', 'green');
      path$id.setAttributeNS(null, 'stroke-width', 5);
      document.getElementById('treino').appendChild(path$id);";

      echo "\n\nvar circle$id = document.createElementNS(namespaceSVG, 'circle');
      circle$id.setAttributeNS(null, 'cx', $Dx);
      circle$id.setAttributeNS(null, 'cy', $Dy);
      circle$id.setAttributeNS(null, 'r', 6);
      circle$id.setAttributeNS(null, 'stroke', 'none');
      circle$id.setAttributeNS(null, 'fill', 'green');
      document.getElementById('treino').appendChild(circle$id);";
    }

    echo $script = "</script>";
    mysqli_close($conectar);
  ?>

  <script src="grafico.js"></script>
  </body>
  </html>

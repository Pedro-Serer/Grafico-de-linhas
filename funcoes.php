<?php
  function transforma_l($hora, $minuto){
    if($minuto < 60){
      $L1 = 50;
    }
    return $L1;
  }

  function transforma_m($rating){
    $y = 0;

    switch($rating){
      case 2050:
        $y = 20;
        break;
      case 2000:
        $y = 35;
        break;

      case 1950:
        $y = 55;
        break;

      case 1900:
        $y = 70;
        break;

      case 1850:
        $y = 90;
        break;

      case 1800:
        $y = 105;
        break;

      case 1750:
        $y = 122;
        break;

      case 1700:
        $y = 140;
        break;

      case 1650:
        $y = 160;
        break;

      case 1600:
        $y = 175;
        break;

      case 1550:
        $y = 195;
        break;

      case 1500:
        $y = 210;
        break;

      case 1450:
        $y = 228;
        break;

      case 1400:
        $y = 245;
        break;

      case 1350:
        $y = 265;
        break;

      case 1300:
        $y = 280;
        break;

      case 1250:
        $y = 298;
        break;

      case 1200:
        $y = 315;
        break;
    }
    return $y;
  }
?>

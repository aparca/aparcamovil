<?php
Class Aparca_App
{
    private $pdo;
    private $lat;
    private $lon;

    private $confPrecios = array(
        1525 => 'donosti-laconcha',
        1527 => 'donosti-Okendo',
        1558 => 'donosti-Kursaal',
        1416 => 'donosti-sanMartin',
        1526 => 'donosti-Easo',
        1531 => 'donosti-PlCatalunya',
        1530 => 'donosti-Atotxa',
        1532 => 'donosti-Arcco',
        1528 => 'donosti-Pio XII',
        1522 => 'donosti-Antiguo',
        719  => 'madrid',
        743 => 'madrid',
        678 => 'madrid',
        705 => 'madrid',
        664 => 'madrid',
        712 => 'madrid',
        1218 => 'madrid',
        751 => 'madrid',
        718 => 'madrid',
        1245 => 'madrid',
        1298 => 'madrid',
        1247 => 'madrid',
        693 => 'madrid',
        727 => 'madrid',
        729 => 'madrid',
        1294 => 'madrid',
        656 => 'madrid',
        686 => 'madrid',
        754 => 'madrid',
        755 => 'madrid',

        1478 => 'pamplona-Baluarte',
        1483 => 'pamplona-BlancaNavarra',
        3175 => 'pamplona-Carlos III',
        3189 => 'pamplona-Hospitales',
        3177 => 'pamplona-Plaza Castillo',
        1477 => 'pamplona-Aduana',
        3173 => 'pamplona-Audiencia',

        3182 => 'vitoria-3182',
        1418 => 'vitoria-1418',
        1423 => 'vitoria-1423',
        1457 => 'vitoria-1457',
        1458 => 'vitoria-1458',
        1558 => 'vitoria-1558',
        3183 => 'vitoria-3183',
        3184 => 'vitoria-3184',
        3185 => 'vitoria-3185',
        3186 => 'vitoria-3186',
    );
    
    private function calc($minutos, $limits) 
    {
        $precio = 0;
        $ulimit = null;
        foreach ($limits as $key => $price) {
            $key--;
            if ($minutos  > $key) {
                $precio += $minutos * $price;
                $minutos-= $key;
            }
            $ulimit = $key;
        }
        return round($precio, 3);
    }
    
    private $confPreciosLimits = array(
        'donosti-laconcha' => array(481 => 0.011, 241 => 0.039, 121 => 0.029, 91 => 0.028, 16 => 0.030, 0 => 0.043),           
        'donosti-Okendo' => array(481 => 0.011, 241 => 0.039, 121 => 0.029, 91 => 0.028, 16 => 0.030, 0 => 0.043),                 
        'donosti-Kursaal' => array(481 => 0.011, 241 => 0.039, 121 => 0.029, 91 => 0.028, 16 => 0.030, 0 => 0.043),                 
        'donosti-sanMartin' => array(481 => 0.011, 241 => 0.039, 121 => 0.029, 91 => 0.028, 16 => 0.030, 0 => 0.043),                 
        'donosti-Easo' => array(481 => 0.018, 241 => 0.034, 121 => 0.024, 91 => 0.023, 16 => 0.024, 0 => 0.036),                 
        'donosti-PlCatalunya' => array(481 => 0.018, 241 => 0.034, 121 => 0.024, 91 => 0.023, 16 => 0.024, 0 => 0.036),                
        'donosti-Atotxa' => array(481 => 0.018, 241 => 0.034, 121 => 0.024, 91 => 0.023, 16 => 0.024, 0 => 0.036),                  
        
        'donosti-Arcco' => array(481 => 0.020, 241 => 0.021, 121 => 0.022, 91 => 0.021, 16 => 0.022, 0 => 0.033),                 
        'donosti-Pio XII' => array(481 => 0.020, 241 => 0.021, 121 => 0.022, 91 => 0.021, 16 => 0.022, 0 => 0.033),                  
        'donosti-Antiguo' => array(481 => 0.020, 241 => 0.021, 121 => 0.022, 91 => 0.021, 16 => 0.022, 0 => 0.033),  
        
        'madrid' => array(91 => 0.045, 31 => 0.034, 0 => 0.038),     
        
        'pamplona-Baluarte' => array(601 => 0.002, 121 => 0.021, 61 => 0.027, 16 => 0.035, 0 => 0.045),                 
        'pamplona-BlancaNavarra' => array(601 => 0.003, 121 => 0.021, 61 => 0.024, 16 => 0.035, 0 => 0.047),                                  
        'pamplona-Carlos III' => array(121 => 0.016, 61 => 0.021, 16 => 0.031, 0 => 0.035),                 
        'pamplona-Hospitales' => array(601 => 0.0014, 121 => 0.0144 , 61 => 0.024, 16 => 0.031, 0 => 0.044),                 
        'pamplona-Plaza Castillo' => array(121 => 0.023, 61 => 0.027, 16 => 0.034, 0 => 0.046),                
        'pamplona-Aduana' => array(121 => 0.021, 61 => 0.021, 16 => 0.027, 0 => 0.034),              
        'pamplona-Audiencia' => array(121 => 0.0154, 61 => 0.031, 16 => 0.035, 0 => 0.044), 
        
        'vitoria-3182' => array(1 => 0.0234, 0 => 0.521),                  
        'vitoria-1418' => array(1 => 0.0239, 0 => 0.6058),                  
        'vitoria-1423' => array(1 => 0.0239, 0 => 0.6058),                  
        'vitoria-1457' => array(1 => 0.0177, 0 => 0.54),                 
        'vitoria-1458' => array(1 => 0.0232, 0 => 0.5996),                 
        'vitoria-1558' => array(1 => 0.0254, 0 => 0.5595),                  
        'vitoria-3183' => array(1 => 0.0251, 0 => 0.5962),                  
        'vitoria-3184' => array(1 => 0.0295, 0 => 0.685),                  
        'vitoria-3185' => array(1 => 0.02, 0 => 0.52),                 
        'vitoria-3186' => array(1 => 0.0227, 0 => 0.6254),
    );
    
    private function calculaGasto($id, $t=null)
    {
        $minutos = round((time() - $t) / 60); 

        if (array_key_exists($id, $this->confPrecios) && array_key_exists($this->confPrecios[$id], $this->confPreciosLimits)) {
            return $this->calc($minutos, $this->confPreciosLimits[$this->confPrecios[$id]]);
        } else {
            return null;
        }
    }
    
    public function seGeo($lat, $lon)
    {
        $this->lat = $lat;
        $this->lon = $lon;
    }
    
    private function findTime($timestamp, $format) {        
        $difference = time() - $timestamp ; 
        if($difference < 0) 
            return false; 
        else{ 
        
            $min_only = intval(floor($difference / 60)); 
            $hour_only = intval(floor($difference / 3600)); 
            
            $days = intval(floor($difference / 86400)); 
            $difference = $difference % 86400; 
            $hours = intval(floor($difference / 3600)); 
            $difference = $difference % 3600; 
            $minutes = intval(floor($difference / 60)); 
            if($minutes == 60){ 
                $hours = $hours+1; 
                $minutes = 0; 
            } 
            
            if($days == 0){ 
                $format = str_replace('Days', '?', $format); 
                $format = str_replace('Ds', '?', $format); 
                $format = str_replace('%d', '', $format); 
            } 
            if($hours == 0){ 
                $format = str_replace('Hours', '?', $format); 
                $format = str_replace('Hs', '?', $format); 
                $format = str_replace('%h', '', $format); 
            } 
            if($minutes == 0){ 
                $format = str_replace('Minutes', '?', $format); 
                $format = str_replace('Mins', '?', $format); 
                $format = str_replace('Ms', '?', $format);        
                $format = str_replace('%m', '', $format); 
            } 
            
            $format = str_replace('?,', '', $format); 
            $format = str_replace('?:', '', $format); 
            $format = str_replace('?', '', $format); 
            
            $timeLeft = str_replace('%d', number_format($days), $format);        
            $timeLeft = str_replace('%ho', number_format($hour_only), $timeLeft); 
            $timeLeft = str_replace('%mo', number_format($min_only), $timeLeft); 
            $timeLeft = str_replace('%h', number_format($hours), $timeLeft); 
            $timeLeft = str_replace('%m', number_format($minutes), $timeLeft); 
                
            if($days == 1){ 
                $timeLeft = str_replace('Days', 'D', $timeLeft); 
                $timeLeft = str_replace('Ds', 'D', $timeLeft); 
            } 
            if($hours == 1 || $hour_only == 1){ 
                $timeLeft = str_replace('Hours', 'Horas', $timeLeft); 
                $timeLeft = str_replace('Hs', 'H', $timeLeft); 
            } 
            if($minutes == 1 || $min_only == 1){ 
                $timeLeft = str_replace('Minutes', 'Minutos', $timeLeft); 
                $timeLeft = str_replace('Mins', 'Min', $timeLeft); 
                $timeLeft = str_replace('Ms', 'M', $timeLeft);            
            } 
                
          return $timeLeft; 
        } 
    } 
    
    public function getOne($id, $t)
    {
        $sql = "
        SELECT
            id,
            attribution,
            title,
            lat,
            lon,
            imageURL,
            line4,
            line3,
            line2,
            type,
            dimension,
            alt,
            relativeAlt,
            distance,
            inFocus,
            doNotIndex,
            showSmallBiw,
            showBiwOnClick,
            liveInfo,
            liveId
        FROM
            POI_Table
        WHERE
            id = {$id}";

        $this->pdo->query($sql, PDO::FETCH_OBJ);
        foreach ($this->pdo->query($sql, PDO::FETCH_OBJ) as $row) {}
        
        $row->gastoAprox = $this->calculaGasto($row->id, $t/1000);
        $tiempo = $this->findTime($t/1000, '%d Days, %h Hours, %m Minutes'); 
        if (trim($tiempo) == '') {
            $tiempo = 'Hace nada';
        }
        $row->tiempo = $tiempo;
        return $row;
    }
    
    public function getCloseOnes($km)
    {
        $out = array();
        $sql = "
        SELECT
            (acos(sin(radians(lat)) * sin(radians({$this->lat})) + cos(radians(lat)) * 
                cos(radians({$this->lat})) * cos(radians(lon) - radians({$this->lon})))
                * 6378) dis,
            id,
            attribution,
            title,
            lat,
            lon,
            imageURL,
            line4,
            line3,
            line2,
            type,
            dimension,
            alt,
            relativeAlt,
            distance,
            inFocus,
            doNotIndex,
            showSmallBiw,
            showBiwOnClick,
            liveInfo,
            liveId
        FROM
            POI_Table
        WHERE
            (acos(sin(radians(lat)) * sin(radians({$this->lat})) + cos(radians(lat)) *
            cos(radians({$this->lat})) * cos(radians(lon) - radians({$this->lon}))) * 6378)
            < {$km}
        order by
            dis";

        // not all parkings has live info. Live info is stored on "plazasLibres" field
        $liveInfo = array();
        foreach ($this->pdo->query($sql, PDO::FETCH_OBJ) as $row) {
            $row->plazasLibres = null;
            if ($row->liveInfo == true) {
                $liveInfo[$row->liveId] = $row->id;
            }
            $row->dis2 = round($row->dis, 2);
            $row->gasto = $this->calculaGasto($row->id);
            $row->iconType = self::NO_LIVE_DATA;
            
            $out[$row->id] = $row;
        }

        if (count($liveInfo) > 0) {
            $plazas = $this->fetchLiveData($liveInfo);
            foreach ($plazas as $rowId => $plazasLibres) {
                
                $out[$rowId]->plazasLibres = $plazasLibres;
                
                //if ($plazasLibres > 0 && $plazasLibres <= self::ALMOST_FULL_NUMBER) {
                if ($plazasLibres > self::ALMOST_FULL_NUMBER ) { 
                    $out[$rowId]->iconType = self::FREE_PARKING;
                } else if ($plazasLibres > 0) {
                    $out[$rowId]->iconType = self::ALMOST_FULL_PARKING;
                } else if ($plazasLibres == 0) {
                    $out[$rowId]->iconType = self::FULL_PARKING;
                } else {
                    $out[$rowId]->iconType = self::NO_LIVE_DATA;
                }
            }
        }
        return $out;
    }
    
    const NO_LIVE_DATA = 'sin-info-plaza';
    const FULL_PARKING = 'sin-plaza';
    const ALMOST_FULL_PARKING = 'pocas-plaza';
    const FREE_PARKING = 'con-plaza';
    
    const ALMOST_FULL_NUMBER = 10;
    
    /**
     * Decodes WML string from Pamplona with the available parkings
     * Quick and dirty way. @todo refactor
     * @param String $wml
     * @return Array 
     */
    private function decodeWml($wml) {
        $xml = simplexml_load_file($wml);
        $parkingsList = array('Baluarte', 'Blanca de Navarra', 'Carlos III',
            'El Corte Inglés', 'Plaza de Toros', 'Plaza del Castillo', 'Rincón de la Aduana',
            'Audiencia', 'Autobuses');

        $plazas = array();
        $data = $xml->card->p[1];
        foreach ($parkingsList as $parking) {
            $x = explode("en {$parking}", $data);
            if ($x[0] != '---') {
                $plazas[$parking] = (int) $x[0];
            }
            $data = $x[1];
        }
        return $plazas;
    }

    private $pamplonaUrl = "http://aparca.pamplona.es";
    /**
     * Fetch the wml from pamplona and decode
     * @param Array $liveInfo
     * @return Array 
     */
    private function fetchLiveData($liveInfo) {
        $out = array();
        $parkings = $this->decodeWml($this->pamplonaUrl);
        foreach ($liveInfo as $liveId => $rowId) {
            if (isset($parkings[$liveId])) {
                $out[$rowId] = $parkings[$liveId];
            }
        }
        return $out;
    }

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 15-1-19
 * Time: 上午10:38
 */
class getGps
{

    public function getGpsFun($admin = null, $gpsId = null)
    {
        $pro = array();
        if ($admin) {
            $pro1['LoginName'] = $admin;
            array_push($pro, $pro1);
        } elseif (!$admin && $gpsId) {
            $pro2['Systemno'] = $gpsId;
            array_push($pro, $pro2);
        }

        try {
            $soap2 = new SoapClient("http://113.31.29.156/VehNewWebService/WebService.asmx?WSDL");
            $result2 = $soap2->__soapCall("GetNewPosition", $pro);
            $re = json_decode($result2->GetNewPositionResult, true);
            return $re;
        } catch (SoapFault $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

} 
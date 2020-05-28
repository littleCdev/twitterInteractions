<?php

class lcJson
{
    public $error = 0;
    public $msg = "";

    // compatibility to old version
    public function setVal($sKey, $mValue)
    {
        $this->$sKey = $mValue;
    }

    // compatibility to old version
    public function setError($error)
    {
        $this->error = $error;
    }

    // compatibility to old version
    public function setMsg($msg)
    {
        $this->msg = $msg;
    }

    private function objcontentToBase64(&$mVar)
    {
        foreach ($mVar as &$mValue) {
            $sType = gettype($mValue);

            switch ($sType) {
                case "bolean":
                case "integer":
                case "double":
                case "string":
                case "float":
                case "NULL":
                    $mValue = base64_encode($mValue);
                    break;

                case "object":
                case "array":
                    $this->objcontentToBase64($mValue);
                    break;
                case "resource":
                    break;
            }
        }
    }

    public function send($bDebug = false)
    {
        if ($bDebug) {
            var_dump($this);
        }

        $this->objcontentToBase64($this);

        echo "+++jsonstart+++";
        echo json_encode($this);
        echo "---jsonend---";
        exit;
    }
}
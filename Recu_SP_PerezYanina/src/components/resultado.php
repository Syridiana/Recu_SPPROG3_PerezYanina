<?php

namespace App\Components;

    class Resultado{
        public $estado;
        public $msg;
        public $status;

        public function __construct($estado,$msg,$status)
        {   
            $this->estado = $estado;
            $this->msg = $msg;
            $this->status = $status;
        }

        public function __set($name, $value)
        {
            $this->$name = $value;
        }

        public function __get($name)
        {
            return $this->$name;
        }

        public function mostarRespuesta(){
            echo json_encode($this);
        }
    }

?>
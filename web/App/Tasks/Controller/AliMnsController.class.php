<?php

namespace Tasks\Controller;
use Tasks\Controller\AliBaseController;

class AliMnsController extends AliBaseController
{

    public function _initialize()
    {
        parent::_initialize();
    }


    public function Notify(){

       R('Tasks/'.$this->Message['mc'].'/'.$this->Message['ac']);

      /* rwlog('ali_notify',$this->data);
       rwlog('ali_notify',$this->Message);*/

    }






}
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UIForm
 *
 * @author kiran
 */
class UIForm {

    //put your code here
    public function getElement($setElementName, $eleType, $value = '', $action = '', $eleId='') {
        $sql = "SELECT html FROM `form` WHERE `element` LIKE '" . $eleType . "'";
        $dataObj = new DataHandler();
        $res = $dataObj->GetQuery($sql);

        return str_replace('$$',$eleId, str_replace('??', $action, str_replace('##', $value, str_replace('@@', $setElementName, $res[0]['html']))));
    }
}

?>

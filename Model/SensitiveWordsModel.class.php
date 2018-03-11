<?php
    class SensitiveWordsModel extends FLModel {
        function find ($word, $limit = 0)
        {
            /** 查询 */
            $where = array ();
            $word === NULL ? : $where['AND']['word'] = $word;

            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $errorModel = new ErrorModel;
            $errorModel->sendError (MASTER, print_r($where, true));
                        
            $ret = $this->db->select ('app_sensitive_words', '*', $where);
            return $ret;
        }
    }

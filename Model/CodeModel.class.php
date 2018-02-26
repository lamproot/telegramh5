<?php
    class CodeModel extends FLModel {
        function find ($chat_id = NULL, $code = NULL, $status = 1, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_id === NULL ? : $where['AND']['chat_id'] = $chat_id;
            $code === NULL ? : $where['AND']['code'] = $code;
            //$status === NULL ? : $where['AND']['status'] = $status;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('codes', '*', $where);
            return $ret;
        }

        function updateByCode ($chat_id, $code, $status, $from_id, $from_username)
        {

            $where['AND']['chat_id'] = $chat_id;
            $where['AND']['code'] = $code;
            $where['AND']['status'] = $status;

            $res = $this->db->update ('codes', [
                'from_id' => $from_id,
                'from_username' => $from_username,
                'status' => 3,
                'updated_at' => time()
            ], $where);
        }

        function updateByFromId ($chat_id, $from_id, $status = -1)
        {
            $where['AND']['chat_id'] = $chat_id;
            $where['AND']['from_id'] = $from_id;
            $res = $this->db->update ('codes', [
                'status' => -1,
                'update_at' => time()
            ], $where);
        }
    }

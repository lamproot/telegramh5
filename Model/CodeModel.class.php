<?php
    class CodeModel extends FLModel {
        function find ($chat_bot_id = NULL, $code = NULL, $status = 1, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $code === NULL ? : $where['AND']['code'] = $code;
            //$status === NULL ? : $where['AND']['status'] = $status;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('codes', '*', $where);
            return $ret;
        }

        function updateByCode ($chat_bot_id, $code, $status, $from_id, $from_username, $first_name, $last_name)
        {

            //$where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['code'] = $code;
            $where['AND']['status'] = $status;

            $res = $this->db->update ('codes', [
                'from_id' => $from_id,
                'chat_bot_id' => $chat_bot_id,
                'from_username' => $from_username,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'status' => 3,
                'updated_at' => time()
            ], $where);
        }

        function updateByFromId ($chat_bot_id, $from_id, $status = -1)
        {
            $where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['from_id'] = $from_id;
            $res = $this->db->update ('codes', [
                'status' => -1,
                'updated_at' => time()
            ], $where);
        }

        function getCodeByFromId ($chat_bot_id, $from_id, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_bot_id === NULL ? : $where['AND']['chat_bot_id'] = $chat_bot_id;
            $from_id === NULL ? : $where['AND']['from_id'] = $from_id;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('codes', '*', $where);
            return $ret;

        }

        function getCodeByActivityId ($activity_id, $from_id, $limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $activity_id === NULL ? : $where['AND']['activity_id'] = $activity_id;
            $from_id === NULL ? : $where['AND']['from_id'] = $from_id;
            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('codes', '*', $where);
            return $ret;

        }

        function getByCode ($code)
        {
            return $this->db->get ('codes', '*', [
                'code' => $code
            ]);
        }
    }

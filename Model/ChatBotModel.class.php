<?php
    class ChatBotModel extends FLModel {

        function getcommand ($chat_id, $limit = 0)
        {

            $errorModel = new ErrorModel;
            $errorModel->sendError (MASTER, "chat_id_ddd".$chat_id);

            // return $this->db->get ('chat_bot', '*', [
            //     'chat_id' => $chat_id
            // ]);
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $chat_id === NULL ? : $where['AND']['chat_id'] = $chat_id;

            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('chat_bot', '*', $where);
            $errorModel->sendError (MASTER, "chat_id_ret".print_r($ret, true));
            return $ret;

        }
    }

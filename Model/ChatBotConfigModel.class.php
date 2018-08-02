<?php
    class ChatBotConfigModel extends FLModel {

        function getcommand ($chat_id, $limit = 0)
        {
            return $this->db->get ('chat_bot_', '*', [
                'chat_id' => $chat_id
            ]);
            /** 查询 */
            // $where = array (
            //     'ORDER' => 'id'
            // );
            // $chat_id === NULL ? : $where['AND']['chat_id'] = $chat_id;
            //
            // if ($limit != 0) {
            //     $where['LIMIT'] = $limit;
            // }
            // $ret = $this->db->select ('chat_bot', '*', $where);
            // return $ret;

        }

        function getChatList ($limit = 0)
        {
            /** 查询 */
            $where = array (
                'ORDER' => 'id'
            );
            $where['AND']['is_del'] = 0;

            if ($limit != 0) {
                $where['LIMIT'] = $limit;
            }
            $ret = $this->db->select ('chat_bot', 'tokenman_name', $where);
            return $ret;

        }

        function getById ($id)
        {
            return $this->db->get ('chat_bot', '*', [
                'id' => $id
            ]);

        }

        function getByChatId ($chat_id)
        {
            return $this->db->get ('chat_bot', '*', [
                'chat_id' => $chat_id
            ]);

        }

        function updateById ($bot_id, $master_id, $chat_id)
        {
            $where['AND']['id'] = $bot_id;
            $res = $this->db->update ('chat_bot', [
                'chat_id' => $chat_id,
                'master_id' => $master_id
            ], $where);
        }

        public function getGroupBotConfig($chat_bot_id, $chat_id)
        {
            /** 查询 */
            // $where = array (
            //     'ORDER' => 'id'
            // );
            $where['AND']['chat_bot_id'] = $chat_bot_id;
            $where['AND']['chat_id'] = $chat_id;
            $ret = $this->db->select ('group_bot_config', '*', $where);

            $result = [];
            foreach ($ret as $key => $value) {
                $result[$value['rule']] = $value['value'];
                if ($value['data']) {
                    $result[$value['rule']] = $value['data'];
                }

                if ($value['rule'] == 'clear_all_news_time') {
                    $result['clear_all_news_stop_time'] = $value['updated_at'] + $value['value'];
                }
            }
            return $result;
        }
    }

<?php

namespace App\Libraries\wyyIm;

use App\Models\LiveMember;
use App\StringToolkit;
use App\Models\Live;

class IstantMessage
{

    protected $AppKey;

    protected $Nonce;

    protected $CurTime;

    public function __construct()
    {
        $this->AppKey = config('site.wyyIm.AppKey');
        $this->Nonce = StringToolkit::createRandomString(20);
        $this->CurTime = time();
        $this->ContentType = 'application/x-www-form-urlencoded;charset=utf-8';
    }

    public function curl_im($url, $params = array())
    {
        $curl = curl_init();
        $headArr = array('AppKey:' . $this->AppKey, 'Nonce:' . $this->Nonce, 'CurTime:' . $this->CurTime, 'CheckSum:' . $this->CheckSum($this->Nonce, $this->CurTime), 'Content-type:' . $this->ContentType);

        if (!empty($params)) {
            $url = $url . (strpos($url, '?') ? '&' : '?') . http_build_query($params);
        }

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headArr);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);

        $response = curl_exec($curl);
        $curlinfo = curl_getinfo($curl);

        $body = substr($response, $curlinfo['header_size']);

        curl_close($curl);

        if (empty($curlinfo['namelookup_time'])) {
            return array();
        }

        $body = json_decode($body, true);

        return $body;
    }

    public function checkSum($nonce, $curTime)
    {
        $AppSecret = config('site.wyyIm.AppSecret');

        return sha1($AppSecret . $nonce . $curTime);
    }

    public function createTeam($live)
    {
        $count = $live->members()->count();
        //平均分配群数量
        $team_num = ceil($count / Live::TEAM_MAX_NUM);
        $people_num = ceil($count / $team_num);

        //查询群主是否创建，没有的话先创建群主
        $manager = $this->curl_im(config('site.wyyIm.GetImUser'), ['accids' => json_encode([Live::LIVE_MANAGER])]);
        if ($manager['code'] !== 200) {
            $Im_data = $this->curl_im(config('site.wyyIm.CreateImUser'), ['accid' => Live::LIVE_MANAGER]);
            if ($Im_data['code'] !== 200) {
                throw new \Exception('群主注册失败，请稍后再试。');
            }
        }

        $room_ids = '|';
        for ($i = 0; $i < $team_num; $i++) {
            $members = $live->members()->skip($i)->limit($people_num)->pluck('mobile')->toArray();

            //配置群信息
            $teamInfo = [
                'tname' => $live->title,
                'owner' => Live::LIVE_MANAGER,
                'msg' => 'welcome',
                'members' => json_encode($members),
                'magree' => Live::MAGREE_NO,
                'joinmode' => Live::JOINMODE_NO,
            ];

            $result = $this->curl_im(config('site.wyyIm.CreateImTeam'), $teamInfo);

            if ($result['code'] !== 200) {
                //throw 异常
                throw new \Exception('直播群注册失败，请稍后再试。');
            }

            $room_ids .= $result['tid'] . "|";
            $members = $live->members()->get();

            foreach ($members as $member) {
                $member->pivot->update(['room_id' => $result['tid']]);
            }
        }

        return $room_ids;
    }

    public function removeTeam($live)
    {
        if (empty($live->room_ids)) {
            throw new \Exception('直播群id不存在');
        }

        $room_ids = $live->room_ids;
        $room_ids = array_filter(explode('|', $room_ids));

        foreach ($room_ids as $room_id) {
            $params = ['tid' => $room_id, 'owner' => Live::LIVE_MANAGER];
            $this->curl_im(config('site.wyyIm.RemoveTeam'), $params);
        }
        $members = $live->members()->get();

        foreach ($members as $member) {
            $member->pivot->update(['state' => LiveMember::TYPE_CLOSE]);
        }

        return true;
    }

    public function getUserTeams($mobile)
    {
        $params = ['accid' => $mobile, 'owner' => Live::LIVE_MANAGER];
        $result = $this->curl_im(config('site.wyyIm.GetUserTeams'), $params);

        return $result;
    }

    public function refreshToken($mobile)
    {
        $params = ['accid' => $mobile];
        $result = $this->curl_im(config('site.wyyIm.refreshToken'), $params);

        return $result;
    }
}
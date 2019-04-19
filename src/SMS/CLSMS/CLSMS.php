<?php
namespace SMS\CLSMS;

class CLSMS
{
    protected $account = '';
    protected $password = '';
    public    $sendUrl = 'https://sms.253.com/msg/send/json';
    public    $variableUrl = 'https://sms.253.com/msg/variable/json';
    public    $queryBalanceUrl = 'https://sms.253.com/msg/balance/json';

    function __construct($account = null,$password = null)
    {
        $this->account = $account;
        $this->password = $password;
        return $this;
    }

    /**
     * 发送短信
     *
     * @param string $mobile 		手机号码
     * @param string $msg 			短信内容
     * @param string $needstatus 	是否需要状态报告
     */
    public function sendSMS($mobile,$msg,$needstatus = 'true')
    {
        //创蓝接口参数
        $postArr = array (
            'account'  =>  $this->account,
            'password' => $this->password,
            'msg' => urlencode($msg),
            'phone' => $mobile,
            'report' => $needstatus,
        );
        $result = $this->curlPost($this->sendUrl, $postArr);
        return $result;
    }
    /**
     * 发送变量短信
     *
     * @param string $msg 			短信内容
     * @param string $params 	最多不能超过1000个参数组
     */
    public function sendVariableSMS($msg, $params)
    {
        //创蓝接口参数
        $postArr = array (
            'account'  =>  $this->account,
            'password' => $this->password,
            'msg' => $msg,
            'params' => $params,
            'report' => 'true'
        );

        $result = $this->curlPost( $this->variableUrl, $postArr);
        return $result;
    }
    /**
     * 查询额度
     *
     *  查询地址
     */
    public function queryBalance()
    {
        //查询参数
        $postArr = array (
            'account'  =>  $this->account,
            'password' => $this->password,
        );
        $result = $this->curlPost($this->queryBalanceUrl, $postArr);
        return $result;
    }
    /**
     * 通过CURL发送HTTP请求
     * @param string $url  //请求URL
     * @param array $postFields //请求参数
     * @return mixed
     *
     */
    private function curlPost($url,$postFields){
        $postFields = json_encode($postFields);
        $ch = curl_init ();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8'   //json版本需要填写  Content-Type: application/json;
            )
        );
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4); //若果报错 name lookup timed out 报错时添加这一行代码
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt( $ch, CURLOPT_TIMEOUT,60);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
        $ret = curl_exec ( $ch );
        if (false == $ret) {
            $result = curl_error(  $ch);
        } else {
            $rsp = curl_getinfo( $ch, CURLINFO_HTTP_CODE);
            if (200 != $rsp) {
                $result = "请求状态 ". $rsp . " " . curl_error($ch);
            } else {
                $result = $ret;
            }
        }
        curl_close ( $ch );
        return $result;
    }
}
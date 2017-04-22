<?php
namespace app\index\controller;
use think\Session;
use think\Request;

// use app\checkCode\Captcha;
use think\captcha\Captcha;
use app\index\model\Users;
use app\common\Verify;


class Index
{
     public function index()
     {
        return view();
     }
    public function loginOrNot(){       //判断用户session是否存在
        // if(SESSION::get('userdata')['id']==$id){
        //     return CURD_result(200,'yes',SESSION::get('userdata'));
        // }
        // else{
        //    return CURD_result(2004,'no','');
        // }
        //判断是否存在Session
        // if(!userIsLogin()){
        //     return CURD_result(200,'请先登录哦！','');
        // }
        $request = Request::instance();
        $data=$request->param();
        $id=$data['id'];
        $userdata=Db('users')/*->field('id,email,nickname,createtime')*/->where('id',$id)->find(); 
        $userdata['password']='';
        return CURD_result(200,'yes',$userdata);
    }

    /*删除图片示例*/
    public function delFile(){
        return delFiles('deleteUploadImg');
    }
    // 获取ajax上传文件
    public function upload(){
		return uploadImg('slider');    
	}

	/* 验证码 */
	public function getVerify(){       
		$Verify = new Captcha();
	    $Verify->length   = 4;          //设置属性
	    $Verify->entry();               //
	}
    /*二维码*/
    public function verify(){
        $request = Request::instance();
        $data=$request->param();

       qrcode($data['aliPayUrl']);
       // qrcode('www.baidu.com');
    }

    /*----------------------------------------------------------------------*/
    /*----------------------------------------------------------------------*/
    /*支付*/
    public function pingpp(){
        // if(!userIsLogin()){
        //     return CURD_result(200,'请先登录哦！','');
        // }
        /**
         * Ping++ Server SDK
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可根据自己网站需求按照技术文档编写, 并非一定要使用该代码。
         * 接入支付流程参考开发者中心：https://www.pingxx.com/docs/server ，文档可筛选后端语言和接入渠道。
         * 该代码仅供学习和研究 Ping++ SDK 使用，仅供参考。
         */

        include('../vendor/pingpp/init.php');
        // 示例配置文件，测试请根据文件注释修改其配置
        include('../vendor/pingpp/example/config.php');

        // 此处为 Content-Type 是 application/json 时获取 POST 参数的示例
        // $input_data = json_decode(file_get_contents('php://input'), true);
        // dump('channel='.$_POST['channel']);
        // dump('amount='.$_POST['amount']);
        // if (empty($_POST['channel']) || empty($_POST['amount'])) {
        //     echo 'channel or amount is empty';
        //     exit();
        // }
        // $request = Request::instance();
        // $data=$request->param();

        $channel =$_POST['channel'];// 'alipay_pc_direct';
        $amount = $_POST['amount'];
        $orderNo = substr(md5(time()), 0, 12);

        /**
         * 设置请求签名密钥，密钥对需要你自己用 openssl 工具生成，如何生成可以参考帮助中心：https://help.pingxx.com/article/123161；
         * 生成密钥后，需要在代码中设置请求签名的私钥(rsa_private_key.pem)；
         * 然后登录 [Dashboard](https://dashboard.pingxx.com)->点击右上角公司名称->开发信息->商户公钥（用于商户身份验证）
         * 将你的公钥复制粘贴进去并且保存->先启用 Test 模式进行测试->测试通过后启用 Live 模式
         */

        \Pingpp\Pingpp::setApiKey(APP_KEY);                                         // 设置 API Key
        \Pingpp\Pingpp::setPrivateKeyPath('../vendor/pingpp/example/your_rsa_private_key.pem');   // 设置私钥

        // 设置私钥内容方式2
        // \Pingpp\Pingpp::setPrivateKey(file_get_contents(__DIR__ . '/your_rsa_private_key.pem'));

        /**
         * $extra 在使用某些渠道的时候，需要填入相应的参数，其它渠道则是 array()。
         * 以下 channel 仅为部分示例，未列出的 channel 请查看文档 https://pingxx.com/document/api#api-c-new；
         * 或直接查看开发者中心：https://www.pingxx.com/docs/server；包含了所有渠道的 extra 参数的示例；
         */
        $extra = array();
        switch ($channel) {
            case 'alipay_wap':
                $extra = array(
                    // success_url 和 cancel_url 在本地测试不要写 localhost ，请写 127.0.0.1。URL 后面不要加自定义参数
                    'success_url' => 'http://127.0.0.1/tp/public/index/index',
                    'cancel_url' => 'http://example.com/cancel'
                );
                break;
            case 'alipay_pc_direct':
                $extra = array(
                    // success_url 和 cancel_url 在本地测试不要写 localhost ，请写 127.0.0.1。URL 后面不要加自定义参数
                    'success_url' => 'http://127.0.0.1/tp/public/index/index'
                );
                break;
            case 'bfb_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result',// 百度钱包同步回调地址
                    'bfb_login' => true// 是否需要登录百度钱包来进行支付
                );
                break;
            case 'upacp_wap':
                $extra = array(
                    'result_url' => 'http://example.com/result'// 银联同步回调地址
                );
                break;
            case 'wx_pub':
                $extra = array(
                    // 'open_id' => 'openidxxxxxxxxxxxx'// 
                    'open_id' => 'http://127.0.0.1/tp/public/index/index'// 用户在商户微信公众号下的唯一标识，获取方式可参考 pingpp-php/lib/WxpubOAuth.php
                );
                break;
            case 'wx_pub_qr':
                $extra = array(
                    'product_id' => 'Productid',// 为二维码中包含的商品 //ID，1-32 位字符串，商户可自定义
                );
                break;
            case 'yeepay_wap':
                $extra = array(
                    'product_category' => '1',// 商品类别码参考链接 ：https://www.pingxx.com/api#api-appendix-2
                    'identity_id'=> 'your identity_id',// 商户生成的用户账号唯一标识，最长 50 位字符串
                    'identity_type' => 1,// 用户标识类型参考链接：https://www.pingxx.com/api#yeepay_identity_type
                    'terminal_type' => 1,// 终端类型，对应取值 0:IMEI, 1:MAC, 2:UUID, 3:other
                    'terminal_id'=>'your terminal_id',// 终端 ID
                    'user_ua'=>'your user_ua',// 用户使用的移动终端的 UserAgent 信息
                    'result_url'=>'http://example.com/result'// 前台通知地址
                );
                break;
            case 'jdpay_wap':
                $extra = array(
                    'success_url' => 'http://example.com/success',// 支付成功页面跳转路径
                    'fail_url'=> 'http://example.com/fail',// 支付失败页面跳转路径
                    /**
                    *token 为用户交易令牌，用于识别用户信息，支付成功后会调用 success_url 返回给商户。
                    *商户可以记录这个 token 值，当用户再次支付的时候传入该 token，用户无需再次输入银行卡信息
                    */
                    'token' => 'dsafadsfasdfadsjuyhfnhujkijunhaf' // 选填
                );
                break;
        }


        try {
            $ch = \Pingpp\Charge::create(
                array(
                    //请求参数字段规则，请参考 API 文档：https://www.pingxx.com/api#api-c-new
                    'subject'   => 'Your Subject',
                    'body'      => 'Your Body',
                    'amount'    => $amount,//订单总金额, 人民币单位：分（如订单总金额为 1 元，此处请填 100）
                    'order_no'  => $orderNo,// 推荐使用 8-20 位，要求数字或字母，不允许其他字符
                    'currency'  => 'cny',
                    'extra'     => $extra,
                    'channel'   => $channel,// 支付使用的第三方支付渠道取值，请参考：https://www.pingxx.com/api#api-c-new
                    'client_ip' => $_SERVER['REMOTE_ADDR'],// 发起支付请求客户端的 IP 地址，格式为 IPV4，如: 127.0.0.1
                    'app'       => array('id' => APP_ID)
                )
            );
            if($ch){
                $ch->code=200;
            }
            echo $ch;// 输出 Ping++ 返回的支付凭据 Charge
            // return json_encode($ch);
        } catch (\Pingpp\Error\Base $e) {
            // 捕获报错信息
            if ($e->getHttpStatus() != null) {
                header('Status: ' . $e->getHttpStatus());
                echo $e->getHttpBody();
            } else {
                echo $e->getMessage();
            }
        }
        exit;
    }
    /*查询支付*/
    public function chargeCondition(){
        include('../vendor/pingpp/init.php');
        // 示例配置文件，测试请根据文件注释修改其配置
        include('../vendor/pingpp/example/config.php');
        $request = Request::instance();
        $data=$request->param();

        \Pingpp\Pingpp::setApiKey(APP_KEY);
        // dump($data['id']);
        $id=\Pingpp\Charge::retrieve($_POST['id']);/*'ch_L8qn10mLmr1GS8e5OODmHaL4'*/
        echo $id;
    }


    /*退款*/
    // public function Refund(){
    //     include('../vendor/pingpp/init.php');
    //     // 示例配置文件，测试请根据文件注释修改其配置
    //     include('../vendor/pingpp/example/config.php');
    //     $orderNo = substr(md5(time()), 0, 12);

    //     \Pingpp\Pingpp::setApiKey(APP_KEY);                                         // 设置 API Key
    //     \Pingpp\Pingpp::setPrivateKeyPath('../vendor/pingpp/example/your_rsa_private_key.pem');   // 设置私钥
    //     $ch = \Pingpp\Charge::retrieve('y1u944PmfnrTHyvnL0nD0iD1');//ch_id 是已付款的订单号
    //     $ch->refunds->create(
    //         array(
    //             'amount' => 10,
    //             'description' => 'Refund Description'
    //         )
    //   );
    // }
    /*webhooks*/
    // public function webhook(){
    //     /* *
    //      * Ping++ Server SDK
    //      * 说明：
    //      * 以下代码只是为了方便商户测试而提供的样例代码，商户可根据自己网站需求按照技术文档编写, 并非一定要使用该代码。
    //      * 接入 webhooks 流程参考开发者中心：https://www.pingxx.com/docs/webhooks/webhooks
    //      * 该代码仅供学习和研究 Ping++ SDK 使用，仅供参考。
    //     */

    //     include('../vendor/pingpp/init.php');
    //     /* *
    //      * 验证 webhooks 签名方法：
    //      * raw_data：Ping++ 请求 body 的原始数据即 event ，不能格式化；
    //      * signature：Ping++ 请求 header 中的 x-pingplusplus-signature 对应的 value 值；
    //      * pub_key_path：读取你保存的 Ping++ 公钥的路径；
    //      * pub_key_contents：Ping++ 公钥，获取路径：登录 [Dashboard](https://dashboard.pingxx.com)->点击管理平台右上角公司名称->开发信息-> Ping++ 公钥
    //      */
    //     function verify_signature($raw_data, $signature, $pub_key_path) {
    //         $pub_key_contents = file_get_contents($pub_key_path);
    //         // php 5.4.8 以上，第四个参数可用常量 OPENSSL_ALGO_SHA256
    //         return openssl_verify($raw_data, base64_decode($signature), $pub_key_contents, 'sha256');
    //     }

    //     $raw_data = file_get_contents('php://input');
    //     // 示例
    //     // $raw_data = '{"id":"evt_eYa58Wd44Glerl8AgfYfd1sL","created":1434368075,"livemode":true,"type":"charge.succeeded","data":{"object":{"id":"ch_bq9IHKnn6GnLzsS0swOujr4x","object":"charge","created":1434368069,"livemode":true,"paid":true,"refunded":false,"app":"app_vcPcqDeS88ixrPlu","channel":"wx","order_no":"2015d019f7cf6c0d","client_ip":"140.227.22.72","amount":100,"amount_settle":0,"currency":"cny","subject":"An Apple","body":"A Big Red Apple","extra":{},"time_paid":1434368074,"time_expire":1434455469,"time_settle":null,"transaction_no":"1014400031201506150354653857","refunds":{"object":"list","url":"/v1/charges/ch_bq9IHKnn6GnLzsS0swOujr4x/refunds","has_more":false,"data":[]},"amount_refunded":0,"failure_code":null,"failure_msg":null,"metadata":{},"credential":{},"description":null}},"object":"event","pending_webhooks":0,"request":"iar_Xc2SGjrbdmT0eeKWeCsvLhbL"}';

    //     $headers = \Pingpp\Util\Util::getRequestHeaders();
    //     // 签名在头部信息的 x-pingplusplus-signature 字段
    //     $signature = isset($headers['X-Pingplusplus-Signature']) ? $headers['X-Pingplusplus-Signature'] : NULL;
    //     // 示例
    //     // $signature = 'BX5sToHUzPSJvAfXqhtJicsuPjt3yvq804PguzLnMruCSvZ4C7xYS4trdg1blJPh26eeK/P2QfCCHpWKedsRS3bPKkjAvugnMKs+3Zs1k+PshAiZsET4sWPGNnf1E89Kh7/2XMa1mgbXtHt7zPNC4kamTqUL/QmEVI8LJNq7C9P3LR03kK2szJDhPzkWPgRyY2YpD2eq1aCJm0bkX9mBWTZdSYFhKt3vuM1Qjp5PWXk0tN5h9dNFqpisihK7XboB81poER2SmnZ8PIslzWu2iULM7VWxmEDA70JKBJFweqLCFBHRszA8Nt3AXF0z5qe61oH1oSUmtPwNhdQQ2G5X3g==';

    //     // Ping++ 公钥，获取路径：登录 [Dashboard](https://dashboard.pingxx.com)->点击管理平台右上角公司名称->开发信息-> Ping++ 公钥
    //     $pub_key_path ="../vendor/pingpp/example/pingpp_rsa_public_key.pem";

    //     $result = verify_signature($raw_data, $signature, $pub_key_path);
    //     if ($result === 1) {
    //         // 验证通过
    //     } elseif ($result === 0) {
    //         http_response_code(400);
    //         echo 'verification failed';
    //         exit;
    //     } else {
    //         http_response_code(400);
    //         echo 'verification error';
    //         exit;
    //     }

    //     $event = json_decode($raw_data, true);
    //     if ($event['type'] == 'charge.succeeded') {
    //         $charge = $event['data']['object'];
    //         // ...
    //         http_response_code(200); // PHP 5.4 or greater
    //     } elseif ($event['type'] == 'refund.succeeded') {
    //         $refund = $event['data']['object'];
    //         // ...
    //         http_response_code(200); // PHP 5.4 or greater
    //     } else {
    //     /**
    //      * 其它类型 ...
    //      * - summary.daily.available
    //      * - summary.weekly.available
    //      * - summary.monthly.available
    //      * - transfer.succeeded
    //      * - red_envelope.sent
    //      * - red_envelope.received
    //      * ...
    //      */
    //         http_response_code(200);
    //     }   

    //     // 异常时返回非 2xx 的返回码
    //     // http_response_code(400);
    // }
}


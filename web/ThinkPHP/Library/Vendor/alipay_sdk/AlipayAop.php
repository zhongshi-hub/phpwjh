<?php
require_once 'AopClient.php';
require_once 'AlipayFundTransToaccountTransferRequest.php';
require_once 'AlipaySystemOauthTokenRequest.php';
class AlipayAop{
  private $appId; 
  private $rsaPrivateKey; 
  private $alipayrsaPublicKey; 
  function __construct(){ 

  }
  /*
  * 支付参数 进行支付
  */
  public function Alipay_Funds($Ali_Data){
	    $msg = array();
	    if(!$Ali_Data['osn']){
			$msg['success'] = 2;
			$msg['msg']    = '交易单号为空';
			return $msg;
		}elseif(!$Ali_Data['payee_account']){
			$msg['success'] = 2;
			$msg['msg']    = '收款人帐号为空';
			return $msg;
		}elseif(!$Ali_Data['amount'] ||$Ali_Data['amount']<0.1){
			$msg['success'] = 2;
			$msg['msg']    = '转账金额不能小于0.1';
			return $msg;
		}elseif(!$Ali_Data['payee_real_name']){
			$msg['success'] = 2;
			$msg['msg']    = '收款人姓名为空';
			return $msg;
		}else{
			$aop = new AopClient();
			$aop->gatewayUrl         = 'https://openapi.alipay.com/gateway.do';
			$aop->appId              = $Ali_Data['app_id'];
			$aop->rsaPrivateKey      = 'MIICXAIBAAKBgQCbX2YDb/1k/a0pK7GlMRzMV/HoePGrcfKr/WQoKDC26p9Jv3Y+qblO5Bqke9yJ234OM+cdOIU2WZMmJ2gQuI5FjyRLWJ/Sas09t2LCaOBBT88JO4EWtYBKfISUVPwyuROFf6SseXG7Xq72S58YR6P/QZ+oDFxqoSyep1REEf8zRQIDAQABAoGAed55OOcFvcpQoYN5QtZj/VBaGfuLq+uj6g3GGs1zcHZV3NXF3N7p0ByRXUUeNi+pD2DcvgnQS1I1Xm74bG0mgeYmc0pCu+lQ6nWusUmuAJVLOH5eLn7byunTsy1sMeNcNmrc3f/dY5Ez3UPT9ZRBAbOpjTNh52+7sJljoNEoWB0CQQDIgMTePhBHXIni0aP4rsgx0J3Nwtj6XYYCr87/rUv9sF4e11bVvoiaKDfYiIPmP0mcGRFRBtGWhGBBcadkWZh/AkEAxmDJCwCw7pzU9vVaS/Y9LPmOIHbT5453p8dy+3sjO2JjU+W3tAH8VnChUVeaK9Mv+8EUjabZMQxAbKq60E/yOwJAc5Vn6AY3NCwwgMUBlZaMacstbTRqCMppOptG5TVtnS1S1MymjklsThHpP7ZS8ySAtq/sv50CrZZaNt/h84OC2QJBAMFb/zm5P7wko7PfLFdUOQbIbA8ao6tVAy5HSrzypkGwwc4535gWQ2XhvGtzrrM+0stZxHXZhmO3ZGhG9XYsJpcCQEciKjeYTISSj0ocLvOxkaiJ2OgMdS6lcGJVi124Gk+zrICagTdJgL4Ujqca79lbPebXqbRuLdANg5qmU3tMa/Y=';
			$aop->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCbX2YDb/1k/a0pK7GlMRzMV/HoePGrcfKr/WQoKDC26p9Jv3Y+qblO5Bqke9yJ234OM+cdOIU2WZMmJ2gQuI5FjyRLWJ/Sas09t2LCaOBBT88JO4EWtYBKfISUVPwyuROFf6SseXG7Xq72S58YR6P/QZ+oDFxqoSyep1REEf8zRQIDAQAB';
			$aop->apiVersion         = '1.0';
			$aop->signType           = 'RSA';
			$aop->postCharset        = 'UTF-8';
			$aop->format             = 'json';
			$request = new AlipayFundTransToaccountTransferRequest();
            $data=array(
              'out_biz_no'=>$Ali_Data['osn'],
              'payee_type'=>'ALIPAY_LOGONID',
              'payee_account'=>$Ali_Data['payee_account'],
              'amount'=>$Ali_Data['amount'],
              'payer_show_name'=>$Ali_Data['payer_show_name'],
              'payee_real_name'=>$Ali_Data['payee_real_name'],
              'remark'=>$Ali_Data['remark']
            );
            $request->setBizContent(json_encode($data));
			$result = $aop->execute ( $request);
		    return $result->alipay_fund_trans_toaccount_transfer_response;
		}
  }


  #获取用户信息
  public function Alipay_oauth($code,$app_id){
      $aop = new AopClient();
      $aop->appId              = $app_id;
      $aop->rsaPrivateKey      = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCh5N4s/7A5KupHFbnzv7JskUyTZ8nUTfOtM3XFnSjgKYdMSgXHrhEKye0JWlGuDnsroijxwvXsF4kJrSukpKGq/epSKLGMRuMcTUB0e1cID6HYsAWHJlI0Z23DDvOS5ADvt3bgUv57xI8bs55jXkM5HSwxJINRh76u48HEOKMuzuVpF5s1Y2mYLsmYvl0eFvbz7OlZ3jhMcjAx1zTFSeuim3X3rj528zJT7s7t63lhuF679a5KhD+l2iJJ/7VN17N1vyo4R+kz5r0jVR68xevnikkNVre6n/iYzaaFYz4UYA3COuShHRNR1rOQf+i1dHeL1N3gNLhsL+C0rw+I1LGJAgMBAAECggEBAIxsxsJlbmfH77qE/+yLpKpDRha9+fUrQGhFsKwea+w0WWU099qCA8pF6FMqgprleLUaTUWXBFUq/PTlvmtrWcfFw1BMd+TjP++2mmfu7EjTtmEMHV9jP/6wkCaXe3M4Tg+gJX7ivlaA4lj27jXm94w8364oq7c2dZGhgNtA/VqFN9IK4CqcdZ+QeLzVGjv+GT2U6xwIY3j+LeDQsR5S+I4bL97+VA1Jrol9WgNP0QlkNtsDFkFlUPCvUAx+GbeL/WH+zn+YLWQzlxTin8fU+sPU8dlrwy1Uw3lEm2yLQUR7P9PDS84TFWDqdd+0zYYGFV+GmGfuO/xtuuQQ4OeaU7kCgYEA93GCNT+rytfY5Y2SZSXBFy4ZtyCrlsgNdMyP9Yih54NbxvB3Nb2qd3024OnoKGlVONXsUuXLFJE2Hf9MGtFnsf1OWwsj/rcTjKukuT9qnIkFb0Cw5TrrVOIgxtwVtvDh7g5CqvgfvrhIf+YcJGlVutLY3gwJpu1EmvUBre6WYjsCgYEAp34IqECK+O+qb6k5P7RHVOO1c+Du+WGVdG+GhSADqk0vPxT8RzAFlcLy9mXj+JllY74gsmfnlnf1GgIPpuPYhn3oLI6SnS48fa48SXMRR9Ff9dzoIKekWgLIROtkQLvXF24iioRT84QAwXDTA1vyX1nYhZqxafwInFKrS9ui2wsCgYEA32GdhqzAThKmQDWaX63Br0dy95uKzEg1vaeenq6GWxZ/GA2l0OI0rkJf9JO1fiX5RaH6AqxotVySWmpLLjq1Pj3LKu3XxO9JusiJoKLbgA1C1riz+X1DThIGPQAaqliz3dEJ5oaxQd/js3LtHQHq3wnRtkNvwz6XnLxqk7a8FFkCgYB+1M6DchSKexoJ4hAK8F4PpzrxW8Q6ra159TXdOyfrXvVHBIIg+flQtcY71V4zEx2W0RN7ZXkWFRJSNntZujFtboxQqUnWonuGBFl8mRjd1GuhBz3z9F5dFrxGjCVna5ZuFKUtVRUpUq0rzl7LrWW01JlEWqa8BfETRvqVv+TrjwKBgE3uVX6NtICa0/w5jZT5J02d/Hyy7YDXnag0h8+I6uskbNFQ5sD1b14aRWqZEyjt9XWkVb1m9UR8Nw+Mn1zg8FLjzXdRgZzxmzKFWupCH2jC+fzh8HZAzQbzI97BItj47V6xNM5zGwPUvQXBNRJri7YT9Cu8GCGt2UCCqaIk7Crb';
      $aop->alipayrsaPublicKey = 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCbX2YDb/1k/a0pK7GlMRzMV/HoePGrcfKr/WQoKDC26p9Jv3Y+qblO5Bqke9yJ234OM+cdOIU2WZMmJ2gQuI5FjyRLWJ/Sas09t2LCaOBBT88JO4EWtYBKfISUVPwyuROFf6SseXG7Xq72S58YR6P/QZ+oDFxqoSyep1REEf8zRQIDAQAB';
      $aop->apiVersion         = '1.0';
      $aop->signType           = 'RSA2';
      $aop->postCharset        = 'UTF-8';
      $aop->format             = 'json';
      $request = new  AlipaySystemOauthTokenRequest();
      $request->setGrantType("authorization_code");//设置要操作的类型
      $request->setCode($code);
      $response = $aop->execute($request);
      //rwlog('response',$response);
      $openid = $response->alipay_system_oauth_token_response->user_id;
      return $openid;
  }

}
?>
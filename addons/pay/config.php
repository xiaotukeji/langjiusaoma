<?php

return array (
  0 => 
  array (
    'name' => 'rechargetips',
    'title' => '充值提示文字',
    'type' => 'text',
    'value' => '余额可用于会员升级或购买商品',
  ),
  1 => 
  array (
    'name' => 'moneylist',
    'title' => '充值金额列表',
    'type' => 'array',
    'value' => 
    array (
      '￥10' => '10',
      '￥20' => '20',
      '￥30' => '30',
      '￥50' => '50',
      '￥100' => '100',
    ),
  ),
  2 => 
  array (
    'name' => 'defaultmoney',
    'title' => '默认充值金额',
    'type' => 'text',
    'value' => '10',
  ),
  3 => 
  array (
    'name' => 'minmoney',
    'title' => '最低充值金额',
    'type' => 'text',
    'value' => '0.01',
  ),
  4 => 
  array (
    'name' => 'paytypelist',
    'title' => '支付方式',
    'type' => 'checkbox',
    'options' => 
    array (
      'wechat' => '微信支付',
      'alipay' => '支付宝支付',
    ),
    'value' => 'wechat',
  ),
  5 => 
  array (
    'name' => 'defaultpaytype',
    'title' => '默认支付方式',
    'type' => 'radio',
    'options' => 
    array (
      'wechat' => '微信支付',
      'alipay' => '支付宝支付',
    ),
    'value' => 'wechat',
  ),
  6 => 
  array (
    'name' => 'wechat',
    'title' => '微信',
    'type' => 'array',
    'value' => 
    array (
      'app_id' => 'wx63b27b038583d070',
      'app_secret' => '517b10d2e4269113e1ba970884305627',
      'mch_id' => '1675253585',
      'key' => 'jgdjusJASGDJagdjasjjhgJASJDWEEC6',
      'mode' => '0',
      'log' => '0',
    ),
    'tip' => '微信参数配置',
  ),
  7 => 
  array (
    'name' => 'alipay',
    'title' => '支付宝',
    'type' => 'array',
    'value' => 
    array (
      'app_id' => '2021004142627138',
      'private_key' => 'MIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQDvNGM2UzoSNBwh59FfsaZq/4J0FNMmzOEypXWLU6yyRFbcsnURhB6iP4NdwN+a4laCU7dfWejeXNBhIMQpKFVf4Gtu8EUp83+YX13aMMLvnn2i95W9V6kK34QsvSS5NCmNYQGd7DVE5ZWTPzC1uzUGmTatgM9mDGPxIpa5KWgGUu8CSXjRRNi2xIQY2xDNU7csVFWwFbQXG2N18I15GlbnNya/KwKdD+g0wWSOUeE/kmIrQu2IaQa7MmWfafRykVkXqbPtO06pYJwb1Fy85mGwbu6UHuauc5rTi0eQ5Xiq6bl/r8/08/Ht7ntoAGUE6CB4vALfRirPJa9UFTzk1vcXAgMBAAECggEBAObhEcGloy9ezKiNMDHFLnOoGyofz45rwhE4UrQgF1gZTh3GwegeNdM0qn8bYrgELqusUhnelj3KJ/cXwT5Yh876tbdmgdt0A3v20IeA+SZ/O5TIk+clbSvpgZINHh0Ek/a54u2ix+ewEScGnuVJxGySBYyB/6zn2K8uS3x80sMF+5/bOxUqrcwniHJpzrWUx+ahFugLa+GvhonOaC+5Acbj2z4r23rcTZUuzbp4mNDEDEeTNCwXsyiCiaZesNkGD9DxQEb04tW6+7Wygxu7mPCBt/7LB8cod0ux47mh9NfL1VFvRy2ynF2Y14nDA448dbrHyyjSy1dLqjRRykdM5QECgYEA+leVtzoitNHjNshVsSlDbrcn7HzTXSAQ3aloJ7S2CqDJ1KaPw38kcFMNGjOkcqXcBbS9uzPNb8BKP07uAybgasg8wBud02aqeKbdBUyoHQzs1158QNnTfthjjOmgUC2gbvY78lueFwekUM48Hvaqeoog/4fiH1thXDPJr6+fS2cCgYEA9JxdM8e9Rpszx0S6E+o90H1LwuDRyJXdpekywQLn3pWYyySnch1nFFnFPm8+RUhc+2JJgwPpATTF0mxsb/xPBXahCoOI6k0MYiKCwD91D/plRg5ZvfEuLt898RBXQ0HwCoo2BjYP6evep/ORhLLoMNy2HtOP/rhHgfmOi0LJWNECgYEAjzbT24JSoIcCKTDqdPNaNFhraoruj/PsMiLcBvsGuYXKcPDohbqSqf+ZG0g3566i9FC1ygaUnD2xPA53cy4mbHAo8O3bmDi/hU5QWtvDwPrH812GT8NNjt9T9CUjJTs08MXE/Z0UD7C7e86/7ibG2Ft1DTo7Th0E0a5+zxb1VjkCgYEA1JU/+sxQFpizey6jeMNOTW9W9FpmdyinpJTyYwOfd7YTQBju3SHof6s03HnZaGTnSiG1OYOgcEfo1GMeKoTgOCNM+dIun1GuvWq4r1N27Rf0A9pc69I7DMk/D83fyLf5YW+UW/mIAwfWTahEg0rot+5Y6Jl8vyZwHuk/lI/KZMECgYAinsKnPy71WLOHYnD8xdunGxu6GO1rR3ZYgOuh43mN8JOgytA6DsxAKtI0V354WiLsAsWeDuBQJo/1aJbj7cRy/YTANGZYSUr2FhZn8KtehMF/ewaYLmCj8vZ7b98KUX4pbjyyfY0aesmlNRpOAW1sDbT/fWVqpc6DOfYr8RvQhw==',
      'signtype' => 'publickey',
      'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtiaGRd3hXCBVHxmKrezQue0Dmz1JkoiutL5SNeR/i/sVDB9jTKVM+nO15iAwKsH7+4CI4hTyCnLPDjcVvOKAHR8Zflu6PZZgwdI9H42Trq0271VbFlBXds4SXC+XbhGJj/TQq++eKcwM9l56eB4zfj6SWcKzCo6DqO7JrNj7YSf1DivWCPMNa//G1AMXtucj1VBHxR927EqzB9HPLdkFBPXPl6S6nwomJo+ftFiMaSOsvTUuQH3h+8KopBdgvQLsUE5stoqCSbGwl85G6BUhsuZJONAMKfMBD/wRz0ClLcNHs49JZQZNkN3/7/EzJfJbzEKp3gemmmja3EpExUTgtQIDAQAB',
      'mode' => '0',
      'isper' => '0',
      'log' => '1',
    ),
    'tip' => '支付宝参数配置',
  ),
);

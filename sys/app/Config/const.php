<?php
/**
 * Copyright (c) 2019, Mallento JAPAN
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms,
 * with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 * Neither the name of the <ORGANIZATION> nor the names of its contributors may
 * be used to endorse or promote products derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING,
 * BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
 * EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * */

// 都道府県
Configure::write('arrPref', array(
    '北海道' => '北海道',
    '青森県' => '青森県',
    '岩手県' => '岩手県',
    '宮城県' => '宮城県',
    '秋田県' => '秋田県',
    '山形県' => '山形県',
    '福島県' => '福島県',
    '茨城県' => '茨城県',
    '栃木県' => '栃木県',
    '群馬県' => '群馬県',
    '埼玉県' => '埼玉県',
    '千葉県' => '千葉県',
    '東京都' => '東京都',
    '神奈川県' => '神奈川県',
    '新潟県' => '新潟県',
    '富山県' => '富山県',
    '石川県' => '石川県',
    '福井県' => '福井県',
    '山梨県' => '山梨県',
    '長野県' => '長野県',
    '岐阜県' => '岐阜県',
    '静岡県' => '静岡県',
    '愛知県' => '愛知県',
    '三重県' => '三重県',
    '滋賀県' => '滋賀県',
    '京都府' => '京都府',
    '大阪府' => '大阪府',
    '兵庫県' => '兵庫県',
    '奈良県' => '奈良県',
    '和歌山県' => '和歌山県',
    '鳥取県' => '鳥取県',
    '島根県' => '島根県',
    '岡山県' => '岡山県',
    '広島県' => '広島県',
    '山口県' => '山口県',
    '徳島県' => '徳島県',
    '香川県' => '香川県',
    '愛媛県' => '愛媛県',
    '高知県' => '高知県',
    '福岡県' => '福岡県',
    '佐賀県' => '佐賀県',
    '長崎県' => '長崎県',
    '熊本県' => '熊本県',
    '大分県' => '大分県',
    '宮崎県' => '宮崎県',
    '鹿児島県' => '鹿児島県',
    '沖縄県' => '沖縄県',
));

Configure::write('arrPrefCode', array(
    '北海道' => 'pref01',
    '青森県' => 'pref02',
    '岩手県' => 'pref03',
    '宮城県' => 'pref04',
    '秋田県' => 'pref05',
    '山形県' => 'pref06',
    '福島県' => 'pref07',
    '茨城県' => 'pref08',
    '栃木県' => 'pref09',
    '群馬県' => 'pref10',
    '埼玉県' => 'pref11',
    '千葉県' => 'pref12',
    '東京都' => 'pref13',
    '神奈川県' => 'pref14',
    '新潟県' => 'pref15',
    '富山県' => 'pref16',
    '石川県' => 'pref17',
    '福井県' => 'pref18',
    '山梨県' => 'pref19',
    '長野県' => 'pref20',
    '岐阜県' => 'pref21',
    '静岡県' => 'pref22',
    '愛知県' => 'pref23',
    '三重県' => 'pref24',
    '滋賀県' => 'pref25',
    '京都府' => 'pref26',
    '大阪府' => 'pref27',
    '兵庫県' => 'pref28',
    '奈良県' => 'pref29',
    '和歌山県' => 'pref30',
    '鳥取県' => 'pref31',
    '島根県' => 'pref32',
    '岡山県' => 'pref33',
    '広島県' => 'pref34',
    '山口県' => 'pref35',
    '徳島県' => 'pref36',
    '香川県' => 'pref37',
    '愛媛県' => 'pref38',
    '高知県' => 'pref39',
    '福岡県' => 'pref40',
    '佐賀県' => 'pref41',
    '長崎県' => 'pref42',
    '熊本県' => 'pref43',
    '大分県' => 'pref44',
    '宮崎県' => 'pref45',
    '鹿児島県' => 'pref46',
    '沖縄県' => 'pref47',
));

// 性別
Configure::write('arrGender', array(
    'male' => '男性',
    'female' => '女性',
));

// メルマガ希望
Configure::write('arrMailMagFlg', array(
    '1' => '希望する',
    '0' => '希望しない',
));

// カスタマーステータス
Configure::write('arrCustomerStatus', array(
    '0' => '仮登録・無効',
    '1' => '正会員・有効',
    '2' => '停止中',
));

// 販売者ステータス
Configure::write('arrSellerStatus', array(
    '0' => '仮登録・無効',
    '1' => '正会員・有効',
//    '2' => '登録・編集制限',
    '3' => 'ログイン制限',
    '4' => '強制退会',
));

// 会員ランク
Configure::write('arrUserRank', array(
    'general' => '一般会員',
    'official' => '公式会員',
));

// 表示件数
Configure::write('arrDispNum', array(
    '20' => '20',
    '40' => '40',
    '60' => '60',
    '80' => '80',
    '100' => '100',
));

// ポイント
Configure::write('arrPointList', array(
    '1000' => '1000',
    '2000' => '2000',
    '3000' => '3000',
    '5750' => '5750',
    '12000' => '12000',
    '37500' => '37500',
));

// ポイント購入金額
Configure::write('arrPointRateList', array(
    '1000' => '1080',
    '2000' => '2160',
    '3000' => '3240',
    '5750' => '5400',
    '12000' => '10800',
    '37500' => '32400',
));

// 支払方法
Configure::write('arrPaymentMethod', array(
    'credit_card' => 'クレジットカード',
    'bank_transfer' => '銀行振込',
));

// ポイント購入
Configure::write('arrPaymentStatus', array(
    '0' => '申請中',
    '1' => '決済完了',
    '2' => '決済不可',
    '99' => 'キャンセル',
));

// カテゴリ
Configure::write('arrCategory', array(
    '行きます（国内）' => array(
        '行きます（国内）' => '　行きます（国内）',
    ),
    '行きます（海外）' => array(
        '行きます（海外）' => '　行きます（海外）',
    ),
    'あります（国内）' => array(
        'あります（国内）' => '　あります（国内）',
    ),
    'あります（海外）' => array(
        'あります（海外）' => '　あります（海外）',
    ),
    'さがしてます（国内）' => array(
        'さがしてます（国内）' => '　さがしてます（国内）',
    ),
    'さがしてます（海外）' => array(
        'さがしてます（海外）' => '　さがしてます（海外）',
    ),
    'その他' => array(
        'その他' => '　その他',
),
));

// 販売ステータス
Configure::write('arrSelling', array(
        '1' => '販売中',
        '0' => '一時停止',
        '2' => '販売終了',
));

// 換金申請
Configure::write('arrCashingStatus', array(
    '0' => '換金申請中',
    '1' => '換金済み',
    '2' => 'キャンセル',
    '3' => '換金不可',
));

// メッセージステータス
Configure::write('arrMessageStatus', array(
    '0' => '下書き',
    '1' => '送信済み',
    '2' => 'ゴミ箱',
));

Configure::write('mail', array(
    'thankYou' => array(
        'templete' => 'thank_you',
        'subject' => '新規登録が完了しました',
    ),
    'purchaseComplete' => array(
        'templete' => 'purchase_complete',
        'subject' => '商品の購入が完了しました',
    ),
    'purchasedToSaller' => array(
        'templete' => 'purchased_to_saller',
        'subject' => 'あなたの商品が購入されました。',
    ),
    'pointPurchaseComplete' => array(
        'templete' => 'point_purchase_complete',
        'subject' => '決済が完了しました',
    ),
    'messageToSaller' => array(
        'templete' => 'message_to_saller',
        'subject' => '販売者にメッセージを送りました',
    ),
    'notificationToSaller' => array(
        'templete' => 'notification_to_saller',
        'subject' => '出品者に気になるを通知しました',
    ),
    'reissuePassword' => array(
        'templete' => 'reissue_password',
        'subject' => 'パスワードの再発行が完了しました',
    ),
));


Configure::write('info', array(
    'siteName' => 'サイト名',
    'domain' => 'sample.com',
    'adminMail' => 'info@sample.com',
    'sendDomain' => 'noreply@sample.com'
));

Configure::write('page', array(
    'main' => 'サイト名',
    'order' => '注文方法',
    'send' => '入稿方法',
    'payment' => '支払方法',
    'adminMail' => 'info@sample.com',
    'sendDomain' => 'noreply@sample.com'
));

Configure::write('html', array(
    'privacy' => 'サイト名｜個人情報保護について',
    'law' => 'サイト名｜特定商法取引に基づく表記',
    'guide' => 'サイト名｜購入者ガイド',
    'guide_sale' => 'サイト名｜出品者ガイド',
    'contact' => 'サイト名｜お問い合わせ',
    'cart' => 'サイト名｜カート',
    'category' => 'サイト名｜カテゴリ一覧',
    'detail' => 'サイト名｜商品詳細',
    'items' => 'サイト名｜商品一覧',
    'mypage' => 'サイト名｜マイページ',
    'tag' => 'サイト名｜タグ一覧',
    'member' => 'サイト名｜新規会員登録',
    'kinds' => 'サイト名｜ジャンル一覧',
    'register' => 'サイト名｜登録情報管理',
    'itemRegister' => 'サイト名｜商品登録ページ',
    'myPurchase' => 'サイト名｜マイページ購入者用管理',
    'login' => 'サイト名｜ログイン',
));

Configure::write('rating', array(
    '1' => '悪い',
    '2' => 'やや悪い',
    '3' => '普通',
    '4' => '良い',
    '5' => '大変良い',
));

Configure::write('notice', array(
    '1' => 'ON',
    '0' => 'OFF',
));

Configure::write('del_flg', array(
    '0' => '未削除',
    '1' => '削除済',
));


define("DEF_DELIV_DAYS",7);    //お届け日数　00営業日
define("IS_TEST",true);    //テスト環境の設定
define("SENDMAIL_FLG",true);    //送信処理設定

    define("SMTP_HOST","ssl://smtp.gmail.com");
    define("SMTP_PORT","465");
    define("SMTP_FROM","admin@sample.com");
    define("SMTP_PROTOCOL","SMTP_AUTH");// 'SMTP',SMTP_AUTH,POP_BEFORE
    define("SMTP_USER","admin@sample.com");
    define("SMTP_PASS","admin");

define('MSG_LOGIN_ERR','IDかパスワードが間違っています');
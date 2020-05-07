function InitIdentity(identity) {
    TokenPay.init(identity);
}

function GetTokenAndRun() {
    TokenPay.createToken({
        number: document.getElementById('creditCardNumber').value, //カード番号
        name: document.getElementById('creditCardName').value, //カード名義
        expMonth: document.getElementById('creditCardExpireMonth').value, //カード有効期限(月)
        expYear: document.getElementById('creditCardExpireYear').value, //カード有効期限(年)
        cvv2: document.getElementById('creditCardCVV2').value //カードCVV2
    }, GetTokenCallback);
}

function GetTokenCallback(result) {
    if (result.token) {
        document.getElementById('paymentToken').value = result.token;
        document.getElementById('creditCardNumber').removeAttribute("name");
        document.getElementById('creditCardName').removeAttribute("name");
        document.getElementById('creditCardExpireMonth').removeAttribute("name");
        document.getElementById('creditCardExpireYear').removeAttribute("name");
        document.getElementById('creditCardCVV2').removeAttribute("name");
        if (document.getElementById('paymentFormName')) {
            document.getElementById(document.getElementById('paymentFormName').value).submit();
        } else {
            document.getElementById('OrderPointForm').submit();
        }
    } else {
        switch (result.errorCode) {
            case 101:
            case 201:
            case 301:
                alert("設定情報が不正です。");
                break;
            case 103:
            case 203:
            case 303:
                alert("カード番号が不正です。");
                break;
            case 104:
            case 204:
            case 304:
                alert("カード名義が不正です。");
                break;
            case 105:
            case 205:
            case 305:
                alert("カード有効期限（年）が不正です。");
                break;
            case 106:
            case 206:
            case 306:
                alert("カード有効期限（月）が不正です。");
                break;
            case 107:
            case 207:
            case 307:
                alert("セキュリティコードが不正です。");
                break;
            default:
                alert("カード情報を正しく入力してください。");
        }
    }
}

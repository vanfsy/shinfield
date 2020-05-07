function InitIdentity(identity) {
    EpsilonToken.init(identity);
}

function GetTokenAndRun() {
    EpsilonToken.getToken({
        cardno: document.getElementById('creditCardNumber').value, //カード番号
        holdername: document.getElementById('creditCardName').value, //カード名義
        expire: document.getElementById('creditCardExpireYear').value + document.getElementById('creditCardExpireMonth').value, //カード有効期限
        securitycode: document.getElementById('creditCardCVV2').value //カードCVV2
    }, GetTokenCallback);
}

function GetTokenCallback(result) {
    if (result.resultCode != 000) {
        alert("カード情報を正しく入力してください。");
    } else {
        document.getElementById('paymentToken').value = result.tokenObject.token;
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
    }
}

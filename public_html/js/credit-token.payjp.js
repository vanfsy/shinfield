function InitIdentity(identity) {
    Payjp.setPublicKey(identity);
}

function GetTokenAndRun() {
    Payjp.createToken({
        number: document.getElementById('creditCardNumber').value, //カード番号
        exp_month: document.getElementById('creditCardExpireMonth').value, //カード有効期限（月）
        exp_year: document.getElementById('creditCardExpireYear').value, //カード有効期限（年）
        cvc: document.getElementById('creditCardCVV2').value //カードCVV2
    }, GetTokenCallback);
}

function GetTokenCallback(status, response) {
    if (status !== 200 || response.error) {
        alert("カード情報を正しく入力してください。");
    } else {
        document.getElementById('paymentToken').value = response.id;
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

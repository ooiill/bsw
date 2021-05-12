'use strict';

bsw.configure({
    data: {},
    method: {},
    logic: {
        loginAnimation: function loginAnimation(v) {
            v.$nextTick(function () {
                bsw.doAnimateCSS('.login-box > div.left', 'flip', '1.2s');
            });
        }
    }
});

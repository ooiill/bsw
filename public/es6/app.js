bsw.configure({
    data: {
        imgRealtime: {},
    },
    method: {
        previewImageRealtime(event, field) {
            let that = this;
            that.uploaderChange(event, field);
            if (typeof event.file.response !== 'undefined') {
                that.imgRealtime[field].url = event.file.response.sets.attachment_url;
            }
        },
    },
    logic: {
        loginAnimation(v) {
            v.$nextTick(function () {
                bsw.doAnimateCSS('.login-box > div.left', 'flip', '1.2s');
            });
        }
    }
});

/*! Anyone */
/*! CustomerWeb - v0.0.1 - 2021-05-08 */
"use strict";bsw.configure({data:{imgRealtime:{}},method:{previewImageRealtime:function(a,b){var c=this;c.uploaderChange(a,b),"undefined"!=typeof a.file.response&&(c.imgRealtime[b].url=a.file.response.sets.attachment_url)}},logic:{loginAnimation:function(a){a.$nextTick(function(){bsw.doAnimateCSS(".login-box > div.left","flip","1.2s")})}}});
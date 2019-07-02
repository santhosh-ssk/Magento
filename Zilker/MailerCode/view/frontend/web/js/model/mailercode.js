define([
    'jquery',
    'mage/storage',
    'jquery/jquery-storageapi'
],function ($) {
    const ISAPPLIED = 'isApplied';
    const CODE = 'code';
    let isApplied;
    let code;
    let storage = $.initNamespaceStorage('mailercode-local-storage').localStorage;

    return {
        getIsApplied : function () {
            return isApplied;
        },
        setIsApplied : function (value) {
            isApplied = value;
        },
        getMailerCode: function () {
            return code;
        },
        setMailerCode: function (value) {
            code = value;
        },
        loadData: function () {
            let data = storage.get('mailercode');
            if (data) {
                data = JSON.parse(data);
                this.setIsApplied(data[ISAPPLIED]);
                this.setMailerCode(data[CODE]);
            } else {
                this.setIsApplied(false);
            }
        },
        saveData: function () {
            let data = {
                ISAPPLIED : this.getIsApplied(),
                CODE : this.getMailerCode()
            };
            storage.save(JSON.stringify(data));
        }
    };
});
import Vue from 'vue';

// Formatting gender integer to string
export default function (value, t) {
    value = parseInt(value);

    switch(value) {
        case 1:
            return t('global.female');
        case 0:
            return t('global.male');
        default:
            return 'N/A';
    }
};

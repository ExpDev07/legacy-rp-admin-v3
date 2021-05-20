import moment from 'moment';

// Formatting seconds to human readable time
export default function (value, includeSeconds) {
    const format = includeSeconds ? 'MMM DD, YYYY h:mm:ss A' : 'lll';

    return moment.utc(value).local().format(format);
};

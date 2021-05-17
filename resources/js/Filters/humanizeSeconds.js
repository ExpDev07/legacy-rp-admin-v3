import moment from 'moment';

// Formatting seconds to human readable time
export default function (value) {
    return moment.duration(parseInt(value), 'seconds').humanize();
};

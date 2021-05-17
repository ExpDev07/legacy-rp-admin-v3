import moment from 'moment';

// Formatting seconds to human readable time
export default formatSeconds(value) {
    return moment.duration(parseInt(value), "seconds").humanize();
};

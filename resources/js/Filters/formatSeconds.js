import Vue from 'vue';
import moment from 'moment';

// Formatting seconds to human readable time
Vue.filter("formatSeconds", function(value) {
    return moment.duration(parseInt(value), "seconds").humanize();
});
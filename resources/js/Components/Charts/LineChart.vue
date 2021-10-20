<script>
import {Line} from 'vue-chartjs';

export default {
    extends: Line,
    data() {
        const _this = this;

        let options = {
            devicePixelRatio: 2,
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    display: true,
                    ticks: {
                        beginAtZero: true
                    },
                    gridLines: {
                        display: true,
                        color: "rgba(128, 128, 128, 0.3)"
                    }
                }],
                xAxes: [{
                    display: false,
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            },
            title: {
                display: true,
                text: this.title,
                fontSize: 13
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
        };

        if (this.formatAsMoney) {
            options.tooltips.callbacks = {
                label: function(tooltipItem, data) {
                    return _this.labels[tooltipItem.datasetIndex] + ': ' + _this.numberFormat(tooltipItem.yLabel, 2, true);
                }
            };
        }

        return {
            options: options
        };
    },
    mounted() {
        let datasets = [];
        for (let x = 0; x < this.data.length; x++) {
            while (this.data[x].length < this.data[0].length) {
                this.data[x].unshift(null);
            }

            datasets.push({
                label: this.labels[x],
                data: this.data[x],
                backgroundColor: 'rgba(' + this.colors[x] + ', 0.3)',
                fill: true,
                borderColor: 'rgba(' + this.colors[x] + ', 1)',
                borderWidth: 1
            });
        }

        this.renderChart({
            labels: this.dataLabels,
            datasets: datasets
        }, this.options);
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        colors: {
            type: Array,
            required: true,
        },
        labels: {
            type: Array,
            required: true,
        },
        dataLabels: {
            type: Array,
            required: true,
        },
        data: {
            type: Array,
            required: true,
        },
        formatAsMoney: {
            type: Boolean,
            default: false,
        }
    }
}
</script>

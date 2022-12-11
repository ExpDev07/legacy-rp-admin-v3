<script>
import {Bar} from 'vue-chartjs';

export default {
    extends: Bar,
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
                    },
                    stacked: true
                }],
                xAxes: [{
                    display: false,
                    gridLines: {
                        display: false
                    },
                    stacked: true
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
                itemSort: function (a, b) {
                    return b.datasetIndex - a.datasetIndex
                }
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
        };

        options.tooltips.callbacks = {
            label: function(tooltipItem, data) {
                const label = _this.tooltips[tooltipItem.index];

                return label[tooltipItem.datasetIndex] || "";
            }
        };

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

            let bg, fg;

            if (x >= this.colors.length || !this.colors[x]) {
                bg = 'rgba(0, 0, 0, 0)';
                fg = 'rgba(0, 0, 0, 0)';
            } else {
                bg = 'rgba(' + this.colors[x] + ', 0.3)';
                fg = 'rgba(' + this.colors[x] + ', 1)';
            }

            datasets.push({
                data: this.data[x],
                backgroundColor: bg,
                fill: true,
                borderColor: fg,
                borderWidth: 1,
                grouped: true
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
        tooltips: {
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
        isCasinoChart: {
            type: Boolean,
            default: false,
        }
    }
}
</script>

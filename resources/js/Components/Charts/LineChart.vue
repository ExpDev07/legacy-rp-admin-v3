<script>
import {Line} from 'vue-chartjs';

import ChartTrendline from 'chartjs-plugin-trendline';

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

        if (this.weaponChart) {
            options.scales.xAxes[0].display = true;

            options.scales.xAxes[0].scaleLabel = {
                display: true,
                labelString: this.weaponChart.x
            };

            options.scales.yAxes[0].scaleLabel = {
                display: true,
                labelString: this.weaponChart.y
            };
        }

        if (this.isCasinoChart) {
            options.tooltips.callbacks = {
                label: function(tooltipItem, data) {
                    const label = _this.labels[tooltipItem.datasetIndex];

                    if (tooltipItem.datasetIndex === 4) {
                        tooltipItem.yLabel = _this.numberFormat(tooltipItem.yLabel, 2, false) + '%';
                    } else {
                        tooltipItem.yLabel = _this.numberFormat(tooltipItem.yLabel, 2, true);
                    }

                    return label + ': ' + tooltipItem.yLabel;
                }
            };
        }

        if (this.smooth || this.weaponChart) {
            options.elements = {
                point: {
                    radius: 0
                }
            };
        }

        return {
            options: options
        };
    },
    mounted() {
        this.addPlugin(ChartTrendline);

        let datasets = [];
        for (let x = 0; x < this.data.length; x++) {
            while (this.data[x].length < this.data[0].length) {
                this.data[x].unshift(null);
            }

            let bg, fg, lineCol;

            if (x >= this.colors.length || !this.colors[x]) {
                bg = 'rgba(0, 0, 0, 0)';
                fg = 'rgba(0, 0, 0, 0)';
                lineCol = 'rgba(0, 0, 0, 0)';
            } else {
                bg = 'rgba(' + this.colors[x] + ', 0.3)';
                fg = 'rgba(' + this.colors[x] + ', 1)';
                lineCol = 'rgba(' + this.colors[x] + ', 1)';
            }

            let dataset = {
                label: this.labels[x],
                data: this.data[x],
                backgroundColor: bg,
                fill: true,
                borderColor: fg,
                borderWidth: 1
            };

            if (this.smooth) {
                dataset.trendlineLinear = {
                    style: lineCol,
                    lineStyle: "dotted",
                    width: 2
                };
            }

            datasets.push(dataset);
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
        isCasinoChart: {
            type: Boolean,
            default: false,
        },
        weaponChart: {
            type: Object,
            default: false,
        },
        smooth: {
            type: Boolean,
            default: false,
        }
    }
}
</script>

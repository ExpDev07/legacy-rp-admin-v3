<script>
import {Candlestick} from 'vue-chartjs-financial';

export default {
    extends: Candlestick,
    data() {
        const _this = this;

        const min = Math.min.apply(null, this.data[0].map(d => {
            return Math.min.apply(null, [
                d.o,
                d.c,
                d.h,
                d.l
            ]);
        }));
        const max = Math.max.apply(null, this.data[0].map(d => {
            return Math.max.apply(null, [
                d.o,
                d.c,
                d.h,
                d.l
            ]);
        }));

        return {
            options: {
                devicePixelRatio: 2,
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            min: min - 10 < 0 ? 0 : min - 10,
                            max: max + 10
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
                    bodyFontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
                    bodyFontStyle: 'bold',
                    displayColors: false,
                    callbacks: {
                        title: function (tooltipItems, data) {
                            return _this.dataLabels[tooltipItems[0].index];
                        },
                        label: function (tooltipItem, data) {
                            const value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            return [
                                'Opening: ' + _this.numberFormat(value.o, 2, _this.formatAsMoney),
                                'Highest: ' + _this.numberFormat(value.h, 2, _this.formatAsMoney),
                                'Lowest:  ' + _this.numberFormat(value.l, 2, _this.formatAsMoney),
                                'Closing: ' + _this.numberFormat(value.c, 2, _this.formatAsMoney),
                            ];
                        }
                    }
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
            }
        };
    },
    mounted() {
        let datasets = [];
        for (let x = 0; x < this.data.length; x++) {
            datasets.push({
                label: this.title,
                data: this.data[x]
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

<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("statistics.title") }}
            </h1>
            <p>
                {{ t("statistics.description") }}
            </p>
        </portal>

        <template>
            <div class="bg-gray-100 p-6 rounded shadow-lg max-w-full w-map -mt-7 dark:bg-gray-300">
                <canvas id="ban_stats" class="w-full" height="400"></canvas>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <canvas id="character_stats" class="w-full" height="400"></canvas>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <canvas id="lucky_wheel_stats" class="w-full" height="400"></canvas>
            </div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import "chart.js";

export default {
    layout: Layout,
    methods: {
        renderChart(id, titles, labels, data, colors) {
            const ctx = document.getElementById(id).getContext('2d');
            let datasets = [
                {
                    label: titles[0],
                    data: data[0],
                    backgroundColor: 'rgba(' + colors[0] + ', 0.3)',
                    fill: true,
                    borderColor: 'rgba(' + colors[0] + ', 1)',
                    borderWidth: 1
                }
            ];
            if (titles.length > 1) {
                datasets.push({
                    label: titles[1],
                    data: data[1],
                    backgroundColor: 'rgba(' + colors[1] + ', 0.3)',
                    fill: true,
                    borderColor: 'rgba(' + colors[1] + ', 1)',
                    borderWidth: 1
                });
            }
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
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
                    tooltips: {
                        mode: 'index',
                        intersect: false,
                    },
                    hover: {
                        mode: 'nearest',
                        intersect: true
                    },
                }
            });
        }
    },
    mounted() {
        const _this = this;

        $(document).ready(function() {
            _this.renderChart(
                'ban_stats',
                [_this.t('statistics.bans'), _this.t('statistics.warnings')],
                _this.bans.labels,
                [_this.bans.data, _this.warnings.data],
                ['54, 162, 235', '145, 55, 235']
            );
            _this.renderChart(
                'character_stats',
                [_this.t('statistics.creations'),
                _this.t('statistics.deletions')],
                _this.creations.labels,
                [_this.creations.data, _this.deletions.data],
                ['87, 235, 54', '235, 54, 54']
            );
            _this.renderChart(
                'lucky_wheel_stats',
                [_this.t('statistics.lucky_wheel')],
                _this.luckyWheel.labels,
                [_this.luckyWheel.data],
                ['235, 145, 55']
            );
        });
    },
    props: {
        bans: {
            type: Object,
            required: true,
        },
        warnings: {
            type: Object,
            required: true,
        },
        creations: {
            type: Object,
            required: true,
        },
        deletions: {
            type: Object,
            required: true,
        },
        luckyWheel: {
            type: Object,
            required: true,
        },
    }
}
</script>

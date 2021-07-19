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
                <canvas id="warning_stats" class="w-full" height="400"></canvas>
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
        renderChart(id, title, labels, data) {
            const ctx = document.getElementById(id).getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: title,
                        data: data,
                        backgroundColor: 'rgba(54, 162, 235, 0.3)',
                        fill: true,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
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
            _this.renderChart('ban_stats', _this.t('statistics.bans'), _this.bans.labels, _this.bans.data);
            _this.renderChart('warning_stats', _this.t('statistics.warnings'), _this.warnings.labels, _this.warnings.data);
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
    }
}
</script>

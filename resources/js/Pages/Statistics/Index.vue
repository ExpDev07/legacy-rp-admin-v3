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
                <LineChart
                    :data="[bans.data, warnings.data]"
                    :data-labels="bans.labels"
                    :labels="[t('statistics.bans'), t('statistics.warnings')]"
                    :colors="['235, 54, 54', '235, 145, 55']"
                    :title="t('statistics.titles.ban_warn')"
                    class="w-full"
                ></LineChart>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <FinancialChart
                    :data="[banMove.data]"
                    :data-labels="banMove.labels"
                    :format-as-money="false"
                    :title="t('statistics.titles.bans_move')"
                    class="w-full"
                ></FinancialChart>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <FinancialChart
                    :data="[economy.data]"
                    :data-labels="economy.labels"
                    :format-as-money="true"
                    :title="t('statistics.titles.economy')"
                    class="w-full"
                ></FinancialChart>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <LineChart
                    :data="[creations.data, deletions.data]"
                    :data-labels="creations.labels"
                    :labels="[t('statistics.creations'), t('statistics.deletions')]"
                    :colors="['87, 235, 54', '235, 54, 54']"
                    :title="t('statistics.titles.character')"
                    class="w-full"
                ></LineChart>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <LineChart
                    :data="[luckyWheel.data]"
                    :data-labels="luckyWheel.labels"
                    :labels="[t('statistics.lucky_wheel')]"
                    :colors="['55, 145, 235']"
                    :title="t('statistics.titles.lucky_wheel')"
                    class="w-full"
                ></LineChart>
            </div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import LineChart from '../../Components/Charts/LineChart';
import FinancialChart from '../../Components/Charts/FinancialChart';

export default {
    components: {
        LineChart,
        FinancialChart
    },
    layout: Layout,
    props: {
        bans: {
            type: Object,
            required: true,
        },
        banMove: {
            type: Object,
            required: true,
        },
        economy: {
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

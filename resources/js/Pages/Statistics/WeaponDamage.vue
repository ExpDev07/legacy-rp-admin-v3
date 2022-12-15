<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ weapon }} ({{ weaponHash }})
            </h1>
            <p>
                {{ t("weapon_damage.description") }}
            </p>
        </portal>

        <template>
            <div class="bg-gray-100 p-6 rounded shadow-lg max-w-full w-map -mt-7 dark:bg-gray-300">
                <LineChart
                    :data="[average.data]"
                    :data-labels="average.labels"
                    :labels="['']"
                    :colors="['235, 54, 54']"
                    :title="t('weapon_damage.average_damage')"
                    :weaponChart="{x: t('weapon_damage.damage'), y: t('weapon_damage.amount')}"
                    class="w-full"
                ></LineChart>
            </div>
        </template>

    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import LineChart from '../../Components/Charts/LineChart';

export default {
    components: {
        LineChart,
    },
    layout: Layout,
    props: {
        weapon: {
            type: String,
            required: true,
        },
        weaponHash: {
            type: Number,
            required: true,
        },
        average: {
            type: Object,
            required: true,
        }
    },
}
</script>

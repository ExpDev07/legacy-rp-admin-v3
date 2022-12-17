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

            <div class="pt-10 border-gray-500 border-t-2 border-dashed mt-10 max-w-full w-map"></div>

            <div class="bg-gray-100 p-6 rounded shadow-lg max-w-full w-map -mt-7 dark:bg-gray-300">
                <LineChart
                    :data="[distance.data]"
                    :data-labels="distance.labels"
                    :labels="['']"
                    :colors="['235, 54, 54']"
                    :title="t('weapon_damage.distance_damage')"
                    :weaponChart="{x: t('weapon_damage.distance'), y: t('weapon_damage.damage')}"
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
        },
        distance: {
            type: Object,
            required: true,
        }
    },
}
</script>

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
                    :data="[bans.data, warnings.data, notes.data]"
                    :data-labels="bans.labels"
                    :labels="[t('statistics.bans'), t('statistics.warnings'), t('statistics.notes')]"
                    :colors="['235, 54, 54', '235, 145, 55', '255, 235, 55']"
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
                <LineChart
                    :data="[creations.data, deletions.data]"
                    :data-labels="creations.labels"
                    :labels="[t('statistics.creations'), t('statistics.deletions')]"
                    :colors="['87, 235, 54', '235, 54, 54']"
                    :title="t('statistics.titles.character')"
                    class="w-full"
                ></LineChart>
            </div>

            <div class="pt-10 border-gray-500 border-t-2 border-dashed mt-10 max-w-full w-map"></div>

            <div class="bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <FinancialChart
                    :data="[economy.data]"
                    :data-labels="economy.labels"
                    :is-casino-chart="true"
                    :title="t('statistics.titles.economy')"
                    class="w-full"
                ></FinancialChart>
            </div>

            <div class="pt-10 border-gray-500 border-t-2 border-dashed mt-10 max-w-full w-map"></div>

            <div class="bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <LineChart
                    :data="[blackjack.average_spent, blackjack.min_earned, blackjack.max_earned, blackjack.average_earned, blackjack.return_rate]"
                    :data-labels="blackjack.labels"
                    :labels="[t('statistics.avg_bet_placed'), t('statistics.min_money_return'), t('statistics.max_money_return'), t('statistics.avg_money_return'), t('statistics.return_rate')]"
                    :colors="['55, 55, 235', '87, 235, 54', '255, 230, 0', '255, 42, 0']"
                    :title="t('statistics.titles.blackjack')"
                    :is-casino-chart="true"
                    class="w-full"
                ></LineChart>

                <div class="flex">
                    <div class="text-xs mt-3 text-gray-800">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.best_players') }}</span>
                        <span v-for="(player, index) in blackjack.best_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.worst_players') }}</span>
                        <span v-for="(player, index) in blackjack.worst_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6" v-if="blackjack.my_place">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.your_spot') }}</span>
                        <span class="flex font-mono">
                            <span class="mr-1">{{ blackjack.my_place.place }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + blackjack.my_place.steam_identifier">{{ $page.auth.player.playerName }}</a>
                            <span :class="blackjack.my_place.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(blackjack.my_place.win, 0, true) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <LineChart
                    :data="[slots.average_spent, slots.min_earned, slots.max_earned, slots.average_earned, slots.return_rate]"
                    :data-labels="slots.labels"
                    :labels="[t('statistics.avg_bet_placed'), t('statistics.min_money_return'), t('statistics.max_money_return'), t('statistics.avg_money_return'), t('statistics.return_rate')]"
                    :colors="['55, 55, 235', '87, 235, 54', '255, 230, 0', '255, 42, 0']"
                    :title="t('statistics.titles.slots')"
                    :is-casino-chart="true"
                    class="w-full"
                ></LineChart>

                <div class="flex">
                    <div class="text-xs mt-3 text-gray-800">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.best_players') }}</span>
                        <span v-for="(player, index) in slots.best_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.worst_players') }}</span>
                        <span v-for="(player, index) in slots.worst_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6" v-if="slots.my_place">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.your_spot') }}</span>
                        <span class="flex font-mono">
                            <span class="mr-1">{{ slots.my_place.place }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + slots.my_place.steam_identifier">{{ $page.auth.player.playerName }}</a>
                            <span :class="slots.my_place.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(slots.my_place.win, 0, true) }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-7 bg-gray-100 p-6 rounded shadow-lg max-w-full w-map dark:bg-gray-300">
                <LineChart
                    :data="[tracks.average_spent, tracks.min_earned, tracks.max_earned, tracks.average_earned, tracks.return_rate]"
                    :data-labels="tracks.labels"
                    :labels="[t('statistics.avg_bet_placed'), t('statistics.min_money_return'), t('statistics.max_money_return'), t('statistics.avg_money_return'), t('statistics.return_rate')]"
                    :colors="['55, 55, 235', '87, 235, 54', '255, 230, 0', '255, 42, 0']"
                    :title="t('statistics.titles.tracks')"
                    :is-casino-chart="true"
                    class="w-full"
                ></LineChart>

                <div class="flex">
                    <div class="text-xs mt-3 text-gray-800">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.best_players') }}</span>
                        <span v-for="(player, index) in tracks.best_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.worst_players') }}</span>
                        <span v-for="(player, index) in tracks.worst_players" :key="index" class="flex font-mono">
                            <span class="mr-1">{{ index + 1 }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + player.steam_identifier">{{ player.player_name || player.steam_identifier }}</a>
                            <span :class="player.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(player.win, 0, true) }}</span>
                        </span>
                    </div>

                    <div class="text-xs mt-3 text-gray-800 ml-6" v-if="tracks.my_place">
                        <span class="text-sm font-semibold mb-1 block">{{ t('statistics.your_spot') }}</span>
                        <span class="flex font-mono">
                            <span class="mr-1">{{ tracks.my_place.place }}.</span>
                            <a class="text-blue-700 mr-1 inline-block w-xs-steam overflow-hidden overflow-ellipsis" :href="'/players/' + tracks.my_place.steam_identifier">{{ $page.auth.player.playerName }}</a>
                            <span :class="tracks.my_place.win > 0 ? 'text-green-600' : 'text-red-600'">{{ numberFormat(tracks.my_place.win, 0, true) }}</span>
                        </span>
                    </div>
                </div>
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
        notes: {
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

        blackjack: {
            type: Object,
            required: true,
        },
        tracks: {
            type: Object,
            required: true,
        },
        slots: {
            type: Object,
            required: true,
        },
    },
}
</script>

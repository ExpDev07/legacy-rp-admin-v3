<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("home.title") }}
            </h1>
            <p>
                {{ t("home.welcome", $page.auth.user.name) }}
            </p>

            <div class="p-4 pl-6 italic border-l-4 border-gray-300 inline-block bg-gray-100 shadow-lg dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100">
                <span class="mb-1 block" v-html="quote.quote">
                    {{ quote.quote }}
                </span>
                <span class="text-xs">
                    - {{ quote.author }}
                </span>
            </div>

            <div class="flex mt-14">
                <div class="p-4 bg-gray-100 shadow-lg dark:bg-gray-700 dark:text-gray-100 flex justify-between">
                    <vue-circle ref="serverCount"
                        :progress="playerCountPercentage()"
                        :size="70"
                        line-cap="square"
                        :fill="{ color: '#5a56c2' }"
                        empty-fill="rgba(0, 0, 0, .1)"
                        :animation-start-value="0.0"
                        :start-angle="1.57079633"
                        insert-mode="append"
                        :thickness="7"
                        :show-percent="false">
                        <p class="text-sm font-semibold">{{ joinedPlayers }}</p>
                    </vue-circle>
                    <p class="ml-3" v-html="playerCount">{{ playerCount }}</p>
                </div>
            </div>
        </portal>

    </div>
</template>

<script>
import Layout from './../Layouts/App';
import VueCircle from 'vue2-circle-progress'

export default {
    layout: Layout,
    components: {
        VueCircle
    },
    data() {
        return {
            playerCount: 'N/A',
            totalPlayers: 1,
            joinedPlayers: 1,
            queuePlayers: 1,
            serverCount: 1
        };
    },
    methods: {
        localizePlayerCount() {
            return this.t('home.player_count', this.joinedPlayers, this.totalPlayers, this.serverCount, this.queuePlayers);
        },
        playerCountPercentage() {
            const percentage = Math.floor(100 * (this.joinedPlayers / this.totalPlayers));

            return percentage > 100 ? 100 : percentage;
        },
        refresh: async function () {
            try {
                const data = await axios.get('/api/players');

                if (data.data) {
                    this.totalPlayers = data.data.totalPlayers;
                    this.joinedPlayers = data.data.joinedPlayers;
                    this.queuePlayers = data.data.queuePlayers;
                    this.serverCount = data.data.serverCount;

                    this.playerCount = this.localizePlayerCount()

                    this.$refs.serverCount.updateProgress(this.playerCountPercentage());
                }
            } catch(e) {}
        },
    },
    mounted() {
        const _this = this;

        this.$nextTick(function () {
            _this.refresh();

            setInterval(function() {
                _this.refresh();
            }, 30 * 1000);
        });
    },
    props: {
        quote: {
            type: Object,
            required: true,
        },
    }
}
</script>

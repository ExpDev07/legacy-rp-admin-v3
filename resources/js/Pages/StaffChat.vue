<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("staff_chat.title") }}
            </h1>
        </portal>

        <div class="flex -mt-6 flex-wrap flex-row">
            <badge
                class="border-gray-200 bg-secondary dark:bg-dark-secondary mb-2"
                v-if="isLoading"
            >
                {{ t('staff_chat.connecting') }}
            </badge>

            <badge
                class="border-gray-200 bg-secondary dark:bg-dark-secondary mb-2"
                v-else-if="socketError"
            >
                {{ t('staff_chat.failed') }}
            </badge>

            <badge
                class="border-gray-200 bg-secondary dark:bg-dark-secondary mb-2"
                v-else-if="staffMessages.length === 0"
            >
                {{ t('staff_chat.no_messages') }}
            </badge>

            <div
                class="w-full mb-3"
                v-for="(message, index) in staffMessages"
                :key="index"
                v-else
            >
                <div
                    :title="formatTimestamp(message.createdAt * 1000)"
                    class="badge border-gray-200 bg-secondary dark:bg-dark-secondary inline-block px-4 leading-5 py-2 border-2 rounded" :class="{ 'inline-block' : small }" @click="click && click($event);"
                >
                    <span class="font-semibold">{{ message.user.playerName }}:</span> {{ message.message }}
                </div>
            </div>

        </div>

    </div>
</template>

<script>
import Layout from "../Layouts/App";
import Badge from "../Components/Badge";
import DataCompressor from "./Map/DataCompressor";

export default {
    layout: Layout,
    components: {
        Badge
    },
    data() {
        return {
            isLoading: false,
            isInitialized: false,
            socketError: false,
            staffMessages: []
        };
    },
    methods: {
        formatTimestamp(time) {
            return this.$options.filters.formatTime(time);
        },
        initChat() {
            if (this.isInitialized) {
                return;
            }
            this.isInitialized = true;
            this.isLoading = true;

            const isDev = window.location.hostname === 'localhost';

            const token = this.$page.auth.token,
                cluster = this.$page.auth.cluster,
                server = this.$page.auth.server,
                socketUrl = isDev ? 'ws://localhost:9999' : 'wss://map.legacy-roleplay.com',
                steam = this.$page.auth.player.steamIdentifier;

            let socket = new WebSocket(socketUrl + "/staff-chat?token=" + token + "&cluster=" + cluster + "&server=" + server + "&steam=" + steam);

            socket.onmessage = async (event) => {
                this.isLoading = false;

                try {
                    const unzipped = await DataCompressor.GUnZIP(event.data);

                    this.staffMessages = JSON.parse(unzipped).reverse();
                } catch (e) {
                    console.error('Failed to parse socket message ', e)
                }
            };

            socket.onclose = () => {
                this.isLoading = false;
                this.isInitialized = false;

                this.socketError = true;

                setTimeout(() => {
                    this.initChat();
                }, 5000);
            };
        }
    },
    mounted() {
        const _this = this;

        this.$nextTick(function () {
            _this.initChat();
        });
    },
    props: {}
}
</script>

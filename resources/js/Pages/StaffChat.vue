<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("staff_chat.title") }}
            </h1>
        </portal>

        <div class="-mt-6">
            <div class="flex flex-wrap flex-row">
                <form class="mb-6 flex" @submit.prevent="sendChat">
                    <input class="w-80 px-4 py-2 mr-1 bg-gray-200 dark:bg-gray-600 border rounded" maxlength="250" required placeholder="Hey gang!" v-model="staffMessage">

                    <button class="px-4 py-2 font-semibold text-white bg-success dark:bg-dark-success rounded hover:shadow-lg" type="submit">
                        <span v-if="!isSendingChat">
                            <i class="fas fa-envelope"></i>
                            {{ t('staff_chat.send_chat') }}
                        </span>
                        <span v-else>
                            <i class="fas fa-cog animate-spin"></i>
                            {{ t('global.loading') }}
                        </span>
                    </button>
                </form>

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
                    <!-- Report Message -->
                    <div
                        :title="formatTimestamp(message.createdAt * 1000)"
                        class="badge border-green-200 bg-green-100 dark:bg-green-900 inline-block px-4 leading-5 py-2 border-2 rounded"
                        v-if="message.type === 'report'"
                    >
                        <a :href="'/players/' + message.user.steamIdentifier" class="font-semibold text-black dark:text-white !no-underline">{{ message.user.reporterName }}:</a> {{ message.message }}
                    </div>

                    <!-- Staff Chat Message -->
                    <div
                        :title="formatTimestamp(message.createdAt * 1000)"
                        class="badge border-purple-200 bg-purple-100 dark:bg-purple-900 inline-block px-4 leading-5 py-2 border-2 rounded"
                        v-else
                    >
                        <a :href="'/players/' + message.user.steamIdentifier" class="font-semibold text-black dark:text-white !no-underline">{{ message.user.playerName }}:</a> {{ message.message }}
                    </div>
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
            staffMessages: [],
            staffMessage: "",
            isSendingChat: false
        };
    },
    methods: {
        formatTimestamp(time) {
            return this.$options.filters.formatTime(time);
        },
        async sendChat() {
            if (this.isSendingChat) {
                return;
            }
            this.isSendingChat = true;

            // Send request.
            await this.$inertia.post('/staffChat', {
                message: this.staffMessage
            });

            // Reset.
            this.isSendingChat = false;
            this.staffMessage = "";
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

<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("staff_chat.title") }}
            </h1>
        </portal>

        <div class="-mt-12">
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

                <div class="w-full mb-3" v-if="isLoading">
                    <div
                        class="badge border-blue-200 bg-blue-100 dark:bg-blue-900 inline-block px-4 leading-5 py-2 border-2 rounded"
                    >
                        <i class="fas fa-cog animate-spin mr-1"></i>
                        {{ t('staff_chat.connecting') }}
                    </div>
                </div>

                <div class="w-full mb-3" v-if="socketError">
                    <div
                        class="badge border-red-200 bg-red-100 dark:bg-red-900 inline-block px-4 leading-5 py-2 border-2 rounded"
                    >
                        {{ t('staff_chat.failed') }}
                    </div>
                </div>

                <div class="w-full mb-3" v-if="!socketError && !isLoading && staffMessages.length === 0">
                    <div
                        class="badge border-yellow-200 bg-yellow-100 dark:bg-yellow-900 inline-block px-4 leading-5 py-2 border-2 rounded"
                    >
                        {{ t('staff_chat.no_messages') }}
                    </div>
                </div>

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
                        <a :href="'/players/' + message.user.steamIdentifier" class="font-semibold text-black dark:text-white !no-underline">{{ message.user.playerName }}:</a> {{ message.message }}
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
import Layout from "../Layouts/Plain";
import Badge from "../Components/Badge";
import DataCompressor from "./Map/DataCompressor";

import {io} from "socket.io-client";

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

            this.socketError = false;

            const isDev = window.location.hostname === 'localhost';

            const token = this.$page.auth.token,
                server = this.$page.auth.server,
                socketUrl = isDev ? 'ws://localhost:9999' : 'wss://' + window.location.host;

            let socket = io(socketUrl, {
                reconnectionDelayMax: 5000,
                query: {
                    server: server,
                    token: token,
                    type: "staff",
                    steam: this.$page.auth.player.steamIdentifier
                }
            });

            socket.on("message", async (buffer) => {
                this.isLoading = false;

                try {
                    const unzipped = await DataCompressor.GUnZIP(buffer);

                    this.staffMessages = JSON.parse(unzipped).reverse();
                } catch (e) {
                    console.error('Failed to parse socket message ', e)
                }
            });

            socket.on("disconnect", () => {
                this.isLoading = false;
                this.isInitialized = false;

                socket.close();

                this.socketError = true;

                setTimeout(() => {
                    this.initChat();
                }, 3000);
            });
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

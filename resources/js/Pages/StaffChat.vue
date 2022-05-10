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
                :title="this.$options.filters.formatTime(message.createdAt * 1000)"
                v-for="(message, index) in staffMessages"
                :key="index"
                v-else
            >
                {{ message.user.playerName }}: {{ message.message }}
            </badge>
        </div>

    </div>
</template>

<script>
import Badge from "../Components/Badge";

export default {
    components: {
        Badge,
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
        this.initChat();
    }
}
</script>

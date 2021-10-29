<template>
    <div class="fixed bottom-0 right-0 p-2 h-9 overflow-hidden text-black bg-yellow-400 w-full max-w-xs z-2k" id="staff_chat">
        <a href="#" @click="toggleChat($event)" class="block text-center text-sm font-semibold">
            <i class="fas fa-comments mr-1"></i>
            {{ t('staff_chat.toggle') }}
        </a>
        <div class="flex flex-wrap pt-2 text-sm max-h-72 overflow-y-auto hidden" id="staff_chat-chat">
            <div class="font-semibold text-center w-full" v-if="isLoading">{{ t('staff_chat.connecting') }}</div>

            <div class="font-semibold text-center w-full" v-else-if="socketError">{{ t('staff_chat.failed') }}</div>

            <div class="border-t border-yellow-700 pt-2 mt-2 w-full border-dashed" v-for="(message, index) in staffMessages" :key="index" v-else>
                <a class="font-semibold" :href="'/players/' + message.user.steamIdentifier" target="_blank">
                    {{ message.user.playerName }}
                    <sup class="italic">{{ message.createdAt * 1000 | formatTime(true) }}</sup>
                </a>
                <div class="italic break-words pt-1 text-xs">
                    {{ message.message }}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DataCompressor from "../Pages/Map/DataCompressor";

export default {
    name: 'StaffChat',
    data() {
        return {
            isLoading: false,
            isInitialized: false,
            socketError: false,
            staffMessages: []
        };
    },
    methods: {
        toggleChat(e) {
            e.preventDefault();

            $('#staff_chat').toggleClass('h-9');
            $('#staff_chat-chat').toggleClass('hidden');

            if (!$('#staff_chat').hasClass('h-9')) {
                this.initChat();
            }
        },
        initChat() {
            if (this.isInitialized) {
                return;
            }
            this.isInitialized = true;
            this.isLoading = true;

            const isDev = window.location.hostname === 'localhost',
                _this = this;

            const token = this.$page.auth.token,
                cluster = this.$page.auth.cluster,
                server = this.$page.auth.server,
                socketUrl = isDev ? 'ws://localhost:9999' : 'wss://map.legacy-roleplay.com',
                steam = this.$page.auth.player.steamIdentifier;

            let socket = new WebSocket(socketUrl + "/staff-chat?token=" + token + "&cluster=" + cluster + "&server=" + server + "&steam=" + steam);

            socket.onmessage = async function (event) {
                _this.isLoading = false;

                try {
                    const unzipped = await DataCompressor.GUnZIP(event.data);

                    _this.staffMessages = JSON.parse(unzipped).reverse();
                } catch (e) {
                    console.error('Failed to parse socket message ', e)
                }
            };

            socket.onclose = function () {
                _this.isLoading = false;

                _this.socketError = true;
            };
        }
    }
}
</script>

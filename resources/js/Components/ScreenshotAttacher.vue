<template>
    <div class="fixed bg-black bg-opacity-70 top-0 left-0 right-0 bottom-0 z-2k">
        <div class="max-h-max overflow-y-auto shadow-xl absolute bg-gray-100 dark:bg-gray-600 text-black dark:text-white left-2/4 top-2/4 -translate-x-2/4 -translate-y-2/4 transform p-4 rounded w-alert">
            <h3 class="mb-2">{{ t('screenshot.title') }}</h3>
            <div>
                <img :src="url" alt="Screenshot" class="block w-full mb-3" />

                <!-- Message -->
                <div>
                    <label class="italic font-semibold mb-1">
                        {{ t('screenshot.note') }}
                    </label>
                    <textarea class="block w-full p-5 bg-gray-200 dark:bg-gray-600 rounded shadow"
                              placeholder="Player is using a mod menu"
                              v-model="note" maxlength="500"></textarea>
                </div>
            </div>
            <div class="flex justify-end mt-2">
                <button type="button" class="px-5 py-2 mr-3 hover:shadow-xl font-semibold text-white rounded bg-dark-secondary mr-3 dark:text-black dark:bg-secondary" @click="close(false, null)">
                    {{ t('global.close') }}
                </button>
                <button class="px-5 py-2 font-semibold text-white bg-red-500 rounded hover:bg-red-600" @click="attach()">
                    <span v-if="!isAttaching">
                        <i class="fas fa-paper-plane mr-1"></i>
                        {{ t('screenshot.attach') }}
                    </span>
                    <span v-else>
                        <i class="fas fa-paper-plane animate-spin mr-1"></i>
                        {{ t('global.loading') }}
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        steam: {
            type: String,
            required: true,
        },
        url: {
            type: String,
            required: true,
        },
        close: {
            type: Function,
            required: true,
        },
    },
    data() {
        return {
            note: '',
            isAttaching: false
        };
    },
    methods: {
        async attach() {
            if (this.isAttaching) {
                return;
            }
            this.isAttaching = true;

            try {
                const result = await axios.post('/players/' + this.steam + '/attachScreenshot', {
                    url: this.url,
                    note: this.note
                });

                if (result.data && result.data.status) {
                    this.close(true, result.data.data);
                } else {
                    this.close(false, result.data.message);
                }
            } catch(e) {}
        }
    }
}
</script>

<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("backstories.title") }}
            </h1>
            <p>
                {{ t("backstories.description") }}
                <a @click="refresh($event)" href="#" class="text-indigo-600 !no-underline dark:text-indigo-300 hover:text-yellow-500 dark:hover:text-yellow-300">
                    <i class="ml-1 mr-1 fa fa-refresh"></i> {{ t('global.refresh') }}
                </a>
            </p>
        </portal>

        <div class="flex -mt-6 justify-between max-w-screen-2xl mobile:flex-wrap">
            <div class="p-4 max-w-xl pl-6 italic border-l-4 border-gray-300 inline-block bg-gray-100 shadow-lg dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100 mobile:w-full mobile:mb-3">
                <div v-if="isLoading">
                    <i class="mr-1 fa fa-refresh animate-spin font-normal text-xl"></i>
                </div>
                <div v-else-if="character">
                    <span class="mb-1 block text-sm">
                        {{ character.backstory }}
                    </span>
                    <inertia-link :href="'/players/' + character.steamIdentifier + '/characters/' + character.id + '/edit'" class="text-indigo-600 text-xs !no-underline dark:text-indigo-300 hover:text-yellow-500 dark:hover:text-yellow-300">
                        - {{ character.firstName }} {{ character.lastName }}
                        <i class="ml-1 fas fa-skull-crossbones text-red-700 dark:text-red-500 font-semibold" v-if="character.characterDeleted"></i>
                    </inertia-link>
                </div>
                <div v-else class="text-danger dark:text-dark-danger font-semibold">
                    {{ t('backstories.failed') }}
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Layout from '../../../Layouts/App';
import VueCircle from 'vue2-circle-progress';
import "chart.js";

export default {
    layout: Layout,
    components: {
        VueCircle
    },
    data() {
        return {
            isLoading: false,
            character: null
        };
    },
    methods: {
        refresh: async function (e) {
            if (e) {
                e.preventDefault();
            }

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;

            try {
                const data = await axios.get('/api/backstories');

                if (data.data && data.data.data) {
                    this.character = data.data.data;
                } else {
                    this.character = null;
                }
            } catch(e) {}

            this.isLoading = false;
        }
    },
    mounted() {
        this.refresh(null);
    }
}
</script>

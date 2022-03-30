<template>
    <div>

        <portal to="title">
            <h1 class="dark:text-white">
                {{ t("backstories.title") }}
            </h1>
            <p>
                {{ t("backstories.description") }}
                <a @click="refresh($event)" class="text-indigo-600 text-xs !no-underline dark:text-indigo-300 hover:text-yellow-500 dark:hover:text-yellow-300">
                    {{ t('global.refresh') }}
                </a>
            </p>
        </portal>

        <div class="flex -mt-6 justify-between max-w-screen-2xl mobile:flex-wrap">
            <div class="p-4 max-w-xl pl-6 italic border-l-4 border-gray-300 inline-block bg-gray-100 shadow-lg dark:border-gray-500 dark:bg-gray-700 dark:text-gray-100 mobile:w-full mobile:mb-3">
                <span class="mb-1 block text-sm">
                    {{ character.backstory }}
                </span>
                <inertia-link :href="'/players/' + character.steamIdentifier + '/characters/' + character.id + '/edit'" class="text-indigo-600 text-xs !no-underline dark:text-indigo-300 hover:text-yellow-500 dark:hover:text-yellow-300">
                    - {{ character.firstName }} {{ character.lastName }}
                </inertia-link>
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
            isLoading: false
        };
    },
    methods: {
        refresh: async function (e) {
            e.preventDefault();

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/backstories', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: [ 'character' ],
                });
            } catch(e) {}

            this.isLoading = false;
        }
    },
    props: {
        character: {
            type: Object,
            required: true,
        }
    }
}
</script>

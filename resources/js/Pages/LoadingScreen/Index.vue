<template>
    <div>
        <portal to="title">
            <h1 class="dark:text-white">
                {{ t('loading_screen.title') }}
            </h1>
            <p>
                {{ t('loading_screen.description') }}
            </p>
        </portal>

        <portal to="actions">
            <button class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded dark:bg-green-400 mr-1"
                type="button" @click="isAdding = true">
                <i class="mr-1 fa fa-plus"></i>
                {{ t('loading_screen.add') }}
            </button>

            <button class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 rounded dark:bg-indigo-400"
                type="button" @click="refresh">
                <span v-if="!isLoading">
                    <i class="mr-1 fa fa-refresh"></i>
                    {{ t('logs.refresh') }}
                </span>
                <span v-else>
                    <i class="fas fa-cog animate-spin"></i>
                    {{ t('global.loading') }}
                </span>
            </button>
        </portal>

        <template>
            <h2 class="mb-4 max-w-screen-md m-auto text-2xl">
                <span class="cursor-pointer" @click="urlOnly = !urlOnly">{{ t('loading_screen.pictures') }}</span>
            </h2>

            <div class="w-full flex flex-wrap max-w-screen-md m-auto">
                <div class="flex pt-3 pb-3 border-t w-full border-gray-400 dark:border-gray-500 px-2 relative hover:bg-gray-100 dark:hover:bg-gray-700" v-for="(picture, index) in pictures" :key="picture.id">
                    <div>
                        <a clas="block" target="_blank" :href="picture.image_url">
                            <span v-if="urlOnly">{{ picture.image_url }}</span>
                            <img :src="picture.image_url" class="h-48" v-else />
                        </a>
                    </div>

                    <inertia-link
                        class="block px-1.5 py-1 text-center text-white text-xs absolute top-1 right-1 bg-red-600 dark:bg-red-400 rounded"
                        href="#"
                        @click="deletePicture($event, picture.id)"
                        :title="t('loading_screen.remove')"
                    >
                        <i class="fas fa-trash-alt"></i>
                    </inertia-link>
                </div>

                <img v-if="pictures.length === 0" src="/images/no-pictures.png" class="w-full" />
            </div>
        </template>

        <modal :show.sync="isAdding">
            <template #header>
                <h1 class="dark:text-white">
                    {{ t('loading_screen.add') }}
                </h1>
            </template>

            <template #default>
                <div>
                    <label class="block mb-3" for="url">
                        {{ t('loading_screen.picture') }}
                    </label>
                    <input class="block w-full px-4 py-3 mb-3 bg-gray-200 border rounded dark:bg-gray-600" type="url" id="url" placeholder="https://images.unsplash.com/photo-1511044568932-338cba0ad803" v-model="image_url" required>
                </div>
            </template>

            <template #actions>
                <button type="button" class="px-5 py-2 rounded hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-400" @click="isAdding = false">
                    {{ t('global.cancel') }}
                </button>
                <button type="submit" class="px-5 py-2 text-white bg-indigo-600 rounded dark:bg-indigo-400" @click="handleAdd">
                    <span v-if="!isLoading">
                        <i class="mr-1 fa fa-plus"></i>
                        {{ t('loading_screen.do_add') }}
                    </span>
                    <span v-else>
                        <i class="fas fa-cog animate-spin"></i>
                        {{ t('global.loading') }}
                    </span>
                </button>
            </template>
        </modal>
    </div>
</template>

<script>
import Layout from './../../Layouts/App';
import VSection from './../../Components/Section';
import Modal from  '../../Components/Modal';

export default {
    layout: Layout,
    components: {
        VSection,
        Modal,
    },
    props: {
        pictures: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            isLoading: false,
            isAdding: false,

            urlOnly: false,

            image_url: '',
        };
    },
    methods: {
        async deletePicture(e, id) {
            e.preventDefault();

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.delete('/loading_screen/' + id);
            } catch (e) { }

            this.isLoading = false;
        },
        async handleAdd() {
            const url = this.image_url.trim();

            if (!url || !url.startsWith("https://")) {
                alert("Please enter a valid URL");

                return;
            }

            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.post('/loading_screen', {
                    image_url: url,
                });
            } catch (e) { }

            this.isLoading = false;
            this.isAdding = false;
        },
        async refresh() {
            if (this.isLoading) {
                return;
            }

            this.isLoading = true;
            try {
                await this.$inertia.replace('/loading_screen', {
                    data: this.filters,
                    preserveState: true,
                    preserveScroll: true,
                    only: ['pictures'],
                });
            } catch (e) { }

            this.isLoading = false;
        },
    },
}
</script>
